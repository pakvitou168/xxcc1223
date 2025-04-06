<?php

namespace App\Services\Claim;

use App\Models\Claim\AdjusterCompany;
use App\Models\Claim\CauseOfLoss;
use App\Models\Claim\Register\Claim;
use App\Models\Claim\Register\ClaimDetail;
use App\Models\Claim\Register\ClaimRegisterPrintV;
use App\Models\Claim\Register\ClaimRegisterReinsurancePrintV;
use App\Models\Claim\Register\ClaimVehicleListV;
use App\Models\Claim\DriverLicense;
use App\Models\Claim\Register\ClaimPolicyListV;
use App\Models\Claim\Register\ListClaimV;
use App\Models\Claim\ThirdParty;
use App\Models\Cover\Cover;
use App\Models\Deductible\DeductibleDetail;
use App\Models\Insurance\Auto;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\BasePolicy;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterService
{
  public function listClaimablePolicies()
  {
    return ClaimPolicyListV::select('document_no AS label', 'document_no AS value','data_id')->get();
  }

  public function listVehiclesByPolicyDocNo($policyDocNo)
  {
    return ClaimVehicleListV::select('detail_id', 'plate_no', 'insured_name')
      ->where('document_no', $policyDocNo)
      ->get()
      ->map(function ($item) {
        return [
          'detail_id' => $item->detail_id,
          'plate_no' => $item->plate_no ?? '-',
          'insured_name' => $item->insured_name,
        ];
      });
  }


  public function listCovers($detailId)
  {
    $vehicleCovers = ClaimVehicleListV::select('product_code', 'selected_cover_pkg')
      ->where('detail_id', $detailId)
      ->first();

    $coverCodesArr = explode(',', optional($vehicleCovers)->selected_cover_pkg);

    return Cover::select('code', 'name')
      ->whereIn('code', $coverCodesArr)
      ->where('product_code', optional($vehicleCovers)->product_code)
      ->where('type', 'C')
      ->where('status', 'ACT')
      ->orderBy('seq')
      ->get()
      ->map(function ($item) {
        return [
          ...$item->toArray(),
          'type' => 'COVER',
        ];
      });
  }

  public function listCauseOfLosses()
  {
    return CauseOfLoss::select('code', 'cause_name')
      ->get();
  }

  public function listThirdParties()
  {
    return ThirdParty::select('id', 'vehicle_make', 'vehicle_model', 'plate_no')
      ->orderBy('vehicle_make')
      ->get()
      ->map(function ($item) {
        return [
          'label' => $item->vehicle_make . ' ' . $item->vehicle_model . ' (' . $item->plate_no . ')',
          'value' => $item->id,
        ];
      });
  }

  public function listDrivers()
  {
    return DriverLicense::select('id', 'name')
      ->orderBy('name')
      ->get()
      ->map(function ($item) {
        return [
          'label' => $item->name,
          'value' => $item->id,
        ];
      });
  }

  public function listAdjusterCompanies()
  {
    return AdjusterCompany::select('id', 'name_en')
      ->orderBy('name_en')
      ->get()
      ->map(function ($item) {
        return [
          'label' => $item->name_en,
          'value' => $item->id,
        ];
      });
  }

  public function getRequestData(array $requestData): array
  {
    $claimablePolicy = ClaimPolicyListV::whereDocumentNo($requestData['policy_no'])->firstOr(fn() => throw new Exception("Policy not found"));
    $policy = BasePolicy::whereDataId($claimablePolicy->data_id)->firstOr(fn() => throw new Exception("Policy not found"));
    $autoMaster = Auto::select('effective_date_from', 'effective_date_to')
      ->where('status', 'ACT')
      ->find($policy->data_id);


    return [
      ...$requestData,
      'policy_id' => $policy->id,
      'data_id' => $policy->data_id,
      'insured_period_from' => $autoMaster->effective_date_from,
      'insured_period_to' => $autoMaster->effective_date_to,
    ];
  }

  public function save(Claim $model, array $data): JsonResponse
  {

    DB::beginTransaction();

    try {
      $claimId = $this->saveClaim($model, $data);

      if ($claimId) {
        $savedDetail = $this->saveClaimDetail($claimId, $data);
        //update deductible
        if ($data['deductibles']) {
          foreach ($data['deductibles'] as $deductible) {
            $oldDeductible = DeductibleDetail::find($deductible['id']);
            $this->updateDeductible($deductible, $oldDeductible);
          }
        }
        if ($savedDetail) {
          DB::commit();

          return response()->json(['message' => 'success'], 200);
        }
      }
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('Save Claim Register Error: ' . $e->getMessage());
      return response()->json(['message' => $e->getMessage()], 500);
    }
  }

  public function saveClaim(Claim $model, array $data): int
  {

    $data = (object) $data;

    // New data
    $claimNumberData = null;
    if (!$model->id) {
      $claimNumberData = $this->getClaimNo($data->policy_no,$data->detail_id);
    }

    $model->policy_id = $data->policy_id;
    $model->data_id = $data->data_id;
    $model->detail_id = $data->detail_id;
    $model->third_party_id = $data->third_party_id;

    if ($claimNumberData) {
      $model->seq_no = optional($claimNumberData)->seq;
      $model->claim_no = optional($claimNumberData)->generate_no;
    }
    $vehicle = AutoDetail::findOr($data->detail_id, fn() => throw new Exception("Vehicle not found"));
    $model->notification_date = $data->notification_date;
    $model->incident_date = $data->incident_date;
    $model->incident_location = $data->incident_location;
    $model->insured_period_from = $data->insured_period_from;
    $model->insured_period_to = $data->insured_period_to;
    $model->driver_id = $data->driver_id;
    $model->adjuster_company_id = optional($data)->adjuster_company_id;
    $model->remark = optional($data)->remark;
    $model->vehicle_uuid = $vehicle->vehicle_uuid;
    if ($model->save())
      return $model->id;
  }

  public function saveClaimDetail(int $claimId, array $data): bool
  {

    $data = (object) $data;

    $claim = Claim::find($claimId);

    $saved = false;

    $details = collect($data->cause_of_losses)->map(function ($item) use ($claim) {
      $item = (object) $item;

      $detailData = [
        'policy_id' => $claim->policy_id,
        'data_id' => $claim->data_id,
        'detail_id' => $claim->detail_id,
        'claim_no' => $claim->claim_no,
        'cause_of_loss_desc' => $item->name,
        'cause_of_loss_code' => $item->code,
        'type' => $item->type,
        'amount' => $item->value,
        'claim_id' => $claim->id,
        'recovery_from_third_party' => optional($item)->recovery_from_third_party ?? 0,
        'vehicle_uuid' => $claim->vehicle_uuid
      ];

      $saved = ClaimDetail::updateOrCreate(['id' => optional($item)->id], $detailData);

      if ($saved->wasRecentlyCreated) {
        $item->id = $saved->id;
      }

      return $item;
    });

    $existingDetailIds = ListClaimV::detail()->where('claim_no', $claim->claim_no)->pluck('id');
    $requestDetailsIds = collect($details)->pluck('id');

    $deleteDetailIds = $existingDetailIds->diff($requestDetailsIds);

    // Delete removed details
    ClaimDetail::whereIn('id', $deleteDetailIds)->update(['status' => 'DEL']);

    $saved = true;

    return $saved;
  }

  public function getClaimNo(string $documentNo,int $detailId): object
  {
    try {
      $params = [
        $documentNo,
        '',
        $detailId,
        '',
        'CLAIM',
        auth()->id(),
      ];

      info('Generating Claim No: ' . $documentNo);
      $generated = DB::select("select * from ins_generate_claim_or_payment_no(?,?,?,?,?,?)", $params);

      if (optional($generated[0])->code === 'SUC') {
        info('Generated Claim No: ' . $documentNo);
        return $generated[0];
      }
    } catch (Exception $e) {
      Log::error('Generating Claim No Error:' . $e->getMessage());
    }
  }

  public function getData(int $id)
  {
    $claim = ListClaimV::claim()
      ->select(
        'policy_id',
        'detail_id',
        'claim_no',
        'third_party_id',
        'notification_date',
        'incident_date',
        'incident_location',
        'driver_id',
        'adjuster_company_id',
        'remark',
      )
      ->findOr($id, fn() => abort(404, 'Not found.'));


    $policy = BasePolicy::find($claim->policy_id);
    $selectedPolicy = new \stdClass;
    $selectedPolicy->document_no = $policy->document_no;
    $selectedPolicy->data_id = $policy->data_id;
    $claim->selected_policy = $selectedPolicy;

    $causeOfLosses = ListClaimV::detail()
      ->select('id', 'cause_of_loss_desc', 'cause_of_loss_code', 'amount', 'type', 'recovery_from_third_party')
      ->where('claim_no', $claim->claim_no)
      ->orderBy('id')
      ->get()
      ->map(function ($item) {
        return [
          'id' => $item->id,
          'code' => $item->cause_of_loss_code,
          'name' => $item->cause_of_loss_desc,
          'value' => $item->amount,
          'type' => $item->type,
          'recovery_from_third_party' => $item->recovery_from_third_party,
        ];
      });
    $deductible = $this->getDeductibleDetail($claim->detail_id);

    return [
      ...$claim->toArray(),
      'policy_no' => $policy->document_no,
      'data_id' => $policy->data_id,
      'cause_of_losses' => $causeOfLosses,
      'deductibles' => $deductible
    ];
  }

  public function approve($claimId, $claimNo, $detailId, $approveType, $approveStatus, $comment, $userId): JsonResponse
  {
    try {
      $params = [
        $claimId,
        $claimNo,
        $detailId,
        'CLAIM',
        $approveType,
        $approveStatus,
        $comment,
        $userId,
      ];

      info('Approving claim: ' . $claimNo);
      $approved = DB::select("select * from ins_claim_approve_or_reject(?,?,?,?,?,?,?,?)", $params);

      if (optional($approved[0])->code === 'SUC') {
        info('Successfully approved claim: ' . $claimNo);
        return response()->json($approved[0], 200);
      }

      return response()->json($approved[0], 400);
    } catch (Exception $e) {
      Log::error('Approving claim: ' . $claimNo . ' :' . $e->getMessage());
      return response()->json(['message' => $e->getMessage()], $e->getCode());
    }
  }
  // public function printDetail(int $claimId) {
  //   $claim = ClaimRegisterPrintV::claim()->findOr($claimId, fn() => abort(404, 'Not found.'));
  //   $claimDetails = ClaimRegisterPrintV::detail()
  //     ->select('cause_of_loss_code', 'cause_of_loss_desc','cause_of_loss_desc_kh', 'amount', 'recovery_from_third_party')
  //     ->where('claim_no', $claim->claim_no)
  //     ->orderBy('id')
  //     ->get();
  //   $claimEstimation = $this->getClaimEstimation($claim->claim_no);
  //   $reinsuranceDetail = ClaimRegisterReinsurancePrintV::where('policy_id', $claim->policy_id)
  //     ->where('data_id', $claim->data_id)
  //     ->where('detail_id', $claim->detail_id)
  //     ->where('claim_id', $claim->claim_id)
  //     ->get();
  //   $totalShare = ClaimRegisterReinsurancePrintV::where('policy_id', $claim->policy_id)
  //     ->where('data_id', $claim->data_id)
  //     ->where('detail_id', $claim->detail_id)
  //     ->where('claim_id', $claim->claim_id)
  //     ->sum('share');
  //   $totalSharePercentage = $totalShare ? round($totalShare * 100, 7) : 0;
  //   $approvalDetail = $this->getApprovalDetail($claimId);

  //   return [
  //     'claim' => $claim,
  //     'claim_details' => $claimDetails,
  //     'claim_estimation' => $claimEstimation,
  //     'reinsurance_details' => $reinsuranceDetail,
  //     'total_share_percentage' => $totalSharePercentage,
  //     ...$approvalDetail,
  //   ];
  // }

  public function printDetail(int $claimId)
  {
    $claim = ClaimRegisterPrintV::claim()->findOr($claimId, fn() => abort(404, 'Not found.'));

    $claimDetails = ClaimRegisterPrintV::detail()
      ->select('cause_of_loss_code', 'cause_of_loss_desc', 'cause_of_loss_desc_kh', 'amount', 'recovery_from_third_party')
      ->where('claim_no', $claim->claim_no)
      ->orderBy('id')
      ->get();

    // Recovery from Third Party only exists in Own Damage cause of loss
    $recoveryFromThirdParty = $claimDetails
      ->where('cause_of_loss_code', 'OD')
      ->value('recovery_from_third_party');
    $overallEstimateAmount = $claimDetails->sum('amount');
    $claimEstimation = $overallEstimateAmount - $recoveryFromThirdParty;

    $reinsuranceDetail = ClaimRegisterReinsurancePrintV::where('policy_id', $claim->policy_id)
      ->where('data_id', $claim->data_id)
      ->where('detail_id', $claim->detail_id)
      ->where('claim_id', $claim->claim_id)
      ->get();
    $totalShare = $reinsuranceDetail->sum('share');
    $approvalDetail = $this->getApprovalDetail($claimId);

    return [
      'claim' => $claim,
      'claim_details' => $claimDetails,
      'claim_estimation' => $claimEstimation,
      'reinsurance_details' => $reinsuranceDetail,
      'total_share_percentage' => $totalShare,
      ...$approvalDetail,
    ];
  }

  private function getClaimEstimation(string $claimNo)
  {
    // Recovery from Third Party only exists in Own Damage cause of loss
    $recoveryFromThirdParty = ClaimRegisterPrintV::detail()
      ->where('claim_no', $claimNo)
      ->where('cause_of_loss_code', 'OD')
      ->value('recovery_from_third_party');

    $overallEstimateAmount = ClaimRegisterPrintV::detail()
      ->where('claim_no', $claimNo)
      ->sum('amount');

    return $overallEstimateAmount - $recoveryFromThirdParty;
  }

  public function getApprovalDetail(int $claimId)
  {
    $approvalDetail = Claim::select(
      'updated_by',
      'updated_at',
      'approved_status',
      'approved_at',
      'approved_by',
      'created_at',
      'created_by'
    )->find($claimId);

    $prepareById = $approvalDetail->updated_by ?? $approvalDetail->created_by;
    $prepareDate = $approvalDetail->updated_at ?? $approvalDetail->created_at;
    return [
      'approved_status' => $approvalDetail->approved_status,
      'updated_at' => $prepareDate,
      'updated_by_name' => User::where('id', $prepareById)->value('full_name'),
      'approved_at' => $approvalDetail->approved_at,
      'approved_by_name' => User::where('id', $approvalDetail->approved_by)->value('full_name'),
    ];

  }

  public function delete(Claim $model): JsonResponse
  {
    DB::beginTransaction();

    try {
      $model->status = "DEL";

      if ($model->save()) {
        $deletedDetail = $this->deleteDetail($model->id);

        if ($deletedDetail) {
          DB::commit();

          return response()->json(['message' => 'success'], 200);
        }
      }
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Delete Claim Register Error: ' . $e->getMessage());
      return response()->json(['message' => $e->getMessage()], 500);
    }
  }

  public function deleteDetail(int $claimId): bool
  {
    $saved = false;

    ClaimDetail::where('claim_id', $claimId)->update(['status' => 'DEL']);

    $saved = true;

    return $saved;
  }

  private function updateDeductible($deductible, DeductibleDetail $deductibleDetail)
  {
    $deductibleDetail->cond_value = $deductible['cond_value'];
    $deductibleDetail->min_value = $deductible['min_value'];
    $deductibleDetail->value = $this->getUpdatedDeductibleValue(
      $deductibleDetail['value_label'],
      $deductible['cond_value'],
      $deductible['min_value'] ?: null,
      $deductibleDetail['cond_value_type']
    );
    $deductibleDetail->save();
  }

  private function getUpdatedDeductibleValue($label, $value, $minValue, $valueType)
  {
    if ($valueType === 'AMOUNT') {
      // Get only first word as label if value type is AMOUNT
      return explode(' ', $label)[0] . ' ' . number_format($value, 2);
    }
    if ($valueType === 'PERCENTAGE')
      return $value . $label . ($minValue > 0 ? number_format($minValue, 2) : '');
    Log::error('Register update deductible: Wrong value type');
    return '';
  }

  public function getDeductibleDetail($autoDetailId)
  {
    return DeductibleDetail::select('id', 'product_code', 'detail_id', 'comp_code', 'value', 'cond_value', 'min_value', 'cond_value_type', 'value_label')
      ->where('detail_id', $autoDetailId)
      ->get()
      ->sortBy('cover.seq')
      ->values()
      ->map(function ($item) {
        $item->deductible_label = optional($item->cover)->deductible_label;
        return $item;
      });
  }
}

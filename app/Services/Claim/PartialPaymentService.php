<?php

namespace App\Services\Claim;

use App\Models\Claim\PartialPayment\ClaimPayment;
use App\Models\Claim\PartialPayment\ClaimPaymentDetail;
use App\Models\Claim\PartialPayment\ListClaimPaymentV;
use App\Models\Claim\Payee;
use App\Models\Claim\Process\ClaimPaymentPrintV;
use App\Models\Claim\Process\ClaimPaymentReinsurancePrintV;
use App\Models\Claim\Register\Claim;
use App\Models\Claim\Register\ClaimDetail;
use App\Models\Claim\Register\ClaimVehicleListV;
use App\Models\Claim\Register\ListClaimV;
use App\Models\Insurance\Auto;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartialPaymentService
{

  public function save(ClaimPayment $model, array $data): JsonResponse
  {
    DB::beginTransaction();

    try {
      $claimPaymentId = $this->saveClaimPayment($model, $data);

      if ($claimPaymentId) {
        $savedDetail = $this->saveClaimPaymentDetail($claimPaymentId, $data);

        if ($savedDetail) {

          $claimPayment = ClaimPayment::find($claimPaymentId);
          $totalAmount = ClaimPaymentDetail::where('payment_id', $claimPaymentId)->sum('amount');

          if ($this->saveClaimPaymentTotalAmount($claimPayment, $totalAmount)) {
            DB::commit();
            return response()->json(['message' => 'success'], 200);
          }
        }
      }
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Save Partial Payment Error: ' . $e->getMessage());
      return response()->json(['message' => $e->getMessage()], 500);
    }
  }

  public function saveClaimPayment(ClaimPayment $model, array $data): int
  {
    $data = (object) $data;

    $claim = Claim::where('claim_no', $data->claim_no)
      ->where('approved_status', 'APV')
      ->first();

    if (!$claim) abort(400, 'Claim Register is not available for partial payment');

    $model->policy_id = $claim->policy_id;
    $model->data_id = $claim->data_id;
    $model->detail_id = $claim->detail_id;
    $model->vehicle_uuid = $claim->vehicle_uuid;

    // New records, default total_amount
    if (!$model->id) {
      $model->total_amount = 0;
    }

    $model->claim_no = $claim->claim_no;

    if ($model->save()) return $model->id;
  }

  public function saveClaimPaymentDetail(int $claimPaymentId, array $data): bool
  {

    $data = (object) $data;

    $claimPayment = ClaimPayment::find($claimPaymentId);

    $saved = false;

    $details = collect($data->cause_of_losses)->map(function ($item) use ($claimPayment) {
      $item = (object) $item;

      $detailData = [
        'policy_id' => $claimPayment->policy_id,
        'data_id' => $claimPayment->data_id,
        'detail_id' => $claimPayment->detail_id,
        'claim_no' => $claimPayment->claim_no,
        'cause_of_loss_code' => $item->cause_of_loss_code,
        'amount' => $item->amount,
        'payee_id' => $item->payee_id,
        'payment_id' => $claimPayment->id,
        'payment_type' => $item->payment_type,
        'remark' => $item->remark ?? '',
        'vehicle_uuid' => $claimPayment->vehicle_uuid
      ];

      $savedRecord = ClaimPaymentDetail::updateOrCreate(['id' => optional($item)->id], $detailData);

      if ($savedRecord->wasRecentlyCreated) {
        $item->id = $savedRecord->id;
      }

      return $item;
    });

    $existingDetailIds = ListClaimPaymentV::detail()->where('payment_id', $claimPayment->id)->pluck('id');
    $requestDetailsIds = collect($details)->pluck('id');

    $deleteDetailIds = $existingDetailIds->diff($requestDetailsIds);

    // Delete removed details
    ClaimPaymentDetail::whereIn('id', $deleteDetailIds)->update(['status' => 'DEL']);

    $saved = true;

    return $saved;
  }

  public function saveClaimPaymentTotalAmount($model, $totalAmount): bool {
    $model->total_amount = $totalAmount;

    return $model->save();
  }

  public function getPartialPaymentNo($claimNo, $detailId, $causeCode): Object
  {
    try {
      $params = [
        '',
        $claimNo,
        $detailId,
        $causeCode,
        'PARTIAL',
        auth()->id(),
      ];

      info('Generating Partial Payment No.');
      $generated = DB::select("select * from ins_generate_claim_or_payment_no(?,?,?,?,?,?)", $params);

      if (optional($generated[0])->code === 'SUC') {
        info('Generated Partial Payment No.');
        return $generated[0];
      }
    } catch (\Exception $e) {
      Log::error('Generating Partial Payment No Error:' . $e->getMessage());
    }
  }

  public function getData(int $id)
  {
    $claimPayment = ListClaimPaymentV::master()
      ->select('id', 'claim_no', 'payment_date', 'approved_status', 'detail_id')
      ->findOr($id, fn () => abort(404, 'Not found.'));

    $listDescriptionOfLoss = ListClaimV::detail()
      ->select('cause_of_loss_desc', 'cause_of_loss_code', 'detail_id', 'claim_no')
      ->where('claim_no', $claimPayment->claim_no)
      ->get();

    $causeOfLosses = ListClaimPaymentV::detail()
      ->select('id', 'policy_id', 'cause_of_loss_code', 'amount', 'payment_no', 'payee_id', 'payment_date', 'payment_no', 'payment_type', 'remark')
      ->where('payment_id', $claimPayment->id)
      ->orderBy('id')
      ->get()
      ->map(function ($item) use ($listDescriptionOfLoss, $claimPayment) {
        $descriptionOfLoss = $listDescriptionOfLoss->where('cause_of_loss_code', $item->cause_of_loss_code)->value('cause_of_loss_desc');
        $detailId = $listDescriptionOfLoss->where('cause_of_loss_code', $item->cause_of_loss_code)->value('detail_id');
        $claimNo = $listDescriptionOfLoss->where('cause_of_loss_code', $item->cause_of_loss_code)->value('claim_no');
        $amount = ClaimDetail::select('cause_of_loss_desc', 'cause_of_loss_code', 'detail_id', 'amount')
          ->where('claim_no', $claimNo)->where('cause_of_loss_code', $item->cause_of_loss_code)->where('policy_id', $item->policy_id)->value('amount');
          return [
          ...$item->toArray(),
          'detail_id' => $detailId,
          'cause_of_loss_desc' => $descriptionOfLoss,
          'remain_amount' => round($amount - $this->getPartialAmount($claimNo, $item->cause_of_loss_code, $claimPayment->id), 2)
        ];
      });

    return [
      ...$claimPayment->toArray(),
      'cause_of_losses' => $causeOfLosses,
    ];
  }

  public function getPartialAmount(string $claimNo, string $causeCode, $paymentId): float
  {
    return ClaimPaymentDetail::whereHas('claimPayment', function ($query) use ($paymentId) {
      $query->where('status', 'ACT')
        ->where('approved_status', 'APV')
        ->when(filled($paymentId), function ($q) use($paymentId){
          $q->where('payment_id', '<', $paymentId);
        });
    })
      ->where('claim_no', $claimNo)
      ->where('cause_of_loss_code', $causeCode)
      ->sum('amount');
  }

  public function approve($claimId, $claimNo, $detailId, $approveStatus, $comment): JsonResponse
  {
    try {

      // If approve, final. if reject, normal.
      $approveType = ($approveStatus === 'APV') ? 'FINAL' : 'NORMAL';

      $params = [
        $claimId,
        $claimNo,
        $detailId,
        'PARTIAL',
        $approveType,
        $approveStatus,
        $comment,
        auth()->id(),
      ];

      info('Approving partial payment: ' . $claimNo);
      $approved = DB::select("select * from ins_claim_approve_or_reject(?,?,?,?,?,?,?,?)", $params);

      if (optional($approved[0])->code === 'SUC') {
        info('Successfully approved partial payment: ' . $claimNo);
        return response()->json($approved[0], 200);
      }

      return response()->json($approved[0], 400);
    } catch (\Exception $e) {
      Log::error('Approving partial payment: ' . $claimNo . ' :' . $e->getMessage());
      return response()->json(['message' => $e->getMessage()], $e->getCode());
    }
  }

  public function listRegisters()
  {
    return Claim::where('approved_status', 'APV')
      ->orderBy('claim_no')
      ->select('claim_no AS label','claim_no AS value')->get();
  }

  public function listCauseOfLossesByClaimNo($claimNo)
  {
    return ClaimDetail::select('cause_of_loss_desc', 'cause_of_loss_code', 'detail_id', 'policy_id', 'claim_id')
      ->where('claim_no', $claimNo)
      ->get()
      ->map(function ($item) use ($claimNo) {
        $amount = ClaimDetail::select('cause_of_loss_desc', 'cause_of_loss_code', 'detail_id', 'amount')
          ->where('claim_no', $claimNo)->where('cause_of_loss_code', $item->cause_of_loss_code)->where('policy_id', $item->policy_id)->value('amount');

        return [
          ...$item->toArray(),
          'remain_amount' => round($amount - $this->getPartialAmount($claimNo, $item->cause_of_loss_code, null), 2)
        ];
      });
  }

  /**
   * Check if amount in partial payment exceed the amount estimated in register
   */
  public function notHigherAmountThanEstimated($claimNo, $causeCode, $amount, $condType): bool
  {
    try {
      $params = [
        $claimNo,
        $causeCode,
        $amount,
        'PARTIAL',
        $condType,
      ];

      info('Validating Partial Claim Payment: ' . $claimNo . ' Cause Code: ' . $causeCode);
      $validated = DB::select("select * from ins_verify_claim_payment(?,?,?,?,?)", $params);

      if (optional($validated[0])->code === 'SUC') {
        info('Validate Passed: ' . $claimNo . ' Cause Code: ' . $causeCode);
        return true;
      }
      Log::error('Validate failed: ' . $claimNo . ' Cause Code: ' . $causeCode);
      return false;
    } catch (\Exception $e) {
      Log::error('Validate Partial Claim Payment: ' . $claimNo . ' :' . $e->getMessage());
      return false;
    }
  }

  public function hasNoPendingPartialPayment($claimNo): bool
  {
    return ListClaimPaymentV::master()
      ->where('claim_no', $claimNo)
      ->where(function($query) {
        $query->where('approved_status', null)
        ->orWhere('approved_status', 'REJ');
      })
      ->doesntExist();
  }

  public function savePaymentNumbers(int $paymentId): bool
  {
    $details = ClaimPaymentDetail::where('payment_id', $paymentId)->get();

    DB::beginTransaction();
    try {
      $saved = false;

      $details->each(function ($item) {
        $paymentNumberData = $this->getPartialPaymentNo($item->claim_no, $item->detail_id, $item->cause_of_loss_code);
        $item->update(['payment_no' => optional($paymentNumberData)->generate_no]);
      });

      $saved = true;

      if ($saved) {
        DB::commit();

        return true;
      }

      return false;
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Save Partial Payment Payment Numbers Error: ' . $e->getMessage());
      return false;
    }
  }

  public function havePaymentNumbers(int $paymentId): bool
  {
    $paymentNumbersCount = ClaimPaymentDetail::where('payment_id', $paymentId)
      ->whereNotNull('payment_no')
      ->count();
    $detailCount = ClaimPaymentDetail::where('payment_id', $paymentId)->count();

    return $paymentNumbersCount === $detailCount;
  }

  public function getVehiclesByDetailId($detailId)
  {
    return ClaimVehicleListV::select('detail_id', 'plate_no', 'insured_name')
      ->where('detail_id', $detailId)->first();
  }

  public function listPayees()
  {
    return Payee::select('id', 'name_en', 'type')
      ->get()
      ->map(function ($item) {
        return [
          'label' => $item->name_en,
          'value' => $item->id,
        ];
      });
  }

  public function printDetail($id)
  {
    $claimPayment = ClaimPaymentPrintV::where('partial_id', $id)
      ->where('type', 'PARTIAL_MASTER')
      ->first();
    $reinsuranceDetails = ClaimPaymentReinsurancePrintV::select('partial_id','claim_no','name', 'share', 'reserve_amount')
      ->where('data_id', $claimPayment->data_id)
      ->where('policy_id', $claimPayment->policy_id)
      ->where('detail_id', $claimPayment->detail_id)
      ->where('partial_id',$claimPayment->partial_id)
      ->where('claim_no',$claimPayment->claim_no)
      ->get()
      ->map(function ($item) {
        return [
          ...$item->toArray(),
          'percentaged_share' => $item->percentagedShare,
        ];
      });
 
    $claimPaymentDetails = $this->getClaimPaymentDetails($claimPayment->partial_id, $reinsuranceDetails, $id);
    return [
      'claim_payment' => $claimPayment,
      'claim_payment_details' => $claimPaymentDetails,
      'product_code' => Auto::find($claimPayment->data_id)->product()->value('alt_code'),
      'reinsurance_details' => $reinsuranceDetails,
      'total_share_rate' => $reinsuranceDetails->sum('percentaged_share'),
      ...$this->getApprovalDetail($id),
    ];
  }

  public function getClaimPaymentDetails(int $id, $reinsurance, $paymentId = null)
  {
    $detailsQuery = ClaimPaymentPrintV::where('partial_id', $id)->where('type', 'PARTIAL_DETAIL');

    $payees = ClaimPaymentPrintV::select('payee_id', 'payee_name', 'payee_type')
      ->whereIn('payee_id', $detailsQuery->pluck('payee_id'))
      ->where('type', 'PAYEE')
      ->get();

    $causeOfLosses = ListClaimV::detail()
      ->select('cause_of_loss_code', 'cause_of_loss_desc','cause_of_loss_desc_kh','amount')
      ->whereIn('cause_of_loss_code', $detailsQuery->pluck('cause_of_loss_code'))
      ->where('claim_no', $detailsQuery->value('claim_no'))
      ->get();

    return ClaimPaymentPrintV::select(
      'claim_no',
      'payment_no',
      'payment_date',
      'payee_id',
      'cause_of_loss_code',
      'amount',
      'payee_address',
      'payment_type',
      'insured_sharing_request',
      'deductible',
      'remark',
      'policy_id',
      'claim_payable'
    )
      ->where('partial_id', $id)
      ->where('type', 'PARTIAL_DETAIL')
      ->orderBy('id')
      ->get()
      ->map(function ($item) use ($reinsurance, $payees, $causeOfLosses, $paymentId) {
        $payee = $payees->firstWhere('payee_id', $item->payee_id);
        $causeOfLossDesc = $causeOfLosses->where('cause_of_loss_code', $item->cause_of_loss_code)->value('cause_of_loss_desc');
        $causeOfLossDescKh = $causeOfLosses->where('cause_of_loss_code', $item->cause_of_loss_code)->value('cause_of_loss_desc_kh');

        $reinsuranceData = $reinsurance->map(function ($i) use ($item) {
          return [
            ...$i,
            'claim_payable' => round($i['share'] * $item->amount, 2),
          ];
        });
        
        $formatCurrency = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        $amount_in_letters = $formatCurrency->format($item->amount)." dollars ";
       
        $amount = ClaimDetail::select('cause_of_loss_desc', 'cause_of_loss_code', 'detail_id', 'amount')
          ->where('claim_no', $item->claim_no)->where('cause_of_loss_code', $item->cause_of_loss_code)->where('policy_id', $item->policy_id)->value('amount');

        return [
          ...$item->toArray(),
          ...$payee->toArray(),
          'amount_in_letters' => $amount_in_letters,
          'cause_of_loss_desc' => $causeOfLossDesc,
          'cause_of_loss_desc_kh' => $causeOfLossDescKh,
          'claim_payable'=>$item->amount,
          'claim_amount'=>round($amount - $this->getPartialAmount($item->claim_no, $item->cause_of_loss_code, $paymentId),2),
          'remain_amount'=>round($amount - ($item->amount + $this->getPartialAmount($item->claim_no, $item->cause_of_loss_code, $paymentId)),2),
          'reinsurance' => $reinsuranceData,
          'total_share_rate' => $reinsurance->sum('percentaged_share'),
        ];
      });
  }

  public function getApprovalDetail(int $id)
  {
    $approvalDetail = ClaimPayment::select(
      'updated_by',
      'updated_at',
      'approved_status',
      'approved_at',
      'approved_by',
      'created_at',
      'created_by'
    )->find($id);

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

  public function delete(ClaimPayment $model) : JsonResponse {
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
    } catch(\Exception $e) {
      DB::rollBack();
      Log::error('Delete Partial Payment Error: ' . $e->getMessage());
      return response()->json(['message' => $e->getMessage()], 500);
    }
  }
  
  public function deleteDetail(int $paymentId): bool {
    $saved = false;
  
    ClaimPaymentDetail::where('payment_id', $paymentId)->update(['status' => 'DEL']);
    
    $saved = true;
  
    return $saved;
  }
}

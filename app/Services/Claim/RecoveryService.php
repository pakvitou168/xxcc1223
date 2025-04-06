<?php

namespace App\Services\Claim;

use App\Models\Claim\Process\ClaimTransaction;
use App\Models\Claim\Recovery\ClaimRecovery;
use App\Models\Claim\Recovery\ClaimRecoveryDetail;
use App\Models\Claim\Recovery\ClaimRecoveryPrintV;
use App\Models\Claim\Recovery\ClaimRecoveryReinsurancePrintV;
use App\Models\Claim\Recovery\ListRecoveryV;
use App\Models\Claim\Register\ListClaimV;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecoveryService
{
  public function save(ClaimRecovery $model, array $data): JsonResponse {
    DB::beginTransaction();

      try {
        $savedDetail = $this->saveClaimRecoveryDetail($data);

        if ($savedDetail) {
          DB::commit();
          return response()->json(['message' => 'success'], 200);
        }
        return response()->json(['message' => 'Calculate Claim Process Error.'], 500);
      } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Save Claim Process Error: ' . $e->getMessage());
        return response()->json(['message' => $e->getMessage()], 500);
      }
  }

  public function saveClaimRecoveryDetail(array $data): bool
  {

    $data = (object) $data;

    $saved = false;

    collect($data->cause_of_losses)->each(function ($item) {
      $item = (object) $item;
      $detailData = [
        'payment_type' => $item->payment_type,
        'salvage' => $item->salvage,
        'remark' => $item->remark,
      ];

      ClaimRecoveryDetail::updateOrCreate(['id' => optional($item)->id], $detailData);
    });

    $saved = true;

    return $saved;
  }

  public function getData(int $id) {
    $recovery = ListRecoveryV::master()
      ->select('id', 'claim_no', 'detail_id', 'insured_name', 'document_no', 'approved_status','address')
      ->findOr($id, fn () => abort(404, 'Not found.'));

    $listDescriptionOfLoss = ListClaimV::detail()
      ->select('cause_of_loss_desc', 'cause_of_loss_code')
      ->where('claim_no', $recovery->claim_no)
      ->get();

    $paymentTypes = ListRecoveryV::select('enum_id', 'name')
      ->where('group_id', 'CLAIM_TYPE')
      ->where('type_id', 'PAYMENT_TYPE')
      ->get();

    $causeOfLosses = ListRecoveryV::detail()
      ->select('id', 'deductible_paid','third_party_recovery','deductible', 'insured_sharing_request', 'payment_type', 'salvage', 'remark', 'cause_of_loss_code')
      ->where('txn_id', $id)
      ->orderBy('id')
      ->get()
      ->map(function ($item) use ($listDescriptionOfLoss, $paymentTypes) {
        $descriptionOfLoss = $listDescriptionOfLoss->where('cause_of_loss_code', $item->cause_of_loss_code)->value('cause_of_loss_desc');
        $paymentTypeName = $paymentTypes->where('enum_id', $item->payment_type)->value('name');

        return [
          ...$item->toArray(),
          'payment_type_name' => $paymentTypeName,
          'cause_of_loss_desc' => $descriptionOfLoss,
        ];
      });

    return [
      ...$recovery->toArray(),
      'cause_of_losses' => $causeOfLosses
    ];
  }

  public function makerIsApprover($id): bool {
    $claimRecovery = ClaimRecovery::find($id);

    return $claimRecovery->updated_by ? $claimRecovery->updated_by == auth()->id() : $claimRecovery->created_by == auth()->id();
  }

  public function approve($id, $claimNo, $detailId, $approveStatus, $comment): JsonResponse {
    try {  
      $params = [
        $id,
        $claimNo,
        $detailId,
        'RECOVERY',
        'FINAL',
        $approveStatus,
        $comment,
        auth()->id(),
      ];
      info(implode(',', $params));
  
      info('Approving claim recovery: ' . $claimNo);
      $approved = DB::select("select * from ins_claim_approve_or_reject(?,?,?,?,?,?,?,?)", $params);
      if (optional($approved[0])->code === 'SUC') {
    info('Successfully approved claim recovery: ' . $claimNo);
    return response()->json($approved[0], 200);
      }
      return response()->json($approved[0], 400);
    } catch(\Exception $e) {
      Log::error('Approving claim recovery error: ' . $claimNo . ' :' . $e->getMessage());
      return response()->json(['message' => $e->getMessage()], $e->getCode());
    }
  }

  public function printDetail($id)
  {  
    $claimRecovery = ClaimRecoveryPrintV::where('type','MASTER_PROCESS')->where('recovery_id', $id)->first();
    
    $reinsuranceDetails = ClaimRecoveryReinsurancePrintV::select('claim_no','name', 'share', 'reserve_amount')
      ->where('data_id', $claimRecovery->data_id)
      ->where('policy_id', $claimRecovery->policy_id)
      ->where('detail_id', $claimRecovery->detail_id)
      ->where('claim_no',$claimRecovery->claim_no)
      ->where('id',$id)->get()
      ->map(function ($item) {
        return [
          ...$item->toArray(),
          'percentaged_share' => $item->percentagedShare,
        ];
      });

    return [
      'claim_recovery' => $claimRecovery,
      'reinsurance_details' => $reinsuranceDetails,
      'total_share_rate' => $reinsuranceDetails->sum('percentaged_share'),
      'total_share'=>$reinsuranceDetails->sum('reserve_amount'),
      ...$this->getApprovalDetail($id),
    ];
  }

  public function getApprovalDetail(int $transactionId)
  {
      $approvalDetail = ClaimTransaction::select(
          'updated_by',
          'updated_at',
          'approved_status',
          'approved_at',
          'approved_by',
          'created_by',
          'created_at'
      )->where('cond_type','RECOVERY')->find($transactionId);

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

  public function delete(ClaimRecovery $model): JsonResponse {
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
      Log::error('Delete Recovery Error: ' . $e->getMessage());
      return response()->json(['message' => $e->getMessage()], 500);
    }
  }

  public function deleteDetail(int $txnId): bool {
    $saved = false;

    ClaimRecoveryDetail::where('txn_id', $txnId)->update(['status' => 'DEL']);

    $saved = true;

    return $saved;
  }
}

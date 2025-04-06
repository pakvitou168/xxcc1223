<?php

namespace App\Services\Claim;

use App\Models\Claim\PartialPayment\ClaimPaymentDetail;
use App\Models\Claim\Payee;
use App\Models\Claim\Process\ClaimRevisionPrintV;
use App\Models\Claim\Process\ClaimRevisionReinsurancePrintV;
use App\Models\Claim\Process\ClaimTransaction;
use App\Models\Claim\Process\ClaimTransactionDetail;
use App\Models\Claim\Process\ClaimTransactionDetailTemp;
use App\Models\Claim\Process\ClaimTransactionPrintV;
use App\Models\Claim\Process\ClaimTransactionReinsurancePrintV;
use App\Models\Claim\Process\ListClaimProcessV;
use App\Models\Claim\Recovery\ClaimRecovery;
use App\Models\Claim\Register\Claim;
use App\Models\Claim\Register\ClaimDetail;
use App\Models\Claim\Register\ClaimVehicleListV;
use App\Models\Claim\Register\ListClaimV;
use App\Models\Insurance\Auto;
use App\Models\Insurance\BasePolicy;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessService
{

    public function save(ClaimTransaction $model, array $data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $claimTransactionId = $this->saveClaimTransaction($model, $data);

            if ($claimTransactionId) {
                $savedDetail = $this->saveClaimTransactionDetail($claimTransactionId, $data);

                if ($savedDetail) {
                    // Calculate every time data changes
                    $calculated = $this->calculateClaimProcess($model->claim_no, $model->data_id, $model->detail_id);

                    if ($calculated) {
                        DB::commit();
                        return response()->json(['message' => 'success'], 200);
                    }
                    Log::error('Calculate Claim Process Error.');
                    return response()->json(['message' => 'Calculate Claim Process Error.'], 500);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save Claim Process Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function saveClaimTransaction(ClaimTransaction $model, array $data): int
    {
        $data = (object) $data;

        $claim = Claim::where('claim_no', $data->claim_no)
            ->where('approved_status', 'APV')
            ->first();

        if (!$claim) abort(400, 'Claim Register is not available for claim process');

        $model->policy_id = $claim->policy_id;
        $model->data_id = $claim->data_id;
        $model->detail_id = $claim->detail_id;
        $model->claim_no = $claim->claim_no;

        // Default 0
        $model->claim_payable = 0;
        $model->claim_request = 0;
        $model->total_claim = 0;
        $model->vehicle_uuid = $claim->vehicle_uuid;

        $model->product_code = BasePolicy::where('id', $claim->policy_id)->value('product_code');

        if ($model->save()) return $model->id;
    }

    public function saveClaimTransactionDetail(int $claimTransactionId, array $data): bool
    {

        $data = (object) $data;

        $claimTransaction = ClaimTransaction::find($claimTransactionId);

        $saved = false;

        collect($data->cause_of_losses)->each(function ($item) use ($claimTransaction) {
            $item = (object) $item;
            $detailData = [
                'policy_id' => $claimTransaction->policy_id,
                'data_id' => $claimTransaction->data_id,
                'detail_id' => $claimTransaction->detail_id,
                'payee_id' => $item->payee_id,
                'claim_no' => $claimTransaction->claim_no,
                'cause_of_loss_code' => $item->cause_of_loss_code,
                'type' => $item->type,
                'cond_type' => 'PROCESS',
                'partial_amount' => $this->getPartialAmount($claimTransaction->claim_no, $item->cause_of_loss_code),
                'remain_amount' => optional($item)->remain_amount ?? 0,
                'deductible_paid' => $item->deductible_paid ?? 0,
                'insured_sharing_request' => $item->insured_sharing_request ?? 0,
                ...$this->deductibleDefaultValue(optional($item)->id),
                'remark' => optional($item)->remark,
                'txn_id' => $claimTransaction->id,
                'payment_type' => $item->payment_type,
                'recovery_from_third_party' => optional($item)->recovery_from_third_party ?? 0,
                'vehicle_uuid' => $claimTransaction->vehicle_uuid
            ];

            ClaimTransactionDetail::updateOrCreate(['id' => optional($item)->id], $detailData);
        });

        $saved = true;

        return $saved;
    }

    public function previewAndValidateDeductibles($request, $validateFromSaving)
    {
        $claim = Claim::where('claim_no', $request->claim_no)
            ->where('approved_status', 'APV')
            ->first();
        $deductibles = [];
        foreach($request->cause_of_losses as $item){
            $this->saveTemp($claim, (object)$item); //save temporary detail for review deductible.
            $params = [
                $request->claim_no,
                $claim->data_id,
                $claim->detail_id,
                $item['cause_of_loss_code'],
                $item['partial_amount'],
                $item['remain_amount']
            ];
            $generated = DB::select('select * from ins_calc_claim_preview_deductible(?,?,?,?,?,?)', $params);
            info('deductible generated',$generated);
            array_push($deductibles, ...$generated);
            if ($validateFromSaving && isset($item['deductible_paid']) && $generated[0]->deductible &&  $item['deductible_paid'] > $generated[0]->deductible) {
                return [
                    'success' => false,
                    'message' => 'Deductible paid of ' . $item['cause_of_loss_desc'] . ' bigger than deductible.',
                    'data' => $deductibles
                ];
            }
        }
      
        return [
            'success' => true,
            'message' => 'Preview Deductible Success.',
            'data' => $deductibles
        ];
    }

    private function saveTemp($claimTransaction, $item)
    {
            $detailData = [
                'policy_id' => $claimTransaction->policy_id,
                'data_id' => $claimTransaction->data_id,
                'detail_id' => $claimTransaction->detail_id,
                'payee_id' => $item->payee_id,
                'claim_no' => $claimTransaction->claim_no,
                'cause_of_loss_code' => $item->cause_of_loss_code,
                'type' => $item->type,
                'cond_type' => 'PROCESS',
                'partial_amount' => $this->getPartialAmount($claimTransaction->claim_no, $item->cause_of_loss_code),
                'remain_amount' => optional($item)->remain_amount ?? 0,
                'deductible_paid' => $item->deductible_paid ?? 0,
                'deductible'=>0,
                'status'=>'ACT',
                'created_by'=>auth()->id(),
                'insured_sharing_request' => $item->insured_sharing_request ?? 0,
                'remark' => optional($item)->remark,
                'payment_type' => $item->payment_type
            ];
            ClaimTransactionDetailTemp::Create($detailData);
    }

    private function deductibleDefaultValue($id): array
    {
        // If new data
        if (!$id) {
            return [
                'deductible' => 0,
            ];
        }

        return [];
    }

    public function calculateClaimProcess(string $claimNo, int $dataId, int $detailId): bool
    {
        try {
            $params = [
                $claimNo,
                $dataId,
                $detailId,
                auth()->id(),
            ];

            info('Calculating Claim Process: ' . $claimNo);
            $generated = DB::select("select * from ins_calc_claim_process(?,?,?,?)", $params);

            if (optional($generated[0])->code === 'SUC') {
                info('Successfully Calculated Claim Process: ' .  $claimNo);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Calculating Claim Process Error:' . $e->getMessage());
            return false;
        }
    }

    public function getPartialAmount(string $claimNo, string $causeCode): float
    {
        return ClaimPaymentDetail::whereHas('claimPayment', function ($query) {
            $query->where('status', 'ACT')
                ->where('approved_status', 'APV');
        })
            ->where('claim_no', $claimNo)
            ->where('cause_of_loss_code', $causeCode)
            ->sum('amount');
    }

    public function getData(int $id)
    {
        $transaction = ListClaimProcessV::master()
            ->select('id', 'claim_no', 'payment_date', 'detail_id')
            ->findOr($id, fn () => abort(404, 'Not found.'));

        $listDescriptionOfLoss = ListClaimV::detail()
            ->select('cause_of_loss_desc', 'cause_of_loss_code')
            ->where('claim_no', $transaction->claim_no)
            ->get();

        $recoveryFromThirdPartyFromRegister = ListClaimV::detail()
            ->where('claim_no', $transaction->claim_no)
            ->where('cause_of_loss_code', 'OD')
            ->value('recovery_from_third_party');

        $causeOfLosses = ListClaimProcessV::detail()
            ->select(
                'id',
                'payee_id',
                'cause_of_loss_code',
                'type',
                'payee_address',
                'insured_sharing',
                'partial_amount',
                'remain_amount',
                'salvage',
                'third_party_recovery',
                'remark',
                'deductible_amount',
                'deductible_amount AS deductible',
                'deductible_paid',
                'insured_sharing_request',
                'payment_type',
                'recovery_from_third_party',
            )
            ->where('txn_id', $id)
            ->orderBy('id')
            ->get()
            ->map(function ($item) use ($listDescriptionOfLoss, $recoveryFromThirdPartyFromRegister) {
                $descriptionOfLoss = $listDescriptionOfLoss->where('cause_of_loss_code', $item->cause_of_loss_code)->value('cause_of_loss_desc');

                return [
                    ...$item->toArray(),
                    ...($item->cause_of_loss_code === 'OD' ?
                        [
                            'recovery_from_third_party_from_register' => $recoveryFromThirdPartyFromRegister,
                        ] : []
                    ),
                    'cause_of_loss_desc' => $descriptionOfLoss,
                ];
            });
        return [
            ...$transaction->toArray(),
            'cause_of_losses' => $causeOfLosses
        ];
    }

    public function getProcessPaymentNo($claimNo, $detailId, $causeCode): Object
    {
        try {
            $params = [
                '',
                $claimNo,
                $detailId,
                $causeCode,
                'PROCESS',
                auth()->id(),
            ];

            info('Generating Process Payment No.');
            $generated = DB::select("select * from ins_generate_claim_or_payment_no(?,?,?,?,?,?)", $params);

            if (optional($generated[0])->code === 'SUC') {
                info('Generated Process Payment No.');
                return $generated[0];
            }
        } catch (\Exception $e) {
            Log::error('Generating Process Payment No Error:' . $e->getMessage());
        }
    }

    public function listRegisters()
    {
        return Claim::where('approved_status', 'APV')
            ->orderBy('claim_no')
            ->select('claim_no AS value','claim_no AS label')->get();
    }

    public function listCauseOfLossesByClaimNo($claimNo)
    {
        return ClaimDetail::select('cause_of_loss_desc', 'cause_of_loss_code', 'type', 'amount', 'detail_id', 'recovery_from_third_party')

            ->where('claim_no', $claimNo)
            ->get()
            ->map(function ($item) use ($claimNo) {

                $claim = $item->toArray();
                unset($claim['recovery_from_third_party']);

                return [
                    ...$claim,
                    'recovery_from_third_party_from_register' => $item->recovery_from_third_party,
                    'partial_amount' => $this->getPartialAmount($claimNo, $item->cause_of_loss_code),
                    'remain_amount' => round($item->amount - $this->getPartialAmount($claimNo, $item->cause_of_loss_code), 2)
                ];
            });
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

    public function printDetail($processID)
    {
        $claimPaymentDetail = ClaimTransactionPrintV::where('txn_id', $processID)
            ->where('type', 'PROCESS_MASTER')
            ->first();

        $reinsuranceDetails = ClaimTransactionReinsurancePrintV::select('name', 'share', 'reserve_amount')
            ->where('data_id', $claimPaymentDetail->data_id)
            ->where('policy_id', $claimPaymentDetail->policy_id)
            ->where('detail_id', $claimPaymentDetail->detail_id)
            ->where('txn_id', $processID)
            ->where('claim_no',$claimPaymentDetail->claim_no)
            ->get()
            ->map(function ($item) {
                return [
                    ...$item->toArray(),
                    'percentaged_share' => $item->percentagedShare,
                ];
            });
        $causeOfLosses = $this->getCauseOfLoss($claimPaymentDetail->txn_id, $reinsuranceDetails);

        return [
            'product_code' => Auto::find($claimPaymentDetail->data_id)->product()->value('alt_code'),
            'claim_payment_detail' => $claimPaymentDetail,
            'cause_of_loss' => $causeOfLosses,
            'reinsurance_details' => $reinsuranceDetails,
            'total_share_rate' => $reinsuranceDetails->sum('percentaged_share'),
            'total_payable_amount' => $reinsuranceDetails->sum('reserve_amount'),
            ...$this->getApprovalDetail($processID),
        ];
    }

    public function printRevision($processID)
    {
        $claim = ClaimTransactionPrintV::where('txn_id', $processID)
            ->where('type', 'PROCESS_MASTER')
            ->first();

        $claimPaymentDetails = ClaimRevisionPrintV::where('claim_no', $claim->claim_no)->where('data_id', $claim->data_id)->where('detail_id', $claim->detail_id)->where('policy_id', $claim->policy_id)->get();
        $reinsuranceDetails = ClaimRevisionReinsurancePrintV::where('claim_no', $claim->claim_no)->where('data_id', $claim->data_id)->where('detail_id', $claim->detail_id)->where('policy_id', $claim->policy_id)->get();
       
        $totalShare = $reinsuranceDetails->sum('share');
        $totalSharePercentage = $totalShare ? round($totalShare * 100, 7) : 0;
        $claimEstimation = $claimPaymentDetails->sum('amount');
        $reinsuranceDetails=$reinsuranceDetails->map(function ($i) use ($claimEstimation) {
            return [
                'percentaged_share' => $i->percentagedShare,
                ...$i->toArray(),
                 'claim_payable' => round($i['share'] * $claimEstimation, 2),
            ];
        });
        
        return [
            'claim' => $claim,
            'claim_payment_details' => $claimPaymentDetails,
            'reinsurance_details' => $reinsuranceDetails,
            'total_share_percentage' => $totalSharePercentage,
            'claim_estimation' => $claimEstimation,
            ...$this->getApprovalDetail($processID),
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
        )->find($transactionId);

        $prepareById = $approvalDetail->updated_by ?? $approvalDetail->created_by;
        $prepareDate = $approvalDetail->updated_at ?? $approvalDetail->created_at;

      return  [
            'approved_status' => $approvalDetail->approved_status,
            'updated_at' => $prepareDate,
            'updated_by_name' => User::where('id', $prepareById)->value('full_name'),
            'approved_at' => $approvalDetail->approved_at,
            'approved_by_name' => User::where('id', $approvalDetail->approved_by)->value('full_name'),
        ];
    }

    public function getCauseOfLoss(int $txn_id, $reinsurance)
    {
        $causeOfLosses = ClaimTransactionPrintV::select(
            'txn_id',
            'claim_no',
            'payee_id',
            'cause_of_loss_code',
            'remain_amount',
            'payee_address',
            'deductible',
            'claim_payable',
            'claim_request',
            'total_claim',
            'payment_no',
            'payment_type',
            'payment_date',
            'insured_sharing_request',
            'deductible_paid',
            'remark',
            'policy_id',
            'recovery_from_third_party',
            'cond_recovery_type',
        )
            ->where('txn_id', $txn_id)
            ->where('type', 'PROCESS_DETAIL')
            ->orderBy('id')
            ->get()
            ->map(function ($item) use ($reinsurance) {
                $payee = ClaimTransactionPrintV::select('payee_name', 'payee_type')
                    ->where('payee_id', $item->payee_id)
                    ->where('type', 'PAYEE')
                    ->first();

                $listDescriptionOfLoss = ListClaimV::detail()
                    ->where('claim_no', $item->claim_no)
                    ->where('cause_of_loss_code', $item->cause_of_loss_code)->value('cause_of_loss_desc'.(App::getLocale()== 'en'? '':'_kh'));

                $reinsuranceData = $reinsurance->map(function ($i) use ($item) {
                    return [
                        ...$i,
                        'claim_payable' => round($i['share'] * ($item->claim_payable + $item->claim_request), 2),
                    ];
                });
                $formatCurrency = new \NumberFormatter(App::getLocale() == "en" ? 'en' : 'km', \NumberFormatter::SPELLOUT);
                $amount_in_letters = $formatCurrency->format($item->total_claim).trans('Dollars');
                $amount = ClaimDetail::select('cause_of_loss_desc', 'cause_of_loss_code', 'detail_id', 'amount')
                ->where('claim_no', $item->claim_no)->where('cause_of_loss_code', $item->cause_of_loss_code)->where('policy_id', $item->policy_id)->value('amount');

                if ($item->cond_recovery_type === 'CLAIM_DETAIL') {
                    $item->recovery_from_third_party_from_register = $item->recovery_from_third_party;
                    unset($item->recovery_from_third_party);
                }
                
                return [
                    ...$item->toArray(),
                    ...$payee->toArray(),
                    'amount_in_letters' => $amount_in_letters,
                    'cause_of_loss_desc' => $listDescriptionOfLoss,
                    'reinsurance' => $reinsuranceData,
                    'total_share_rate' => $reinsurance->sum('percentaged_share'),
                ];
            });
        return $causeOfLosses;
    }

    public function approve($id, $claimNo, $detailId, $approveStatus, $comment): JsonResponse
    {
        try {
            // If approve, final. if reject, normal.
            $approveType = ($approveStatus === 'APV') ? 'FINAL' : 'NORMAL';

            $params = [
                $id,
                $claimNo,
                $detailId,
                'PROCESS',
                $approveType,
                $approveStatus,
                $comment,
                auth()->id(),
            ];

            info('Approving claim process: ' . $claimNo);
            $approved = DB::select("select * from ins_claim_approve_or_reject(?,?,?,?,?,?,?,?)", $params);
            if (optional($approved[0])->code === 'SUC') {
                info('Successfully approved claim process: ' . $claimNo);
                return response()->json($approved[0], 200);
            }
            return response()->json($approved[0], 400);
        } catch (\Exception $e) {
            Log::error('Approving claim process error: ' . $claimNo . ' :' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function getVehiclesByDetailId($detailId)
    {
        return ClaimVehicleListV::select('detail_id', 'plate_no', 'insured_name')
            ->where('detail_id', $detailId)->first();
    }

    public function savePaymentNumbers(int $txnId): bool
    {
        $details = ClaimTransactionDetail::where('txn_id', $txnId)->get();

        DB::beginTransaction();
        try {
            $saved = false;

            $details->each(function ($item) {
                $paymentNumberData = $this->getProcessPaymentNo($item->claim_no, $item->detail_id, $item->cause_of_loss_code);
                info(optional($paymentNumberData)->generate_no);
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
            Log::error('Save Process Payment Numbers Error: ' . $e->getMessage());
            return false;
        }
    }

    public function havePaymentNumbers(int $txnId): bool
    {
        $paymentNumbersCount = ClaimTransactionDetail::where('txn_id', $txnId)
            ->whereNotNull('payment_no')
            ->count();
        $detailCount = ClaimTransactionDetail::where('txn_id', $txnId)->count();

        return $paymentNumbersCount === $detailCount;
    }

    public function generateRecovery($id): bool
    {
        $claimTransaction = ClaimTransaction::where('approved_status', 'APV')->findOr($id, fn () => abort(404, 'Not found.'));

        if ($this->alreadyGeneratedRecovery($claimTransaction->claim_no)) {
            abort(400, 'Claim Recovery is already generated!');
        }

        try {
            $params = [
                $claimTransaction->claim_no,
                $claimTransaction->data_id,
                $claimTransaction->detail_id,
                auth()->id(),
            ];

            info('Generate Recovery: ' . $claimTransaction->claim_no);
            $generated = DB::select("select * from ins_clone_claim_process(?,?,?,?)", $params);

            if (optional($generated[0])->status === 'SUC') {
                info('Successfully Generate Recovery: ' .  $claimTransaction->claim_no);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Generating Recovery Error:' . $e->getMessage());
            return false;
        }
    }

    public function alreadyGeneratedRecovery($claimNo)
    {
        return ClaimRecovery::where('claim_no', $claimNo)->exists();
    }

    public function delete(ClaimTransaction $model): JsonResponse {
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
            Log::error('Delete Full Payment Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteDetail(int $txnId): bool {
        $saved = false;

        ClaimTransactionDetail::where('txn_id', $txnId)->update(['status' => 'DEL']);

        $saved = true;

        return $saved;
    }
}

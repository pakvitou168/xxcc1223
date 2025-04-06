<?php

namespace App\Services\Claim\HS;

use App;
use Exception;
use Carbon\Carbon;
use App\Models\Claim\Payee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\HS\Claim\ClaimRegisterV;
use App\Models\UserManagement\User\UserFile;
use App\Models\HS\Claim\Register\ClaimHSDetail;
use App\Models\HS\Claim\Payment\ClaimTransaction;
use App\Models\HS\Claim\Payment\ClaimTransactionV;
use KhmerDateTime\KhmerDateTime;

class HSPaymentService
{
    public function __construct(private ClaimTransaction $claimTransaction, private ClaimTransactionV $claimTransactionV)
    {
    }

    public function listCauseOfLossesByClaimNo($claimNo)
    {
        $claim = ClaimRegisterV::where('claim_no', $claimNo)->first();
        $claim_hs_detail = ClaimHSDetail::where('id', $claim->claim_detail_id)->first();

        return [
            'claim' => $claim,
            'claim_hs_detail' => $claim_hs_detail,
        ];
    }

    public function listRegisters($search = null)
    {
        return ClaimRegisterV::when($search, function ($q) use ($search) {
            $q->where('document_no', 'ILIKE', $search . "%")->orWhere('claim_no', 'ILIKE', $search . "%");
        })
            ->where('approved_status', 'APV')
            ->where('schema_approved_status', 'APV')
            ->where('is_transaction_created', false)
            ->orderBy('claim_no')
            ->pluck('claim_no');
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

    public function getData($id)
    {
        $claimPayment = $this->claimTransactionV->where('txn_id', $id)->whereLangCode('EN')->firstOr(fn () => abort(404, 'Not found.'));

        return [
            'claim_payment' => $claimPayment,
        ];
    }

    public function printDetail($id)
    {
        $claimPaymentEN = $this->claimTransactionV->where('txn_id', $id)->whereLangCode('EN')->firstOr( fn () => abort(404, 'Not found.'));
        $claimPaymentEN->reporting_date = $claimPaymentEN->reporting_date ? Carbon::createFromFormat('d/M/Y', $claimPaymentEN->reporting_date)->format('d/m/Y') : null;
        $formatCurrency = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $claimPaymentEN->amount_in_words = $formatCurrency->format($claimPaymentEN->amount).trans('Dollars',[],'en');
        $claimPaymentEN->date_of_disability = Carbon::parse($claimPaymentEN->date_of_disability)->format('d F Y');
        $claimPaymentEN->non_payable_in_words = $formatCurrency->format($claimPaymentEN->total_non_payable_expense).trans('Dollars',[],'en');

        $claimPaymentKM = $this->claimTransactionV->where('txn_id', $id)->whereLangCode('KM')->firstOr( fn () => abort(404, 'Not found.'));
        $claimPaymentKM->reporting_date = $claimPaymentKM->reporting_date ? Carbon::createFromFormat('d/M/Y', $claimPaymentKM->reporting_date)->format('d/m/Y') : null;
        $formatCurrency = new \NumberFormatter('km', \NumberFormatter::SPELLOUT);
        $claimPaymentKM->amount_in_words = $formatCurrency->format($claimPaymentKM->amount).trans('Dollars',[],'km');
        $claimPaymentKM->date_of_disability = KhmerDateTime::parse(Carbon::parse($claimPaymentKM->date_of_disability)->format('Y-m-d'))->format('LL');
        $claimPaymentKM->non_payable_in_words = $formatCurrency->format($claimPaymentKM->total_non_payable_expense).trans('Dollars',[],'km');
        // $signature = UserFile::select('file_url')->where('user_id', $claimPayment->approved_by)->where('file_type', 'SIGNATURE')->first();
        // if ($signature)
        //     if (!$signature->file_url)
        //         $signature = null;
        return [
            'EN' => $claimPaymentEN,
            'KM' => $claimPaymentKM,
            // 'signature' => $signature
        ];
    }

    public function store($claim, $p_payee_id, $p_payment_type, $p_user_id, $amount): JsonResponse{
        $params = [
            $claim->claim_id, 
            $p_payee_id, 
            $p_payment_type, 
            $p_user_id
        ];
        if($claim){
            $result = DB::select("select ins_hs_claim_generate_transaction(?,?,?,?)", $params);
            return response()->json(['success' => true, 'message' => 'Claim payment successfully','data' => $result], 200);
            //schema type not Hospitalization
            // if($claim->schema_type != 'STANDARD'){
            //     // $check_remaining = DB::select("select * from ins_hs_get_claim_opd_remaining($claim->claim_detail_id,  null)");
                
            //     //if($check_remaining[0]->remaining > $amount) {
            //         // Executing the function
                   
                
            // }else{
            //     $result = DB::select("select ins_hs_claim_generate_transaction(?,?,?,?)", $params);
            //     return response()->json(['success' => true, 'message' => 'Claim payment successfully','data' => $result], 200);
            // }
        }else{
            return response()->json(['success' => false, 'message' => 'claim not found'], 500);
        }
    }

    public function update($id, $data): JsonResponse
    {
        $claimPayment = ClaimTransaction::find($id);
        $claimPayment->payment_type = $data['payment_type'];
        $claimPayment->payee_id = $data['payee_id'];
        if($claimPayment->update()){
            return response()->json([$claimPayment], 200);
        }
    }

    public function approve($claim_id, $claimDetailId, $approveStatus, $comment): JsonResponse
    {
        $params = [
            $claim_id,
            $claimDetailId,
            $approveStatus,
            'PROCESS',
            $comment,
            auth()->id()
        ];

        info('Approving claim process: ' . $claim_id);
        $approved = DB::select("select * from ins_hs_claim_approve_or_reject(?,?,?,?,?,?)", $params);
        
        if (optional($approved[0])->response_code === 200) {
            info('Successfully approved claim process: ' . $claim_id);
            return response()->json($approved[0], 200);
        }
        return response()->json($approved[0], 400);
    
    }

}

<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessRequest;
use App\Models\Claim\Process\ClaimTransaction;
use App\Models\Claim\Process\ClaimTransactionDetailTemp;
use App\Models\Claim\Process\ListClaimProcessV;
use App\Models\Claim\Register\Claim;
use App\Models\RefEnum;
use App\Services\Claim\ProcessService;
use App\Services\Claim\RegisterService;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessController extends Controller
{
    use DataTable;

    public function __construct(private ProcessService $processService)
    {
        $this->middleware('has-permission:CLAIM_PROCESS.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:CLAIM_PROCESS.NEW')->only('store');
        $this->middleware('has-permission:CLAIM_PROCESS.UPD')->only('update');
        $this->middleware('has-permission:CLAIM_PROCESS.DEL')->only('destroy');
        $this->middleware('has-permission:CLAIM_PROCESS.APV')->only('approve');
        $this->middleware('has-permission:CLAIM_PROCESS.REV')->only('revise');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            ListClaimProcessV::master()->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProcessRequest $request, RegisterService $registerService)
    {
        $previewAndValidateDeductibles=$this->previewAndValidateDeductibles($request,true);

        if ($previewAndValidateDeductibles['success']) {
            $claimTransaction = new ClaimTransaction();

            $response = $this->processService->save($claimTransaction, $request->all());

            if ($response->status() === 200) {

                $claim = Claim::where('claim_no', $request->claim_no)->where('confirmed_final_claim', 'N')->first();
                if ($claim) {
                    $this->approveFinalRegister($registerService, $claim);
                }

                return response([
                    'success' => true,
                    'message' => 'Record is created.'
                ], 201);
            }

            return response(['message' => 'Something  went wrong!'], $response->status());
        }else{
              return $previewAndValidateDeductibles;
        }
    }

    public function previewAndValidateDeductibles(ProcessRequest $request, $validateFromSaving = false)
    {
        return $this->processService->previewAndValidateDeductibles($request,$validateFromSaving);
    }

    private function approveFinalRegister(RegisterService $registerService, $claim)
    {
        $response = $registerService->approve($claim->id, $claim->claim_no, $claim->detail_id, 'FINAL', $claim->approved_status, $claim->approved_cmt, $claim->approved_by);

        if ($response->status() === 200) {
            info('Successfully approved final Claim No: ' . $claim->claim_no);
            return;
        }
        Log::error('Failed to approved final Claim No: ' . $claim->claim_no);
        return;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->processService->getData($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProcessRequest $request, $id)
    {
        $previewAndValidateDeductibles = $this->previewAndValidateDeductibles($request, true);

        if ($previewAndValidateDeductibles['success']) {
            $claimTransaction = ClaimTransaction::where('approved_status', null)->findOr($id, fn () => abort(404, 'Not found.'));

            $response = $this->processService->save($claimTransaction, $request->all());

            if ($response->status() === 200) {
                return [
                    'success' => true,
                    'message' => 'Record is updated.'
                ];
            }

            return response(['message' => 'Something  went wrong!'], $response->status());
        } else {
            return $previewAndValidateDeductibles;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $claimTransaction = ClaimTransaction::where('approved_status', null)->findOr($id, fn () => abort(404, 'Not found.'));

        $response = $this->processService->delete($claimTransaction);

        if ($response->status() === 200) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
        return response(['message' => 'Something  went wrong!'], $response->status());
    }

    public function approve(Request $request, $id)
    {
        if ($this->checkMakerAndApprover($id)) {
            abort(403, "You can not approve your own Process.");
        }

        $claimTransaction = ClaimTransaction::select('claim_no', 'detail_id')
            ->where('approved_status', null)
            ->findOr($id, fn () => abort(404, 'Not found.'));
        $response = $this->processService->approve($id, $claimTransaction->claim_no, $claimTransaction->detail_id, $request->status, $request->comment);

        if ($response->status() === 200) {
            return [
                'success' => true,
                'message' => 'Approved successfully.'
            ];
        }

        return response(['message' => 'Something  went wrong!'], $response->status());
    }

    public function checkMakerAndApprover($claim_transaction_id)
    {
        $maker = ClaimTransaction::find($claim_transaction_id);
        return $maker->updated_by ? $maker->updated_by == auth()->id() : $maker->created_by == auth()->id();
    }

    public function revise($id)
    {
        $claimTransaction = ClaimTransaction::where('approved_status', 'REJ')
            ->findOr($id, fn () => abort(404, 'Not found.'));

        $claimTransaction->approved_status = null;
        $claimTransaction->approved_cmt = null;
        $claimTransaction->approved_by = null;
        $claimTransaction->approved_at = null;

        if ($claimTransaction->saveQuietly()) {
            return [
                'success' => true,
                'message' => 'Record is revised.'
            ];
        }
    }

    public function print($id, $lang)
    {
        App::setLocale($lang);

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Claim Payment');

        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => '',
                'hasLetterHead' => request()->letterhead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => request()->letterhead
            ]),
        ]);
        if (request()->print == 'revision') {
            $data = $this->processService->printRevision($id);
            $pdf->loadView('pdf.processes.revision', $data);

            return $pdf->stream('Claim Revision Request.pdf');
        }
        $data = $this->processService->printDetail($id);
        if (request()->print == 'cheque') {
            $pdf->loadView('pdf.processes.cheque', $data);

            return $pdf->stream('Claim Cheque Request.pdf');
        }
        $pdf->loadView('pdf.processes.process', $data);

        return $pdf->stream('Claim Registration.pdf');
    }

    public function getLovs()
    {
        return [
            'registers' => $this->processService->listRegisters(),
            'payees' => $this->processService->listPayees(),
            'payee_types' => RefEnum::listPayeeTypes(),
            'payment_types'=>RefEnum::listPaymentTypes()
        ];
    }

    public function savePaymentNumbers($id)
    {
        $isApprovedClaimTxn = ClaimTransaction::where('approved_status', 'APV')
            ->where('id', $id)
            ->exists();

        if (!$isApprovedClaimTxn) abort(400, 'Process is not approved.');

        if ($this->processService->havePaymentNumbers($id))
            abort(400, 'Payment numbers is already generated');

        $success = $this->processService->savePaymentNumbers($id);

        if ($success) {
            return [
                'success' => true,
                'message' => 'Generated Payment Numbers successfully.'
            ];
        }

        return response(['message' => 'Something  went wrong!']);
    }

    public function havePaymentNumbers($id)
    {
        return $this->processService->havePaymentNumbers($id);
    }

    public function listCauseOfLosses($claimNo)
    {
        return $this->processService->listCauseOfLossesByClaimNo($claimNo);
    }

    public function detail($id)
    {
        return $this->processService->printDetail($id);
    }

    public function getVehicle($detailId)
    {
        return $this->processService->getVehiclesByDetailId($detailId);
    }

    public function generateRecovery($id) {
        $generated = $this->processService->generateRecovery($id);

        if ($generated) {
            return [
                'success' => true,
                'message' => 'Generated Recovery successfully.'
            ];
        }
        return response(['message' => 'Something  went wrong!'], 500);
    }

    public function alreadyGeneratedRecovery($claimNo) {
        return $this->processService->alreadyGeneratedRecovery($claimNo);
    }
}

<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartialPaymentRequest;
use App\Models\Claim\PartialPayment\ClaimPayment;
use App\Models\Claim\PartialPayment\ListClaimPaymentV;
use App\Models\Claim\Register\Claim;
use App\Models\RefEnum;
use App\Services\Claim\PartialPaymentService;
use App\Services\Claim\RegisterService;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class PartialPaymentController extends Controller
{
    use DataTable;

    public function __construct(private PartialPaymentService $partialPaymentService)
    {
        $this->middleware('has-permission:CLAIM_PARTIAL_PAYMENT.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:CLAIM_PARTIAL_PAYMENT.NEW')->only('store');
        $this->middleware('has-permission:CLAIM_PARTIAL_PAYMENT.UPD')->only('update');
        $this->middleware('has-permission:CLAIM_PARTIAL_PAYMENT.DEL')->only('destroy');
        $this->middleware('has-permission:CLAIM_PARTIAL_PAYMENT.APV')->only('approve');
        $this->middleware('has-permission:CLAIM_PARTIAL_PAYMENT.REV')->only('revise');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            ListClaimPaymentV::master()->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PartialPaymentRequest $request, RegisterService $registerService)
    {
        $claimPayment = new ClaimPayment();

        $response = $this->partialPaymentService->save($claimPayment, $request->all());

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
        return $this->partialPaymentService->getData($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PartialPaymentRequest $request, $id)
    {
        $claimPayment = ClaimPayment::where('approved_status', null)->findOr($id, fn () => abort(404, 'Not found.'));

        $response = $this->partialPaymentService->save($claimPayment, $request->all());

        if ($response->status() === 200) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }

        return response(['message' => 'Something  went wrong!'], $response->status());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $claimPayment = ClaimPayment::findOr($id, fn () => abort(404, 'Not found.'));

        $response = $this->partialPaymentService->delete($claimPayment);

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
            abort(403, "You can not approve your own Partial Payment.");
        }

        $claim = ClaimPayment::select('claim_no', 'detail_id')
            ->where('approved_status', null)
            ->findOr($id, fn () => abort(404, 'Not found.'));
        $response = $this->partialPaymentService->approve($id, $claim->claim_no, $claim->detail_id, $request->status, $request->comment);

        if ($response->status() === 200) {
            return [
                'success' => true,
                'message' => 'Approved successfully.'
            ];
        }

        return response(['message' => 'Something  went wrong!'], $response->status());
    }

    public function savePaymentNumbers($id) {
        $isApprovedClaimPayment = ClaimPayment::where('approved_status', 'APV')
            ->where('id', $id)
            ->exists();
        
        if (!$isApprovedClaimPayment) abort(400, 'Partial Payment is not approved.');

        if ($this->partialPaymentService->havePaymentNumbers($id))
            abort(400, 'Payment numbers is already generated');

        $success = $this->partialPaymentService->savePaymentNumbers($id);

        if ($success) {
            return [
                'success' => true,
                'message' => 'Generated Payment Numbers successfully.'
            ];
        }

        return response(['message' => 'Something  went wrong!']);
    }

    public function havePaymentNumbers($id) {
        return $this->partialPaymentService->havePaymentNumbers($id);
    }

    public function checkMakerAndApprover($claim_payment_id)
    {
        $maker = ClaimPayment::find($claim_payment_id);
        return $maker->updated_by ? $maker->updated_by == auth()->id() : $maker->created_by == auth()->id();
    }

    public function revise($id)
    {
        $claimPayment = ClaimPayment::where('approved_status', 'REJ')
            ->findOr($id, fn () => abort(404, 'Not found.'));

        $claimPayment->approved_status = null;
        $claimPayment->approved_cmt = null;
        $claimPayment->approved_by = null;
        $claimPayment->approved_at = null;

        if ($claimPayment->saveQuietly()) {
            return [
                'success' => true,
                'message' => 'Record is revised.'
            ];
        }
    }

    public function print($id, $lang) {
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

        $data = $this->partialPaymentService->printDetail($id);
        $pdf->loadView('pdf.partial_payments.claim_payment', $data);

        return $pdf->stream('Claim Payment.pdf');
    }

    public function detail($id)
    {
        return $this->partialPaymentService->printDetail($id);
    }

    public function getLovs()
    {
        return [
            'registers' => $this->partialPaymentService->listRegisters(),
            'payees' => $this->partialPaymentService->listPayees(),
            'payment_types' => RefEnum::listPaymentTypes(),
            'payee_types' =>RefEnum::listPayeeTypes(),
        ];
    }

    public function listCauseOfLosses($claimNo)
    {
        return $this->partialPaymentService->listCauseOfLossesByClaimNo($claimNo);
    }

    public function getVehicle($detailId)
    {
        return $this->partialPaymentService->getVehiclesByDetailId($detailId);
    }

    public function printCheque($id, $lang) {
        App::setLocale($lang);
        
        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'CLAIMS CHEQUE REQUEST');

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

        $data = $this->partialPaymentService->printDetail($id);

        $pdf->loadView('pdf.partial_payments.cheque', $data);

        return $pdf->stream('Claim Cheque Request.pdf');
    }
}

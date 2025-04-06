<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecoveryRequest;
use App\Models\Claim\Recovery\ClaimRecovery;
use App\Models\Claim\Recovery\ListRecoveryV;
use App\Models\RefEnum;
use App\Services\Claim\RecoveryService;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class RecoveryController extends Controller
{
    use DataTable;

    public function __construct(private RecoveryService $recoveryService)
    {
        $this->middleware('has-permission:CLAIM_RECOVERY.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:CLAIM_RECOVERY.UPD')->only('update');
        $this->middleware('has-permission:CLAIM_RECOVERY.DEL')->only('destroy');
        $this->middleware('has-permission:CLAIM_RECOVERY.APV')->only('approve');
        $this->middleware('has-permission:CLAIM_RECOVERY.REV')->only('revise');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            ListRecoveryV::master()->latest('id')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->recoveryService->getData($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RecoveryRequest $request, $id)
    {
        $claimRecovery = ClaimRecovery::where('approved_status', null)->findOr($id, fn() => abort(404, 'Not found.'));

        $response = $this->recoveryService->save($claimRecovery, $request->all());

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
        $claimRecovery = ClaimRecovery::where('approved_status', null)->findOr($id, fn() => abort(404, 'Not found.'));

        $response = $this->recoveryService->delete($claimRecovery);

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
        $this->validate($request, [
            'status' => 'required',
            'comment' => 'required'
        ],[
            'comment.required' =>'Remark is required'
        ]);
        if ($this->recoveryService->makerIsApprover($id)) {
            abort(403, "You can not approve your own record.");
        }
        $claimRecovery = ClaimRecovery::select('claim_no', 'detail_id')
            ->where('approved_status', null)
            ->findOr($id, fn() => abort(404, 'Not found.'));

        $response = $this->recoveryService->approve($id, $claimRecovery->claim_no, $claimRecovery->detail_id, $request->status, $request->comment);

        if ($response->status() === 200) {
            return [
                'success' => true,
                'message' => 'Approved successfully.'
            ];
        }

        return response(['message' => 'Something  went wrong!'], $response->status());
    }

    public function getLovs()
    {
        return [
            'payment_types' => RefEnum::listPaymentTypes(),
        ];
    }

    public function print($id, $lang)
    {
        App::setLocale($lang);

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Deductible Recovery');

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

        $data = $this->recoveryService->printDetail($id);

        $pdf->loadView('pdf.recoveries.recovery', $data);

        return $pdf->stream('Deductible Recovery.pdf');
    }
}

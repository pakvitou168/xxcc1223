<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Claim\PartialPayment\ClaimPayment;
use App\Models\Claim\Process\ClaimTransaction;
use App\Models\Claim\Register\Claim;
use App\Models\Claim\Register\ListClaimV;
use App\Services\Claim\RegisterService;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class RegisterController extends Controller
{

    use DataTable;

    public function __construct(private RegisterService $registerService)
    {
        $this->middleware('has-permission:CLAIM_REGISTER.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:CLAIM_REGISTER.NEW')->only('store');
        $this->middleware('has-permission:CLAIM_REGISTER.UPD')->only('update');
        $this->middleware('has-permission:CLAIM_REGISTER.DEL')->only('destroy');
        $this->middleware('has-permission:CLAIM_REGISTER.APV')->only('approve');
        $this->middleware('has-permission:CLAIM_REGISTER.REV')->only('revise');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            ListClaimV::claim()->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        $claim = new Claim();

        $allRequestData = $this->registerService->getRequestData($request->all());

        $response = $this->registerService->save($claim, $allRequestData);

        if ($response->status() === 200) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }

        return response(['message' => 'Something  went wrong!'], $response->status());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->registerService->getData($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterRequest $request, $id)
    {
        $claim = Claim::where('approved_status', null)->findOr($id, fn() => abort(404, 'Not found.'));

        $allRequestData = $this->registerService->getRequestData($request->all());

        $response = $this->registerService->save($claim, $allRequestData);

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
        $claim = Claim::findOr($id, fn() => abort(404, 'Not found.'));

        $response = $this->registerService->delete($claim);

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
            abort(403, "You can not approve your own Register.");
        }
        $this->validate($request, [
            'status' => 'required',
            'comment' => 'required'
        ],[
            'comment.required' => 'Remark is required'
        ]);
        $claim = Claim::select('claim_no', 'detail_id')
            ->where('approved_status', null)
            ->findOr($id, fn() => abort(404, 'Not found.'));
        $response = $this->registerService->approve($id, $claim->claim_no, $claim->detail_id, 'NORMAL', $request->status, $request->comment, auth()->id());

        if ($response->status() === 200) {
            return [
                'success' => true,
                'message' => 'Approved successfully.'
            ];
        }

        return response(['message' => 'Something  went wrong!'], $response->status());
    }

    public function checkMakerAndApprover($claim_id)
    {
        $maker = Claim::find($claim_id);
        return $maker->updated_by ? $maker->updated_by == auth()->id() : $maker->created_by == auth()->id();
    }

    public function detail($id)
    {
        return $this->registerService->printDetail($id);
    }

    public function revise($id)
    {
        $claim = Claim::whereNotNull('approved_status')
            ->findOr($id, fn() => abort(404, 'Not found.'));

        // If register, has been processed to partial payment or full payment, cannot be revised
        if ($this->hasPartialPaymentOrProcess($claim->claim_no)) {
            abort(400, 'Cannot be revised');
        }

        $claim->approved_status = null;
        $claim->approved_cmt = null;
        $claim->approved_by = null;
        $claim->approved_at = null;

        if ($claim->saveQuietly()) {
            return [
                'success' => true,
                'message' => 'Record is revised.'
            ];
        }
    }

    private function hasPartialPaymentOrProcess($claimNo)
    {
        $hasPartialPayment = ClaimPayment::where('claim_no', $claimNo)->exists();
        $hasProcess = ClaimTransaction::where('claim_no', $claimNo)->exists();

        return $hasPartialPayment || $hasProcess;
    }

    public function print($id, $lang)
    {
        App::setLocale($lang);

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Claim Registration');

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

        $data = $this->registerService->printDetail($id);

        $pdf->loadView('pdf.registers.register', $data);

        return $pdf->stream('Claim Registration.pdf');
    }

    public function getLovs()
    {
        return [
            'policies' => $this->registerService->listClaimablePolicies(),
            'causeOfLosses' => $this->registerService->listCauseOfLosses(),
            'thirdParties' => $this->registerService->listThirdParties(),
            'drivers' => $this->registerService->listDrivers(),
            'adjusterCompanies' => $this->registerService->listAdjusterCompanies(),
        ];
    }

    public function listVehicles($policyDocNo)
    {
        return $this->registerService->listVehiclesByPolicyDocNo($policyDocNo);
    }


    public function listCovers($detailId)
    {
        return $this->registerService->listCovers($detailId);
    }

    public function getDeductibleDetail($autoDetailId)
    {

        return $this->registerService->getDeductibleDetail($autoDetailId);
    }
}

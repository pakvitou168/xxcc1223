<?php

namespace App\Http\Controllers\Claim\HS;

use App;
use Exception;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\HS\ClaimPaidExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HS\ClaimInCurredExport;
use App\Models\HS\Claim\ClaimRegisterV;
use App\Exports\HS\ClaimOutstandingExport;
use App\Http\Requests\HS\ClaimSchemaRequest;
use App\Services\Claim\HS\HSRegisterService;
use App\Http\Requests\HS\ClaimApproveRequest;
use App\Http\Requests\HS\ClaimRegisterRequest;
use App\Models\HS\Reinsurance\ClaimReinsurance;
use App\Http\Requests\HS\ClaimSchemaReviseRequest;

class HSRegisterController extends Controller
{
    use DataTable;
    CONST ERROR_MESSAGE = "Something went wrong!";
    public function __construct(private HSRegisterService $hSRegisterService)
    {
        $this->middleware('has-permission:HS_CLAIM_REGISTER.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:HS_CLAIM_REGISTER.NEW')->only('store');
        $this->middleware('has-permission:HS_CLAIM_REGISTER.UPDATE')->only('update','setSchema');
        $this->middleware('has-permission:HS_CLAIM_REGISTER.DELETE')->only('destroy');
        $this->middleware('has-permission:HS_CLAIM_REGISTER.APPROVE')->only('approve','approveSchema');
        $this->middleware('has-permission:HS_CLAIM_REGISTER.REVISE')->only('reviseSchema');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(ClaimRegisterV::orderByDesc('claim_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClaimRegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $register = $this->hSRegisterService->create((object) $request->validated());
            DB::commit();
            return response()->json(['success' => true, 'register' => $register, 'message' => 'Claim registered successfully']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->hSRegisterService->detail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->hSRegisterService->edit($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClaimRegisterRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $register = $this->hSRegisterService->update($id, (object) $request->validated());
            DB::commit();
            return response()->json(['success' => true, 'register' => $register, 'message' => 'Claim updated successfully']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], 500);
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
        try {
            DB::beginTransaction();
            $register = $this->hSRegisterService->delete($id);
            DB::commit();
            return response()->json(['success' => true, 'register' => $register, 'message' => 'Claim revised successfully']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], 500);
        }
    }

    public function getLov(Request $request)
    {
        return [
            'policies' => $this->hSRegisterService->policyList($request->search),
            'clinics' => $this->hSRegisterService->clinicList()
        ];
    }
    public function filterPolicy(Request $request)
    {
        return $this->hSRegisterService->policyList($request->search);
    }

    public function getInsuredPerson(Request $request)
    {
        return $this->hSRegisterService->insuredPersonsByPolicy();
    }

    public function getCauseOfLoss($policyId, $insuredId)
    {
        return $this->hSRegisterService->causeOfLoss($policyId, $insuredId);
    }

    public function approve(ClaimApproveRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->hSRegisterService->approve($id, (object) $request->validated());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Claim approved successfully']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], 500);
        }
    }

    public function reviseSchema(ClaimRegisterV $claim,ClaimSchemaReviseRequest $request)
    {
        try {
            DB::beginTransaction();
            $revision = $this->hSRegisterService->reviseSchema($claim,$request);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Claim revised successfully',...$revision]);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], 500);
        }
    }
    public function getSchema(ClaimRegisterV $claim)
    {
        try{
            if(!is_null($claim->schema_approved_status)) throw new Exception("Schema data has already been updated",403);
            $claim->date_of_disability = $claim->date_of_disability ? date('Y-m-d', strtotime($claim->date_of_disability)) : null;
            $claim->date_of_completed_doc = $claim->date_of_completed_doc ? date('Y-m-d', strtotime($claim->date_of_completed_doc)) : null;
            $data = [
                'claim' => $claim,
                'schema_data' => $this->hSRegisterService->schemaData($claim),
                'claim_histories' => $this->hSRegisterService->claimHistories($claim)
            ];
            return response()->json($data);
        }catch(Exception $e){
            report($e);
            return response()->json(['message' => self::ERROR_MESSAGE],$e->getCode() > 0 ? $e->getCode() : 500);
        }
    }
    public function setSchema(ClaimRegisterV $claim, ClaimSchemaRequest $request)
    {
        try {
            DB::beginTransaction();
            $schema = $this->hSRegisterService->saveSchema($claim, (Object) $request->validated());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Schema saved']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], $e->getCode() ?? 500);
        }
    }
    public function approveSchema(ClaimRegisterV $claim, Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->hSRegisterService->approveSchema($claim, (Object)$request->all());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Schema approved',...$data]);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' =>'Something went wrong'], 500);
        } 
    }
    public function pdfRegister(ClaimRegisterV $claim, $lang, $letterHead = 0)
    {
        $documentNo = $claim->claim_no;
        $reinsurances = ClaimReinsurance::whereClaimNo($claim->claim_no)->get();
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadView('pdf.claims.hs.register', ['claim' => $claim, 'lang' => $lang, 'reinsurances' => $reinsurances]);

        $pdf->setOption('title', 'PGI');

        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $pdf->setOption('footer-right', 'Page: [page] of [topage]          ');
        $pdf->setOption('footer-font-size', 8);
        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => $documentNo,
                'hasLetterHead' => $letterHead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => $letterHead
            ]),
        ]);
        return $pdf->stream($documentNo . '.pdf');
    }
    public function pdfSchema(ClaimRegisterV $claim, $lang, $letterHead = 0){
        $documentNo = $claim->claim_no;
        $schemaData = $this->hSRegisterService->schemaData($claim,true);
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadView('pdf.claims.hs.schema', ['claim' => $claim, 'lang' => $lang,'schemaData' => $schemaData]);

        $pdf->setOption('title', 'PGI');

        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $pdf->setOption('footer-right', 'Page: [page] of [topage]          ');
        $pdf->setOption('footer-font-size', 8);
        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => $documentNo,
                'hasLetterHead' => $letterHead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => $letterHead
            ]),
        ]);
        return $pdf->stream($documentNo . '.pdf');
    }
    public function exportClaim(Request $request)
    {
        if ($request->route('report_type') == 'ClaimsPaid') {
            return Excel::download(new ClaimPaidExport($request->route('from_date'), $request->route('to_date')), 'HS Claim Paid.xlsx');
        }
        if ($request->route('report_type') == 'ClaimsOutstanding') {
            return Excel::download(new ClaimOutstandingExport($request->route('from_date'), $request->route('to_date')), 'HS Claim Outstanding.xlsx');
        }
        if ($request->route('report_type') == 'ClaimsIncurred') {
            return Excel::download(new ClaimInCurredExport($request->route('from_date'), $request->route('to_date')), 'HS Claim Incurred.xlsx');
        }
    }
}
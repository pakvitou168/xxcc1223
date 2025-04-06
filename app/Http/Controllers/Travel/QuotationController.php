<?php

namespace App\Http\Controllers\Travel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Travel\QuotationApprovalRequest;
use App\Http\Requests\Travel\QuotationRequest;
use App\Imports\Travel\QuotationImport;
use App\Services\Travel\BaseService;
use App\Services\Travel\QuotationService;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class QuotationController extends Controller
{
    use DataTable;
    public function __construct(private BaseService $baseService, private QuotationService $quotationService)
    {
        $this->middleware('has-permission:TV_QUOTATION.VIEW')->only(['index', 'show','exportInsuredPerson']);
        $this->middleware('has-permission:TV_QUOTATION.NEW')->only(['store']);
        $this->middleware('has-permission:TV_QUOTATION.APPROVE')->only(['approve']);
        $this->middleware('has-permission:TV_QUOTATION.ACCEPT')->only(['accept']);
        $this->middleware('has-permission:TV_QUOTATION.DELETE')->only(['destroy']);
        $this->middleware('has-permission:TV_QUOTATION.PROCESS')->only(['proceed']);
    }

    public function index()
    {
        return $this->generateTableData($this->quotationService->list());
    }
    public function store(QuotationRequest $request)
    {
        try {
            DB::beginTransaction();
            $master = $this->quotationService->save($request->validated());
            DB::commit();
            $this->quotationService->storeCoverage($master,$request->schedule_benefits);
            return response()->json(['success' => true, 'message' => "Quote is created successfully"]);
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage() ?? 'Something went wrong'], 500);
        }
    }
    public function show($id)
    {
        return response()->json($this->quotationService->detail($id));
    }
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->quotationService->delete($id);
            DB::commit();
            return response()->json(['message' => 'Quotation deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    public function selection()
    {
        return response()->json([
            'productTypeOpts' => $this->baseService->productTypeOptions(),
            'customerTypeOpts' => $this->baseService->customerTypeOptions(),
            'endorsementClauseOpts' => $this->baseService->endorsementClauseOptions(),
            'generalExclusionOpts' => $this->baseService->generalExclusionOptions(),
            'businessChannelOpts' => $this->baseService->businessChannelOptions(),
            'businessHandlerOpts' => $this->baseService->businessHandlerOptions(),
            'scheduleBenefitOpt' => $this->baseService->scheduleBenefitOptions(),
            'policyWordingOpts' => $this->baseService->policyWordingOptions(),
            'defaultEndorsementClause' => $this->baseService->defaultEndorsementClause(),
            'defaultGeneralExclusion' => $this->baseService->defaultGeneralExclusion(),
            'calcOpts' => $this->baseService->calcOptions(),
            'periodOpts' => $this->baseService->periodOptions(),
            'quotationTmp' => $this->baseService->quotationTmp(),
            'packageAndGroupOpts' => $this->baseService->packageAndGroupOptions(),
            'countryOpts' => $this->baseService->countryOptions()
        ]);
    }
    public function searchInsuredPerson(Request $request)
    {
        return response()->json($this->baseService->searchInsuredPerson($request));
    }
    public function validateFileUpload(Request $request)
    {
        $this->validate($request, [
            'file' => ['required', 'mimes:xlsx']
        ]);
        try {
            $data = Excel::toArray(new QuotationImport(), $request->file('file'));
            $insuredPersons = isset($data['Name List']) ? $data['Name List'] : throw new \Exception("Name List sheet not found");

            $this->quotationService->validateInsuredData($insuredPersons);
            return response()->json(['success' => true, 'message' => 'Validation success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'errors' => [$e->getMessage()]], 500);
        }
    }
    public function businessOption(Request $request, $businessChannel)
    {
        return response()->json($this->baseService->businessOptions($businessChannel));
    }
    public function approve(QuotationApprovalRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->quotationService->approve($id, $request->validated());
            DB::commit();
            return response()->json(['message' => 'Quotation has been approved']);
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function accept(QuotationApprovalRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->quotationService->accept($id, $request->validated());
            DB::commit();
            return response()->json(['message' => 'Quotation has been accepted']);
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function proceed($id)
    {
        try {
            DB::beginTransaction();
            $this->quotationService->proceed($id);
            DB::commit();
            return response()->json(['message' => 'Quotation has been proceeded to policy']);
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function exportInsuredPerson($id)
    {
        return $this->quotationService->exportInsuredPerson($id);
    }
    public function print($id, $lang = 'en')
    {
        App::setLocale($lang);
        return $this->quotationService->print($id, $lang);
    }
}


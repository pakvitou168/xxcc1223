<?php

namespace App\Http\Controllers\PA;

use App;
use App\Http\Controllers\Controller;
use App\Http\Requests\PA\QuotationApprovalRequest;
use App\Http\Requests\PA\QuotationRequest;
use App\Http\Requests\PA\QuoteUpdateRequest;
use App\Imports\PA\QuotationImport;
use App\Services\PA\BaseService;
use App\Services\PA\QuotationService;
use App\Traits\DataTable;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class QuotationController extends Controller
{
    use DataTable;
    public function __construct(private BaseService $baseService, private QuotationService $qoutationService)
    {

    }
    public function index()
    {
        return $this->generateTableData($this->qoutationService->list());
    }
    public function store(QuotationRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->qoutationService->save($request->validated());
            DB::commit();
            return response()->json(['success' => true, 'message' => "Quote is created successfully"]);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }
    public function show($id)
    {
        return response()->json($this->qoutationService->detail($id));
    }
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->qoutationService->delete($id);
            DB::commit();
            return response()->json(['message' => 'Quotation deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
    
    public function approve(QuotationApprovalRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->qoutationService->approve($id, $request->validated());
            DB::commit();
            return response()->json(['message' => 'Quotation has been approved']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function accept(QuotationApprovalRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->qoutationService->accept($id, $request->validated());
            DB::commit();
            return response()->json(['message' => 'Quotation has been accepted']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function proceed($id)
    {
        try {
            DB::beginTransaction();
            $this->qoutationService->proceed($id);
            DB::commit();
            return response()->json(['message' => 'Quotation has been proceeded to policy']);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function exportInsuredPerson($id)
    {
        return $this->qoutationService->exportInsuredPerson($id);
    }
    public function print($id, $lang = 'en')
    {
        App::setLocale($lang);
        return $this->qoutationService->print($id, $lang);
    }
    public function edit($id)
    {
        return response()->json($this->qoutationService->edit($id));
    }
    public function update(QuoteUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->qoutationService->update($request->validated(), $id);
            DB::commit();
            return response()->json([
                'message' => 'Update success'
            ]);
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }
    public function insuredPerson($id)
    {
        return $this->generateTableData($this->qoutationService->insuredPersonList($id));
    }
}

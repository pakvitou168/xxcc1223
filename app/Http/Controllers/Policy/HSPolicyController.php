<?php

namespace App\Http\Controllers\Policy;

use App\Exports\HS\ReinsuranceExport;
use App\Http\Controllers\Controller;
use App\Models\HS\DataMaster;
use App\Models\HS\Insurance\PolicyView;
use App\Models\HS\Insurance\ReinsuranceData;
use App\Models\HS\Policy;
use App\Services\HS\PolicyService;
use App\Traits\DataTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class HSPolicyController extends Controller
{
    use DataTable;

    public function __construct(
        private PolicyService $policyService,
    ) {
        $this->middleware('has-permission:HS_POLICY.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:HS_POLICY.UPDATE')->only(['update']);
        $this->middleware('has-permission:HS_POLICY.APPROVE')->only(['approve']);
        $this->middleware('has-permission:HS_POLICY.REVISE')->only(['revise']);
        $this->middleware('has-permission:HS_POLICY.DELETE')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            PolicyView::with([
                'policy:id,approved_reason,approved_status,quotation_id',
                'policy.quotation:id,document_no'
            ])
                ->whereNull('version')
                ->where('status', '<>', 'DEL')
                ->orderByDesc('id')
            ,
            function (&$data) {
                foreach ($data as $key => $value) {
                    $value->_status = $value->status;
                }
            }
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Insurance\Policy  $policy
     * @return \Illuminate\Http\Response
     */
    public function show(Policy $policy)
    {
        return $policy;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Insurance\Policy  $policy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Policy $policy)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $policy);

        if ($policy->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    private function validateRequest($request)
    {
        $request->validate([
            'business_type' => 'required',
            'policy_type' => 'required',
        ]);
    }

    private function assignValues($request, $model)
    {
        $model->business_type = $request->post('business_type');
        $model->policy_type = $request->post('policy_type');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Insurance\Policy  $policy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Policy $policy)
    {
        $policy->status = "DEL";

        if ($policy->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }

    /**
     * Approve policy
     *
     * @param Request $request
     * @param Policy $policy
     * @return json
     */
    public function approve(Request $request, Policy $policy)
    {
        try {
            DB::beginTransaction();
            if ($this->checkMakerAndApprover($policy->id)) {
                throw new Exception("You can not approve your own Policy.",403);
            }

            $policy->status = $request->approved_status;
            $policy->approved_reason = $request->approved_reason;
            $policy->approved_at = now();
            $policy->approved_by = auth()->id();
            $policy->dataMaster()->update([
                'updated_at' => now()
            ]);

            if ($policy->save()) {
                if ($request->approved_status == 'APV') {
                    $params = [
                        $policy->document_no,
                        'INVOICE',
                        auth()->id(),
                    ];
                    $hsInvoice = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
                    if (!isset($hsInvoice[0]) || $hsInvoice[0]->code != 'SUC') {
                        Log::error('Generate policy invoice', ['invoice' => $hsInvoice]);
                        throw new Exception("Generate invoice failed",500);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $request->approved_status == 'APV' ? 'Approved successfully' : 'Rejected successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Something went wrong!','success' => false],$e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    public function checkMakerAndApprover($policy_id)
    {
        $maker = Policy::find($policy_id);
        return $maker->updated_by ? $maker->updated_by == auth()->id() : $maker->created_by == auth()->id();
    }

    public function revise(Policy $policy)
    {
        $policy->status = 'PND';
        $policy->approved_reason = null;
        if ($policy->save()) {
            return [
                'success' => true,
                'message' => 'Record is revised.'
            ];
        }
    }

    public function showDetail($id)
    {
        $hs = $this->policyService->getDataDetail($id);
        return [
            'hs' => $hs,
            'signature' => $hs['signature']
        ];
    }

    public function generateHSInvoice(Request $request)
    {
        $params = [
            $request->documentNo,
            $request->requestType,
            auth()->id(),
        ];
        $hsInvoice = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
        if (!empty($hsInvoice) && $hsInvoice[0]->code == 'SUC') {
            return response([
                'success' => true,
                'message' => 'Generate Invoice Successfully!',
            ]);
        }
        return response('Something went wrong!', 500);
    }

    public function isPolicyReinsuranceCompleted(DataMaster $policy)
    {
        return ReinsuranceData::isReinsuranceCompleted($policy->id);
    }

    // Use approved_status as submit_status
    public function updateSubmitStatus(DataMaster $policy, Request $request)
    {
        if ($policy->approved_status !== $request->status) {
            $policy->approved_status = $request->status;

            if ($policy->save()) {
                return [
                    'success' => true,
                    'message' => 'Policy submit status has been updated to ' . $request->post('status')
                ];
            }
        }
    }

    public function exportReinsurance($id, $productCode)
    {
        return Excel::download(new ReinsuranceExport($id, $productCode), 'Reinsurance.xlsx');
    }
}

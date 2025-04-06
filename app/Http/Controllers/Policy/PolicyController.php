<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\Policy;
use App\Models\Insurance\PolicyView;
use App\Models\Insurance\ReinsuranceData;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class PolicyController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->middleware('has-permission:POLICY.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:POLICY.UPDATE')->only(['update']);
        $this->middleware('has-permission:POLICY.APPROVE')->only(['approve']);
        $this->middleware('has-permission:POLICY.REVISE')->only(['revise']);
        $this->middleware('has-permission:POLICY.DELETE')->only(['destroy']);
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
            function(&$data){
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

    private function validateRequest($request) {
        $request->validate([
            'business_type' => 'required',
            'policy_type' => 'required',
        ]);
    }

    private function assignValues($request, $model) {
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
    public function approve(Request $request, Policy $policy) {
        // Check if user is authorized for approve
        $this->authorize('approve', Policy::class);

        $isReinsuranceCompleted = ReinsuranceData::isReinsuranceCompleted($policy->id);
        $isConfigurationCompleted = $policy->isPolicyConfigurationCompleted();

        if (!$isReinsuranceCompleted || !$isConfigurationCompleted) return response('Policy data has not completed!', 400);

        if($this->checkMakerAndApprover($policy->id)){
            abort(403,"You can not approve your own Policy.");
        }

        $policy->status = $request->approved_status;
        $policy->approved_reason = $request->approved_reason;
        $policy->approved_at = now();
        $policy->approved_by = auth()->id();
        $policy->auto()->update([
            'updated_at' => now()
        ]);
        if ($policy->save()) {
            return response ([
                'success' => true,
                'message' => $request->approved_status == 'APV' ? 'Approved successfully' : 'Rejected successfully'
            ]);
        }
        return response('Something went wrong!', 500);
    }

    public function checkMakerAndApprover($policy_id){
        $maker = Policy::find($policy_id);
        return $maker->updated_by ? $maker->updated_by == auth()->id() : $maker->created_by == auth()->id();
    }

    public function revise(Policy $policy) {
        $policy->status = 'PND';
        $policy->approved_reason = null;
        if ($policy->save()) {
            return [
                'success' => true,
                'message' => 'Record is revised.'
            ];
        }
    }

    public function generateAutoEndorsement(Request $request, Policy $policy) {
        if ($policy->status !== 'APV') return response('Policy is not yet approved', 400);
        $params = [
            $policy->id,
            $request->auto_endorsement_type,
            $request->endorsement_e_date,
            $request->endorsement_description,
            auth()->id(),
        ];
        $generated = DB::select("select * from ins_prod_auto_gen_new_policy_endorsement(?,?,?,?,?)", $params);
        info($generated);
        if (!empty($generated)) {
            $this->fillEffectiveDateToEndorVehicle(optional($generated[0])->master_id,$request->endorsement_e_date);
            return response([
                'success' => true,
                'message' => 'Generate Endorsement Successfully!',
                'data' => $generated
            ]);
        }
    }

    private function fillEffectiveDateToEndorVehicle($master_id,$endorsement_e_date) {
        try {
            $autoDetails = AutoDetail::where('master_data_id',$master_id)->get();
            if(count($autoDetails)>0) {
                foreach ($autoDetails as $item) {
                    if($item->status != 'DEL') {
                        $item->endorsement_e_date = $endorsement_e_date;
                        $item->save();
                    }
                }
            }
        } catch (\Throwable $th) {
            info('Fill effective date to endorsement vehicle');
            info($th->getMessage());
        }
    }

    public function generateAutoInvoice(Request $request){
        $params = [
            $request->documentNo,
            $request->requestType,
            auth()->id(),
        ];
        $autoInvoice = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
        info('Invoice Generation');
        info($autoInvoice);
        if (!empty($autoInvoice))
            return response([
                'success' => true,
                'message' => 'Generate Invoice Successfully!',
            ]);
        return response('Something went wrong!', 500);
    }

    // override trait
    public function actionButtons($item, $permissions) {
        if ($permissions) {
            $canView = $permissions['VIEW'];
            $canUpdate = $permissions['UPDATE'];
            $canDelete = $permissions['DELETE'];
            $canRevise = $permissions['REVISE'];
        } else {
            $canView = $canUpdate = $canDelete = true;
        }

        $showViewButton = $canView ? '' : 'hidden';
        $showUpdateButton = $canUpdate ? '' : 'hidden';
        $showDeleteButton = $canDelete ? '' : 'hidden';
        $showReviseButton = $canRevise ? '' : 'hidden';

        $actionBtn = $item->status == 'REJ' ? '<svg title="Revise" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>' : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
        $revise = $item->status == 'REJ' ? 'title="Revise"' : '';
        return '
            <div class="flex justify-center">
                <a class="view flex items-center mr-1 text-sm '.$showViewButton.'" href="javascript:;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </a>
                <a '.$revise.' class="edit flex items-center mr-1 '.($item->status == 'REJ' ? $showReviseButton : $showUpdateButton).'" href="javascript:;">'.$actionBtn.'</a>
                <a class="delete flex items-center text-theme-6 '.$showDeleteButton.'" href="javascript:;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </a>
            </div>
        ';
    }
}

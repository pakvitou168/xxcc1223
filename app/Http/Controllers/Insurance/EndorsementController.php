<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Quotation\AutoController;
use App\Models\Insurance\Auto;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\Endorsement\Endorsement;
use App\Models\Insurance\Endorsement\EndorsementView;
use App\Models\Insurance\ReinsuranceData;
use App\Models\RefEnum;
use App\Scopes\ActiveScope;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\ReinsuranceConfig\ReinsurancePartner;

class EndorsementController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Endorsement::class, 'endorsement');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            EndorsementView::with('endorsement:id,approved_reason,approved_status')
                ->where('status', '<>', 'DEL')
                ->orderByDesc('id')
            ,
            function( &$datas ) {
                foreach ($datas as $data) {
                    $data->endorsed_premium = $this->getPremium(Endorsement::where('id',$data->id)->first(),$data->document_no);
                    $data->_status = $data->status;
                }
            }
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Insurance\Endorsement  $endorsement
     * @return \Illuminate\Http\Response
     */
    public function show(Endorsement $endorsement)
    {
        return $endorsement->load('auto:id,endorsement_type,endorsement_e_date');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Insurance\Endorsement  $endorsement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Endorsement $endorsement)
    {
        $this->validateRequest($request);

        $endorsement->business_type = $request->business_type;
        $endorsement->policy_type = $request->policy_type;

        if ($endorsement->save()) {
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

    /**
     * Cancel endorsement
     *
     * @param  \App\Models\Insurance\Endorsement  $endorsement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Endorsement $endorsement)
    {
        if ($endorsement->status === 'APV')
            return response([
                'success' => false,
                'message' => 'Cannot cancel approved endorsement'
            ], 400);

        $params = [
            $endorsement->id,
            $endorsement->auto->product_code,
            'POLICY',
            $endorsement->auto->id,
            auth()->id()
        ];

        $response = DB::select("
            select * from ins_prod_auto_cancel_endorsement(?,?,?,?,?)", $params);
        if (optional($response[0])->code === 'SUC')
            return [
                'success' => true,
                'message' => 'Endorsement has been cancelled.'
            ];
    }

    public function revise(Endorsement $endorsement) {
        $this->authorize('revise', Endorsement::class);
        $endorsement->status = 'PND';
        $endorsement->approved_reason = null;
        if ($endorsement->save()) {
            return [
                'success' => true,
                'message' => 'Record is revised.'
            ];
        }
    }

    public function showDetail(Endorsement $endorsement) {
        // TODO: move to service or model class
        $data = (new AutoController)->showDetail($endorsement->data_id);

        // get vehicles for cancellation, addition and deletion kinds in endorsement stage only
        $data['auto']->vehicles = [];
        $data['auto']->vehicles = AutoDetail::select('id')->withoutGlobalScopes([ActiveScope::class])
                                        ->where('master_data_id', $endorsement->data_id)
                                        ->where('endorsement_stage', $endorsement->document_no)
                                        ->get();

        $auto = Auto::find($endorsement->data_id);
        $data['endorsement_type'] = RefEnum::listAutoEndorsementTypes()->where('value',$auto->endorsement_type)->value('label');
        $data['endorsement_description'] = nl2br($endorsement->endorsement_description);

        $data['endorsement_premium'] = $this->getPremium($endorsement, $endorsement->document_no);

        return $data;
    }

    public function generateAutoCreditNote(Request $request){
        $params = [
            $request->documentNo,
            $request->requestType,
            auth()->id()
        ];
        $autoCreditNote = DB::select("select * from ins_generate_policy_invoice_note(?,?,?)", $params);
        info('Credit Note Generation');
        info($autoCreditNote);
        if (!empty($autoCreditNote))
            return response([
                'success' => true,
                'message' => 'Generate Credit Note Successfully!',
            ]);
        return response('Something went wrong!', 500);
    }

    /**
     * Approve policy
     *
     * @param Request $request
     * @param Endorsement $endorsement
     * @return json
     */
    public function approve(Request $request, Endorsement $endorsement) {
        // Check if user is authorized for approve
        $this->authorize('approve', Endorsement::class);
        $isReinsuranceCompleted = ReinsuranceData::isReinsuranceCompleted($endorsement->id, $endorsement->document_no);
        $isConfigurationCompleted = $endorsement->isPolicyConfigurationCompleted();
        if (!$isReinsuranceCompleted || !$isConfigurationCompleted) return response('Endorsement data has not completed!', 400);

        if($this->checkMakerAndApprover($endorsement->id)){
            abort(403,"You can not approve your own endorsement.");
        }

        $endorsement->status = $request->approved_status;
        $endorsement->approved_reason = $request->approved_reason;
        $endorsement->approved_at = now();
        $endorsement->approved_by = auth()->id();

        if ($endorsement->save()) {
            return response ([
                'success' => true,
                'message' => $request->approved_status == 'APV' ? 'Approved successfully' : 'Rejected successfully'
            ]);
        }
        return response('Something went wrong!', 500);
    }

    public function checkMakerAndApprover($endorsement_id){
        $maker = Endorsement::find($endorsement_id);
        return $maker->updated_by ? $maker->updated_by == auth()->id() : $maker->created_by == auth()->id();
    }

    public function generateAutoEndorsement(Request $request, Endorsement $endorsement) {
        if ($endorsement->status !== 'APV') return response('Policy is not yet approved', 400);
        $params = [
            $endorsement->id,
            $request->auto_endorsement_type,
            $request->endorsement_e_date,
            $request->endorsement_description,
            auth()->id()
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

    public function getPremium(Endorsement $endorsement, $endorsementStage, $rawNumber = false) {
        $endorsementPremium = AutoDetail::withoutGlobalScopes([ActiveScope::class])
            ->where('master_data_id', $endorsement->data_id)
            ->where('endorsement_stage', $endorsementStage)
            ->sum('premium');

        if ($rawNumber) return $endorsementPremium;

        if ($endorsementPremium < 0)
            return '(' . number_format(abs($endorsementPremium), 2,'.', ',') . ')';

        return number_format($endorsementPremium, 2,'.', ',');
    }

    public function listCancelVehicles(Endorsement $endorsement) {
        $endorsement_description = $endorsement->endorsement_description;

        $vehicles = AutoDetail::select('id', 'selected_cover_pkg', 'vehicle_value', 'model_id', 'premium')
            ->where('master_data_id', $endorsement->data_id)
            ->get()
            ->map(function($item) use ($endorsement) {
                $item->selected_cover_pkg = collect(explode(',', $item->selected_cover_pkg))->join(', ');
                $item->make = $item->makeModel->make()->select('make')->value('make');
                $item->model = $item->makeModel()->select('model')->value('model');
                // Default Endorsement Effective Date
                $item->endorsement_e_date = optional($endorsement->auto)->endorsement_e_date;

                return $item;
            });

        return [
            'endorsement_description' => $endorsement_description,
            'vehicles' => $vehicles
        ];
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
        $showDeleteButton = $canDelete && $item->status != 'APV' ? '' : 'hidden';
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

    public function getReinsuranceData(Endorsement $endorsement) {
        return ReinsuranceData::where('policy_id', $endorsement->id)
            ->where('lvl', 1)
            ->where('endorsement_stage', $endorsement->document_no)
            ->where('status', 'ACT')
            ->orderBy('id')
            ->get()
            ->map(function($item) use ($endorsement) {

                $subReinsuranceData = ReinsuranceData::where('policy_id', $endorsement->id)
                    ->where('detail_id', $item->detail_id)
                    ->where('endorsement_stage', $endorsement->document_no)
                    ->where('parent_code', $item->treaty_code)
                    ->where('lvl', 2)
                    ->where('status', 'ACT')
                    ->orderBy('id')
                    ->get()
                    ->map(function($item) {
                        $item->participant = ReinsurancePartner::getPartnerNameByCode($item->treaty_code);

                        return $item;
                    });

                $item->sub_reinsurance_data = $subReinsuranceData;
                $item->reinsurance_type = ReinsuranceData::getReinsuranceType($item->product_code, $item->treaty_code);
                $item->participant = ReinsurancePartner::getPartnerNameByCode($item->treaty_code);

                $item->vehicle_value = ReinsuranceData::getVehicleValue($item->detail_id);
                $item->vehicle_total_premium = ReinsuranceData::getVehicleTotalPremium($item->detail_id);

                return $item;
            })
            ->groupBy('detail_id');
    }
}

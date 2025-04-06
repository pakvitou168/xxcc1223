<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutoRequest;
use App\Models\Cover\Cover;
use App\Models\CustomerManagement\Customer;
use App\Models\Deductible\DeductibleDetail;
use App\Models\Insurance\Auto;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\Endorsement\Endorsement;
use App\Models\Insurance\Quotation;
use App\Models\Insurance\QuotationView;
use App\Models\Make\MakeModel;
use App\Models\Product\Product;
use App\Models\UserManagement\User\UserFile;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Address\AddressCode;
use App\Models\CustomerManagement\Country;

class AutoController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Auto::class, 'auto');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            QuotationView::with(
                [
                    'quotation' => function ($query) {
                        $query->select(
                            'data_id',
                            'quotation_no',
                            'approved_at',
                            'approved_status',
                            'approved_reason',
                            'accepted_status',
                            'accepted_reason'
                        );
                    }
                ]
            )->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AutoRequest $request)
    {
        $auto = new Auto();

        $this->assignAutoFields($request, $auto);

        if ($auto->save()) {
            $this->storeEndorsementClauses($request, $auto);
            $this->storeJointDetails($request, $auto);

            return response([
                'autoData' => [
                    'product_code' => $auto->product_code,
                    'request_id' => $auto->id
                ],
                'id' => $auto->id,
                'message' => 'Auto Quotation is successfully created.'
            ]);
        }
    }

    private function generatePolicyWording($productCode)
    {
        return collect(DB::select("select * from ins_get_policy_wording_version('" . $productCode . "')"))->first()->ins_get_policy_wording_version;
    }

    private function assignAutoFields($request, $auto)
    {
        $auto->data_type = $request->data_type;
        $auto->product_code = $request->product_code;
        $auto->branch_code = '000';
        $auto->customer_no = $request->customer_no;
        $auto->joint_status = $request->joint_status;
        $auto->insured_name = Str::upper($request->insured_name);
        $auto->insured_name_kh = Str::upper($request->insured_name_kh);
        $auto->calc_option = $request->calc_option;
        $auto->insurance_period_type = $request->insurance_period_type;
        $auto->effective_date_from = $request->effective_date_from;
        $auto->effective_date_to = $request->effective_date_to;
        $auto->negotiation_rate = $request->negotiation_rate;
        $auto->business_code = $request->business_code;
        $auto->sale_channel = $request->sale_channel;
        $auto->commission_rate = $request->commission_rate;
        $auto->handler_code = $request->handler_code;
        $auto->remark = $request->remark;
        $auto->warranty = $request->warranty;
        $auto->memorandum = $request->memorandum;
        $auto->subjectivity = $request->subjectivity;

        if ($request->policy_wording_version) {
            $auto->policy_wording_version = $request->policy_wording_version;
        } else {
            $auto->policy_wording_version = $this->generatePolicyWording($request->product_code);
        }

        $this->assignAutoDetailFieldGeneralEndorsement($request);
    }

    private function assignAutoDetailFieldGeneralEndorsement($request)
    {
        $vehicles = collect($request->vehicles)->filter(function ($item) {
            $item = (object) $item;

            return optional($item)->status !== 'DEL';
        });

        $vehicles->each(function ($item) {
            $item = (object) $item;

            $autoDetail = AutoDetail::firstOrCreate(['id' => optional($item)->id]);

            $autoDetail->plate_no = optional($item)->plate_no;
            $autoDetail->chassis_no = optional($item)->chassis_no;
            $autoDetail->engine_no = optional($item)->engine_no;

            $autoDetail->save();
        });
    }

    private function storeEndorsementClauses($request, $auto)
    {
        $existingEndorsement = $this->getInsuranceClauseIds($auto->id, 'ENDORSEMENT');
        if (!$existingEndorsement->isEmpty()) {
            Auto::find($auto->id)->insuranceClauses()->detach($existingEndorsement);
        }
        Auto::find($auto->id)->insuranceClauses()->attach($request->endorsement_clause, ['status' => 'ACT']);

        $existingExclusion = $this->getInsuranceClauseIds($auto->id, 'EXCLUSION');
        if (!$existingExclusion->isEmpty()) {
            Auto::find($auto->id)->insuranceClauses()->detach($existingExclusion);
        }
        Auto::find($auto->id)->insuranceClauses()->attach($request->general_exclusive, ['status' => 'ACT']);
    }

    private function storeJointDetails($request, $auto)
    {
        $auto->jointAccountDetails()->delete();

        if (!$request->joint_details) return;

        $details = collect($request->joint_details)->map(function ($item) use ($auto) {
            $item = (object) $item;

            return [
                'customer_no' => $item->customer_no,
                'product_line_code' => 'AUTO',
                'product_code' => $auto->product_code,
                'data_id' => $auto->id,
                'joint_level' => $item->joint_level,
                'permission' => $item->permission
            ];
        });
        $auto->jointAccountDetails()->createMany($details);
    }

    private function getInsuranceClauseIds($autoId, $clauseType)
    {
        return Auto::find($autoId)->insuranceClauses()->where('clause_type', $clauseType)->get()->pluck('pivot.clause_id');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Insurance\Auto  $auto
     * @return \Illuminate\Http\Response
     */
    public function show(Auto $auto)
    {
        $endorsementClause = $this->getInsuranceClauseIds($auto->id, 'ENDORSEMENT')->map(function ($item) {
            return intval($item);
        });
        $generalExclusion = $this->getInsuranceClauseIds($auto->id, 'EXCLUSION')->map(function ($item) {
            return intval($item);
        });

        $auto->customer_type = Customer::getCustomerTypeByCustomerNo($auto->customer_no);
        $auto->endorsement_clause = $endorsementClause;
        $auto->general_exclusive = $generalExclusion;

        // Get all vehicle if it is an endorsement
        $isEndorsement = $auto->endorsement_type != null;
        $auto->vehicles = $this->showVehicles($auto, $isEndorsement);

        $auto->joint_details = $this->showJointAccountDetails($auto);

        // if is endorsement
        if ($auto->policy) {
            $auto->endorsement_description = $auto->policy->endorsement_description;
        }

        // if quotation is generated
        if ($auto->quotation) {
            $auto->document_no = $auto->quotation->document_no;
        }

        return $auto;
    }

    private function showVehicles($auto, $withTrash = false)
    {
        if ($withTrash) {
            $vehicles = $auto->allAutoDetails()->orderBy('id', 'ASC')->get();
        } else {
            $vehicles = $auto->autoDetails()->orderBy('id', 'ASC')->get();
        }

        return $vehicles->map(function ($item) {
            $covers = collect(explode(',', $item->selected_cover_pkg));

            $model = MakeModel::select('make_id', 'model')->find($item->model_id);
            if (isset($model)) {
                $make = $model->make()->select('id', 'make')->first();
            }

            if (isset($make)) {
                $item->make = $make->id;
                $item->make_name = $make->make;
                $item->model = $item->model_id;
                $item->model_name = $model->model;
            }

            // $item->negotiation_rate = $item->negotiation_rate;
            // $item->surcharge = $item->surcharge;
            // $item->discount = $item->discount;
            // $item->ncd = $item->ncd;
            $item->passenger_tonnage = $item->passenger ?? $item->tonnage;
            $item->optional_covers = $covers;

            // Cover package component value needs array
            $item->cover_pkg_id = $item->cover_pkg_id ? [$item->cover_pkg_id] : [];

            //Capitalize Plat No
            $item->plate_no = strtoupper($item->plate_no);

            return $item;
        });
    }

    private function showJointAccountDetails($auto)
    {
        return $auto->jointAccountDetails()->with('customer:customer_no,customer_type')->get()
            ->map(function ($item) {
                $item->customer_type = optional($item->customer)->customer_type;
                return $item;
            });
    }

    /**
     * Display the specified resource in detail.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDetail($id)
    {
        $auto = $this->autoDetail($id);

        // Check if has passenger or tonnage
        $auto->has_passenger_tonnage = $this->hasPassengerOrTonnage($auto->product_code);

        $covers = [];
        foreach ($auto->vehicles as $vehicle) {
            $covers = array_unique(array_merge($covers, explode(',', $vehicle->selected_cover_pkg)));
        }

        $coverage = [];
        $coverage = Cover::listByProductAndCode($auto->product_code, $covers)
            ->map(function ($item) {
                $item->html_detail = nl2br($item->detail);
                $item->html_detail_kh = nl2br($item->detail_kh);
                $item->html_detail_zh = nl2br($item->detail_zh);

                return $item;
            });

        $deductibles = [];
        if ($auto->vehicles->count() === 1) {
            $deductibles = DeductibleDetail::listByDetailAndProduct($auto->vehicles[0]->id, $auto->product_code);
        }

        return [
            'auto' => $auto,
            'coverage' => $coverage,
            'deductibles' => $deductibles,
            'signature' => $this->getSignature($auto)
        ];
    }

    private function hasPassengerOrTonnage($productCode)
    {
        $specification = Product::getProductSpecificationByCode($productCode);

        return $specification === 'TONNAGE' || $specification === 'PASSENGER';
    }

    // Get signature from user that approved the quote
    private function getSignature(Auto $auto)
    {

        if (!$auto->quotation && !optional($auto->quotation)->approved_by || optional($auto->quotation)->approved_status != 'APV') return null;

        return UserFile::select('file_url')->where('user_id', $auto->quotation->approved_by)->where('file_type', 'SIGNATURE')->first();
    }

    private function autoDetail($id)
    {
        $auto = Auto::with([
            'customers:customer_no,customer_type',
            'customer:customer_no,address_en,name_en,village_en,country_code',
            'product:code,name,name_kh,limitation_to_use_en,limitation_to_use_kh',
            'quotation' => function ($query) {
                $query->select(
                    'id',
                    'data_id',
                    'quotation_no',
                    'document_no',
                    'created_at',
                    'approved_status',
                    'approved_by',
                    'accepted_status',
                    'accepted_by'
                )->with('policy:quotation_id');
            },
        ])->find($id);

        $customer = Customer::with('customerClassification:cust_classification,description,description_kh,description_zh')
            ->select('customer_type', 'cust_classification', 'postal_code')
            ->where('customer_no', $auto->customer_no)
            ->first();
        $addressData = AddressCode::select('province', 'district', 'commune')->where('postal_code', $customer->postal_code)->first();
        $country = Country::select('description')->where('country_code', $auto->customer->country_code)->value('description');

        $auto->customer_type = $customer->customer_type;
        $auto->customer_classification = optional($customer->customerClassification)->description;
        $auto->customer_classification_kh = optional($customer->customerClassification)->description_kh;
        $auto->endorsement_clause = $this->getInsuranceClauses($auto->id, 'ENDORSEMENT');
        $auto->general_exclusive = $this->getInsuranceClauses($auto->id, 'EXCLUSION');
        $auto->vehicles = $this->showVehicles($auto);
        $auto->negotiation_rate = $auto->negotiation_rate;
        $auto->addressData = $addressData;
        $auto->country = $country;

        if ($auto->updated_by)
            $auto->issued_by = $auto->issuedByName($auto->updated_by);
        else if ($auto->created_by)
            $auto->issued_by = $auto->issuedByName($auto->created_by);
        else
            $auto->issued_by = null;

        return $auto;
    }

    private function getInsuranceClauses($autoId, $clauseType)
    {
        return Auto::find($autoId)->insuranceClauses()
            ->select('clause', 'clause_kh', 'clause_zh', 'ins_insurance_clause.id')
            ->where('clause_type', $clauseType)
            ->orderBy('sequence')
            ->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Insurance\Auto  $auto
     * @return \Illuminate\Http\Response
     */
    public function update(AutoRequest $request, Auto $auto)
    {
        $this->assignAutoFields($request, $auto);

        // Manually update updated_at for showing issue date
        $auto->updated_at = now();

        if ($auto->save()) {
            $this->storeEndorsementClauses($request, $auto);
            $this->storeJointDetails($request, $auto);

            return [
                'autoData' => [
                    'product_code' => $auto->product_code,
                    'request_id' => $auto->id
                ],
                'message' => 'Auto Quotation is successfully updated.'
            ];
        }
    }

    /**
     * Update endorsement
     *
     */
    public function saveEndorsementGeneral(AutoRequest $request, Auto $auto)
    {
        // Update endorsement description
        $endorsement = $auto->policy;
        $endorsement->endorsement_description = $request->endorsement_description;
        $endorsement->save();

        // If is general endorsement
        if ($auto->endorsement_type === 'GENERAL') {
            $this->assignAutoFields($request, $auto);

            // Manually update updated_at for showing issue date
            $auto->updated_at = now();

            if ($auto->save()) {
                $this->storeEndorsementClauses($request, $auto);
                $this->storeJointDetails($request, $auto);

                return [
                    'success' => true,
                    'autoData' => [
                        'product_code' => $auto->product_code,
                        'request_id' => $auto->id
                    ],
                    'message' => 'Endorsement is successfully updated.'
                ];
            }
        }
    }

    public function saveCancelPolicyEndorsement(Request $request, Endorsement $endorsement)
    {
        $vehicles = $request->vehicles;

        collect($vehicles)->each(function ($item) use ($endorsement) {
            $item = (object) $item;
            $autoDetail = AutoDetail::find($item->id);

            $autoDetail->endorsement_e_date = optional($item)->endorsement_e_date ?? optional($endorsement->auto)->endorsement_e_date;
            $autoDetail->refund_option = $item->refund_option;

            // Only refund type custom has custom refund amount
            if ($item->refund_option === 'CUSTOM')
                $autoDetail->custom_refund_amount = optional($item)->custom_refund_amount * (-1);
            else
                $autoDetail->custom_refund_amount = null;

            // Save vehicle
            if ($autoDetail->save()) info($item->id . ' saved');
        });

        // Update endorsement description
        $endorsement->endorsement_description = $request->endorsement_description;
        $endorsement->save();

        $response = $this->cancelPolicyEndorsement($endorsement);
        if (optional($response[0])->code === 'SUC')
            return [
                'success' => true,
                'message' => 'Policy cancelled successfully.'
            ];
    }

    public function saveCancelPolicyEndorsementAllVehicles(Request $request, Endorsement $endorsement)
    {
        $endorsement->endorsement_description = $request->endorsement_description;
        $endorsement->save();
        AutoDetail::where('master_data_id', $endorsement->data_id)->get()->each(function ($item) use ($request) {
            $item->endorsement_e_date = $request->endorsement_e_date;
            $item->refund_option = $request->refund_option;
            if ($item) info($item . ' saved');
        });
        $response = $this->cancelPolicyEndorsement($endorsement);
        if (optional($response[0])->code === 'SUC')
            return [
                'success' => true,
                'message' => 'Policy cancelled successfully.'
            ];
    }

    private function cancelPolicyEndorsement($endorsement)
    {
        $params = [
            $endorsement->id,
            optional($endorsement->auto)->product_code,
            'POLICY',
            $endorsement->data_id,
            auth()->id(),
        ];
        return DB::select("select * from ins_do_auto_endor_policy_cancellation(?,?,?,?,?)", $params);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Insurance\Auto  $auto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Auto $auto)
    {
        $auto->status = 'DEL';

        if ($auto->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }

    /**
     * Clone auto record
     *
     */
    public function clone(Auto $auto)
    {
        if (!$auto->quotation) return;
        $params = [
            $auto->quotation->id,
            auth()->id(),
        ];
        $cloned = DB::select("select * from ins_prod_auto_gen_new_quotation_version(?,?)", $params);
        if (!empty($cloned) && $cloned[0]->value) {
            /**
             * Getting master_auto_id for the newly cloned quotation in order to generate total premium
             */
            $newAutoId = Quotation::where('id', $cloned[0]->value)->value('data_id');

            return response()->json([
                'success' => true,
                'message' => 'Successfully generated new version!',
                'newAutoId' => $newAutoId,
            ]);
        }

        return response()->json([
            'message' => 'Failed to generate new version!',
        ], 400);
    }

    public function canGenerateNewVersion(Auto $auto)
    {

        $isAcceptedQuote = optional($auto->quotation)->accepted_status === 'ACP';

        // Customer has accepted the quote
        if ($isAcceptedQuote) return false;

        if ($auto->hasPendingPolicy()) return false;

        return true;
    }

    public function checkValidationFunctions(Request $request)
    {
        $params = [
            $request->id,
            $request->data_id,
            $request->detail_id,
            $request->product_line,
            $request->product_code,
            $request->request_type,
            $request->p_type,
            Auth::user()->id,
            $request->group_type,
            '',
            '',
            ''
        ];
        return DB::select("select * from ins_prod_check_validation(?,?,?,?,?,?,?,?,?,?,?,?)", $params);
    }

    public function generateOverallPremium(Request $request)
    {
        $params = [
            $request->product_code,
            $request->request_type,
            $request->data_id,
            Auth::user()->id,
        ];
        return DB::select("select * from ins_do_auto_calc_premium(?,?,?,?)", $params);
    }

    public function generateOverallDeductible(Request $request)
    {
        $params = [
            $request->product_code,
            $request->request_type,
            $request->data_id,
            Auth::user()->id,
        ];
        return DB::select("select * from ins_prod_auto_generate_deductible(?,?,?,?)", $params);
    }

    public function generateSinglePremium(Request $request)
    {
        $params = [
            $request->product_code,
            $request->request_type,
            $request->data_id,
            $request->detail_id,
            Auth::user()->id,
        ];
        // dd($params);
        return DB::select("select * from ins_do_auto_calc_premium_single(?,?,?,?,?)", $params);
    }

    public function generateQuotationNo(Request $request)
    {
        $params = [
            $request->product_line,
            $request->product_code,
            $request->request_type,
            $request->data_id,
            Auth::user()->id,
            $request->is_checked,
        ];
        $response = DB::select("select * from ins_prod_auto_generate_new_quotation_no(?,?,?,?,?,?)", $params);
        if ($response[0]->status == 'SUC') {
            return response([
                'success' => true,
                'message' => 'Quotation Number has been generated successfully',
                'quotation_no' => $response[0]->quote_no,
            ]);
        } else {
            return response([
                'success' => false,
                'message' => $response[0]->message,
            ]);
        }
    }

    public function deleteVehicles(Request $request)
    {
        $response = null;
        foreach ($request->detail_id_list as $detail_id) {
            $params = [
                $request->policy_id,
                $request->data_id,
                $detail_id,
                $request->request_type,
                Auth::user()->id,
            ];
            $response = DB::select("select * from ins_prod_remove_vehicle(?,?,?,?,?)", $params);
        }
        return $response;
    }

    public function approve(Request $request, Auto $auto)
    {
        $this->validate($request,[
            'approved_status' => 'required',
            'approved_reason' => 'required'
        ],[
            'approved_status.required' => 'Status is required',
            'approved_reason.required' => 'Reason is required'
        ]);
        if (!$auto->quotation) return response('Not found', 404);

        $this->authorize('approve', Quotation::class);
        $statusApproved = 'APV';
        $statusPending = 'PND';

        $auto->quotation->approved_status = $request->post('approved_status');
        $auto->quotation->approved_reason = $request->post('approved_reason');

        if ($this->checkMakerAndApprover($auto)) {
            abort(403, "You can not approve your own request.");
        }

        // Set accepted_status to Pending if approve
        if ($auto->quotation->approved_status === $statusApproved)
            $auto->quotation->accepted_status = $statusPending;

        $auto->quotation->approved_at = now();
        $auto->quotation->approved_by = auth()->id();

        if ($auto->quotation->save()) {
            return response([
                'success' => true,
                'message' => 'Approved successfully'
            ]);
        }
        return response('Something went wrong!', 500);
    }

    public function checkMakerAndApprover(Auto $auto)
    {
        return $auto->updated_by ? $auto->updated_by == auth()->id() : $auto->created_by == auth()->id();
    }

    public function accept(Request $request, Auto $auto)
    {
        if (!$auto->quotation) return response('Not found', 404);

        $this->authorize('accept', Quotation::class);
        
        $auto->quotation->accepted_status = $request->post('accepted_status');
        $auto->quotation->accepted_reason = $request->post('accepted_reason');
        $auto->quotation->accepted_at = now();
        $auto->quotation->accepted_by = auth()->id();

        if ($auto->quotation->save()) {
            return response([
                'success' => true,
                'message' => 'Accepted successfully'
            ]);
        }
        return response('Something went wrong!', 500);
    }

    public function proceedToPolicy(Auto $auto)
    {
        if (!$auto->quotation) return response('Not found', 404);
        $params = [
            $auto->quotation->id,
            auth()->id(),
        ];
        $generated = DB::select("select * from ins_prod_auto_generate_new_policy(?,?)", $params);

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Proceeded successfully!',
                'data' => $generated
            ]);
        }
    }
    /**
     * Update Issue Date mainly from Deductible Submission
     */
    public function updateIssuedDate(Auto $auto)
    {
        // Manually update updated_at for showing issue date
        $auto->updated_at = now();

        if ($auto->save()) {
            return [
                'success' => true,
                'message' => 'Issue Date is successfully updated.'
            ];
        }
    }

    public function reviseApprovalStatus(Auto $auto)
    {
        // Check if user is authorized for the revision
        $this->authorize('revise', Auto::class);

        $quotation = $auto->quotation;
        $quotation->approved_status = 'PND';
        $quotation->approved_reason = null;
        if ($quotation->save()) {
            return [
                'success' => true,
                'message' => 'Record is revised.'
            ];
        }
    }

    public function reviseAcceptanceStatus(Auto $auto)
    {
        // Check if user is authorized fthe revision
        $this->authorize('revise', Auto::class);

        $quotation = $auto->quotation;
        $quotation->accepted_status = 'PND';
        $quotation->accepted_reason = null;
        if ($quotation->save()) {
            return [
                'success' => true,
                'message' => 'Record is revised.'
            ];
        }
    }

    public function getPolicyId(Auto $auto)
    {
        return optional($auto->policy)->id;
    }

    // override trait
    public function actionButtons($item, $permissions)
    {
        if ($permissions) {
            $canView = $permissions['VIEW'];
            $canUpdate = $permissions['UPDATE'];
            $canDelete = $permissions['DELETE'];
            $canRevise = $permissions['REVISE'];
        } else {
            $canView = $canUpdate = $canDelete = true;
        }

        $itemStatus = '';
        $showViewButton = $canView ? '' : 'hidden';
        $showUpdateButton = $canUpdate ? '' : 'hidden';
        $showDeleteButton = $canDelete ? '' : 'hidden';
        $showReviseButton = $canRevise ? '' : 'hidden';

        if (!is_null($item->quotation))
            if ($item->quotation->approved_status == 'REJ' || $item->quotation->accepted_status == 'REJ')
                $itemStatus = 'REJ';

        $actionBtn = $itemStatus == 'REJ' ? '<svg title="Revise" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>' : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
        $revise = $itemStatus == 'REJ' ? 'title="Revise"' : '';
        return '
            <div class="flex justify-center">
                <a class="view flex items-center mr-1 text-sm ' . $showViewButton . '" href="javascript:;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </a>
                <a ' . $revise . ' class="edit flex items-center mr-1 ' . ($itemStatus == 'REJ' ? $showReviseButton : $showUpdateButton) . '" href="javascript:;">' . $actionBtn . '</a>
                <a class="delete flex items-center text-theme-6 ' . $showDeleteButton . '" href="javascript:;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </a>
            </div>
        ';
    }

    public function deleteVehiclesManually(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->detail_id_list as $detail_id) {
                AutoDetail::find($detail_id)->delete();
            }
            DB::commit();
            return [
                'success' => true,
                'message' => 'Deleted successfully.'
            ];
        } catch (\Exception $ex) {
            DB::rollBack();
            report($ex);
            return response([
                'message' => 'Error'
            ], 500);
        }
    }
}

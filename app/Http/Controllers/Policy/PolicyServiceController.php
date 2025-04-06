<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\BusinessManagement\BusinessChannel;
use App\Models\Insurance\PolicyCommissionData;
use App\Models\Insurance\ReinsuranceData;
use App\Models\RefEnum;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReinsuranceExport;
use App\Exports\PolicyRegisterExport;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\BasePolicy;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;
use Illuminate\Http\Request;
use App\Models\UserManagement\User\UserFile;
use App\Models\BankInformation\BankInformation;
use App\Models\Insurance\Invoice\InvoiceNote;
use App\Models\Insurance\Policy;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Quotation\AutoController;
use App\Models\CoverPackage\CoverPackage;
use App\Models\Deductible\DeductibleDetail;
use App\Models\Insurance\Auto;
use App\Models\CustomerManagement\Country;
use App\Models\Address\AddressCode;

class PolicyServiceController extends Controller
{
    public function listBusinessTypes() {
        return RefEnum::select('enum_id', 'name')
            ->where('group_id', 'POLICY_CONFIG')
            ->where('type_id', 'BUSINESS_TYPE')
            ->select('name AS label', 'enum_id AS value')->get();
    }

    public function listPolicyTypes() {
        return RefEnum::select('enum_id', 'name')
            ->where('group_id', 'POLICY_CONFIG')
            ->where('type_id', 'POLICY_TYPE')
            ->select('name AS label', 'enum_id AS value')->get();
    }

    public function generateCommissionData($policyId) {
        $generated = DB::select("select * from ins_generate_commission_data(". $policyId .")");

        if (!empty($generated)) {
            return response()->json([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }

    public function getCommissionData($policyId) {
        return PolicyCommissionData::where('policy_id', $policyId)
            ->where('status', 'ACT')
            ->orderBy('detail_id')
            ->get();
    }

    public function getCommissionDataByVehicle($detail_id) {
        return PolicyCommissionData::where('detail_id', $detail_id)
            ->where('status', 'ACT')
            ->first();
    }

    public function updateCommissionData($policyId, Request $request) {
        $request->validate([
            'premium_tax_fee_rate' => 'required|min:0',
            'commission_rate' => 'required|min:0',
            'witholding_tax_rate' => 'required|min:0',
        ]);

        $commission_data = PolicyCommissionData::where('policy_id', $policyId)
            ->where('detail_id', $request->detail_id)
            ->where('status', 'ACT')
            ->first();

        $commission_data->premium_tax_fee_rate = $request->premium_tax_fee_rate;
        $commission_data->commission_rate = $request->commission_rate;
        $commission_data->witholding_tax_rate = $request->witholding_tax_rate;
        $commission_data->save();

        $generated = DB::select("select * from ins_regen_commission_data(". $policyId .")");

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    public function isCommissionDataAvailable($policyId){
        return PolicyCommissionData::where('policy_id', $policyId)->where('status', 'ACT')->first() ? true : false;
    }

    public function getBusinessNameByBusinessCode($businessCode) {
        $businessName = BusinessChannel::select('business_code', 'full_name')
            ->where('business_code', $businessCode)
            ->where('status', 'ACT')
            ->first();

            return $businessName->business_code . ' - ' . $businessName->full_name;
    }

    public function generateReinsuranceShare($policyId) {
        $generated = DB::select("select * from ins_generate_reinsurance_share(". $policyId .")");
        info("reinsurance generating",['res' => $generated]);
        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }

    public function generateReinsuranceData($policyId) {
        $generated = DB::select("select * from ins_generate_reinsurance_data(". $policyId .")");

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }

    public function getReinsuranceData($policyId) {
        $deletedVehicles = ReinsuranceData::where('policy_id', $policyId)->where('endorsement_state','DELETION')->distinct()->pluck('detail_id')->toArray();
        return ReinsuranceData::where('policy_id', $policyId)
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->orderBy('id')
            ->get()
            ->filter(function($item) use ($deletedVehicles) {
                if($deletedVehicles){
                    if(in_array($item->detail_id, $deletedVehicles))
                        if($item->endorsement_state != 'DELETION')
                            return false;
                }
                return true;
            })
            ->map(function($item) use ($policyId) {
                $subReinsuranceData = ReinsuranceData::where('policy_id', $policyId)
                    ->where('detail_id', $item->detail_id)
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

    public function checkIfShareUnderLimit($policyId, $detailId) {
        $hasDeletedVehicle = ReinsuranceData::where('policy_id', $policyId)
                                        ->where('detail_id', $detailId)
                                        ->where('endorsement_state','DELETION')
                                        ->first() ? true : false;
        if($hasDeletedVehicle)
            $totalShares = ReinsuranceData::getTotalSharesDeletedVehicle($policyId, $detailId);
        else
            $totalShares = ReinsuranceData::getTotalShares($policyId, $detailId);

        // If share is equals to 1 (100%)
        if ($totalShares == 1)
            return [
                'isUnderLimit' => false,
            ];

        $remainingShare = round(1 - $totalShares, 3);

        return [
            'isUnderLimit' => true,
            'totalShare' => $totalShares,
            'remainingShare' => $remainingShare,
        ];
    }

    public function isPolicyReinsuranceCompleted(BasePolicy $policy) {
        return ReinsuranceData::isReinsuranceCompleted($policy->id);
    }

    public function isPolicyConfigurationCompleted(BasePolicy $policy) {
        return $policy->isPolicyConfigurationCompleted();
    }

    public function listAutoEndorsementTypes() {
        return RefEnum::listAutoEndorsementTypes();
    }

    public function listTreatyCodes() {
        return ReinsurancePartner::getPartnerNamesForForm();
    }

    public function listPartnerGroupCodes(){
        return ReinsurancePartnerGroup::getAllPartnerGroupNames();
    }

    public function exportReinsurance($id, $productCode){
        return Excel::download(new ReinsuranceExport($id, $productCode), 'Reinsurance.xlsx');
    }

    public function exportPoilcy(Request $request){
        return Excel::download(new PolicyRegisterExport($request->route('issue_date_from'), $request->route('issue_date_to'), ReinsurancePartner::getAllPartnerNames()), 'Policy.xlsx');
    }

    public function checkCanGenerateEndorsement(BasePolicy $policy) {
        if ($policy->status !== 'APV') return false;

        // If is policy cancellation endorsement
        if ($policy->auto->endorsement_type === 'CANCELLATION') return false;
        
        return BasePolicy::isLatestEndorsement($policy);
    }

    // Use approved_status as submit_status
    public function updateSubmitStatus(BasePolicy $policy, Request $request){
        if($policy->status !== 'PND'){
            return response()->json(['success' => false,'message' => 'Policy has already been updated'],500);
        }
        if($policy->approved_status !== $request->status){
            $policy->approved_status = $request->status;

            if ($policy->save()) {
                return [
                    'success' => true,
                    'message' => 'Policy submit status has been updated to '.$request->post('status')
                ];
            }
        }
    }

    public function getSignatureByPolicy(BasePolicy $policy) {
        return [
            'signature' => UserFile::select('file_url')->where('user_id', $policy->approved_by)->where('file_type','SIGNATURE')->first()
        ];
    }

    /**
     * Print Policy Invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Insurance\Policy  $policy
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice($id, $withSignature = null)
    {
        $invoice = InvoiceNote::getInvoiceData($id);

        $bank_list = BankInformation::where('status', 'ACT')->where('default', true)->get();

        $policy = Policy::find($id);
        if($policy->status == 'APV' && $withSignature){
            $signature = UserFile::select('file_url')->where('user_id', $policy->approved_by)->where('file_type','SIGNATURE')->first();
            if($signature)
                if(!$signature->file_url)
                    $signature = null;
        }
        $isEpolicy = $policy->quotation_id === null && $policy->status === 'APV';
        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Invoice');
        $pdf->loadView('pdf.policies.invoice', ['invoice' => $invoice, 'bank_list' => $bank_list,'isEpolicy' => $isEpolicy, 'signature' => $signature ?? null]);

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('margin-top', 27);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);

        return $pdf->stream($invoice->inv_cdn_no.'.pdf');
    }

    public function downloadPolicySchedule(Request $request, Policy $policy, $lang) {
        App::setLocale($lang);

        // TODO: move to service or model class
        $autoData = (new AutoController)->showDetail($policy->data_id);
        $autoData['hasLetterHead'] = $request->get('letterhead');

        $documentNo = $policy->document_no;
        $autoData['documentNo'] = $documentNo;

        if($policy->status == 'APV'){
            $autoData['signature'] = UserFile::select('file_url')->where('user_id', $policy->approved_by)->first();
            // fix error when there is a record but empty file_url
            if($autoData['signature'])
                if(!$autoData['signature']['file_url'])
                    $autoData['signature'] = null;
        }
        $isEpolicy = $policy->quotation_id === null && $policy->status === 'APV';
        $autoData['isEpolicy'] = $isEpolicy;
        $pdf = App::make(abstract: 'snappy.pdf.wrapper');

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
                'hasLetterHead' => $request->get('letterhead')
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => $request->get('letterhead')
            ]),
        ]);

        $pdf->loadView('pdf.policies.auto', $autoData);

        return $pdf->stream($documentNo . '.pdf');
    }

    public function downloadAutoCertificate($id) {
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'Certificate');

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('zoom', 1);
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $data = $this->getAutoCertificateData($id);

        $pdf->loadView('pdf.policies.certificate', $data);

        return $pdf->stream('certificate.pdf');
    }

    private function getAutoCertificateData($id) {
        $policy = Policy::select('id', 'document_no', 'data_id')->find($id);

        $auto = Auto::with(['customer' => function($query) {
                $query->select('customer_no', 'address_en', 'name_en','village_en','country_code', 'postal_code');
            }])
            ->select('id', 'customer_no', 'insured_name', 'effective_day', 'effective_date_from', 'effective_date_to')
            ->find($policy->data_id);

        $addressData = AddressCode::select('province', 'district', 'commune')->where('postal_code', $auto->customer->postal_code)->first();
        $auto->addressData = $addressData;

        $country = Country::select('description')->where('country_code', $auto->customer->country_code)->value('description');
        $auto->country = $country;

        $autoDetails = AutoDetail::with(['makeModel' => function($query) {
                $query->with(['make' => function($query) {$query->select('id', 'make');}])
                    ->select('id', 'model', 'make_id');
            }])
            ->select('id', 'product_code', 'master_data_id','plate_no','model_id','chassis_no','engine_no','selected_cover_pkg', 'cover_pkg_id')
            ->where('master_data_id', $auto->id)
            ->orderBy('id')
            ->get();

        $autoDetails = $autoDetails->map(function($item) {
            $deductibles = DeductibleDetail::listByDetailAndProduct($item->id, $item->product_code);
            $item->deductibles = $deductibles;

            $coverArr = explode(',', $item->selected_cover_pkg);

            $covers = CoverPackage::getCoverPackageWithRemainingCovers($item->cover_pkg_id, $coverArr);
            $item->covers = $covers;

            return $item;
        });

        return [
            'policy' => $policy,
            'auto' => $auto,
            'auto_details' => $autoDetails
        ];
    }
}

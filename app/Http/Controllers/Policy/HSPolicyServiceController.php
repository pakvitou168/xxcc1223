<?php

namespace App\Http\Controllers\Policy;

use App\Exports\HS\PolicyRegisterExport;
use App\Exports\HS\ReinsuranceExport;
use App\Exports\HS\View\PolicyRegister;
use App\Exports\InsuredPersonsExport;
use App\Http\Controllers\Controller;
use App\Models\BusinessManagement\BusinessChannel;
use App\Models\HS\Insurance\PolicyCommissionData;
use App\Models\HS\Insurance\ReinsuranceData;
use App\Models\RefEnum;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;
use Illuminate\Http\Request;
use App\Models\UserManagement\User\UserFile;
use App\Models\BankInformation\BankInformation;
use Illuminate\Support\Facades\App;
use App\Models\CoverPackage\CoverPackage;
use App\Models\Deductible\DeductibleDetail;
use App\Models\CustomerManagement\Country;
use App\Models\Address\AddressCode;
use App\Models\CustomerManagement\Customer;
use App\Models\HS\DataDetail;
use App\Models\HS\DataMaster;
use App\Models\HS\Invoice\InvoiceNote;
use App\Models\HS\Policy;
use App\Services\HS\PolicyService;
use KhmerDateTime\KhmerDateTime;

class HSPolicyServiceController extends Controller
{
    public function __construct(private PolicyService $hsPolicyService)
    {
    }

    public function listBusinessTypes()
    {
        return RefEnum::select('enum_id', 'name')
            ->where('group_id', 'POLICY_CONFIG')
            ->where('type_id', 'BUSINESS_TYPE')
            ->pluck('name', 'enum_id');
    }

    public function listPolicyTypes()
    {
        return RefEnum::select('enum_id', 'name')
            ->where('group_id', 'POLICY_CONFIG')
            ->where('type_id', 'POLICY_TYPE')
            ->pluck('name', 'enum_id');
    }

    public function generateCommissionData($policyId)
    {
        return $this->hsPolicyService->generateCommissionData($policyId);
    }

    public function getCommissionData($policyId)
    {
        return PolicyCommissionData::where('policy_id', $policyId)
            ->where('status', 'ACT')
            ->first();
    }

    public function updateCommissionData($policyId, Request $request)
    {
        $request->validate([
            'premium_tax_fee_rate' => 'required|min:0',
            'commission_rate' => 'required|min:0',
            'witholding_tax_rate' => 'required|min:0',
        ]);

        $commission_data = PolicyCommissionData::where('policy_id', $policyId)
            ->where('status', 'ACT')
            ->first();

        $commission_data->premium_tax_fee_rate = $request->premium_tax_fee_rate;
        $commission_data->commission_rate = $request->commission_rate;
        $commission_data->witholding_tax_rate = $request->witholding_tax_rate;
        $commission_data->save();

        $generated = DB::select("select * from ins_hs_regen_commission_data(" . $policyId . ")");

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    public function isCommissionDataAvailable($policyId)
    {
        return PolicyCommissionData::where('policy_id', $policyId)->where('status', 'ACT')->first() ? true : false;
    }

    public function getBusinessNameByBusinessCode($businessCode)
    {
        $businessName = BusinessChannel::select('business_code', 'full_name')
            ->where('business_code', $businessCode)
            ->where('status', 'ACT')
            ->first();

        return $businessName->business_code . ' - ' . $businessName->full_name;
    }

    public function generateReinsuranceShare($policyId)
    {
        $generated = DB::select("select * from ins_hs_generate_reinsurance_share(" . $policyId . ")");

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }

    public function generateReinsuranceData($policyId)
    {
        $generated = DB::select("select * from ins_hs_generate_reinsurance_data(" . $policyId . ")");

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }

    public function getReinsuranceData($policyId)
    {
        return ReinsuranceData::where('policy_id', $policyId)
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->orderBy('id')
            ->get()
            ->map(function ($item) use ($policyId) {
                $subReinsuranceData = ReinsuranceData::where('policy_id', $policyId)
                    ->where('data_id', $item->data_id)
                    ->where('parent_code', $item->treaty_code)
                    ->where('lvl', 2)
                    ->where('status', 'ACT')
                    ->orderBy('id')
                    ->get()
                    ->map(function ($item) {
                        $item->participant = ReinsurancePartner::getPartnerNameByCode($item->treaty_code);
                        return $item;
                    });

                $item->sub_reinsurance_data = $subReinsuranceData;
                $item->reinsurance_type = ReinsuranceData::getReinsuranceType($item->product_code, $item->treaty_code);
                $item->participant = ReinsurancePartner::getPartnerNameByCode($item->treaty_code);

                return $item;
            });
    }

    public function checkIfShareUnderLimit($policyId)
    {

        $totalShares = ReinsuranceData::getTotalShares($policyId);

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

    public function isPolicyReinsuranceCompleted(Policy $policy)
    {
        return response()->json(ReinsuranceData::isReinsuranceCompleted($policy->id));
    }

    public function listTreatyCodes()
    {
        return ReinsurancePartner::getPartnerNamesForForm();
    }

    public function listPartnerGroupCodes()
    {
        return ReinsurancePartnerGroup::getAllPartnerGroupNames();
    }

    public function exportReinsurance($id, $productCode)
    {
        return Excel::download(new ReinsuranceExport($id, $productCode), 'Reinsurance.xlsx');
    }

    public function exportPolicy(Request $request)
    {
        return Excel::download(new PolicyRegister($request->route('issue_date_from'), $request->route('issue_date_to'), ReinsurancePartner::getAllPartnerNames()), 'Policy.xlsx');
    }

    // Use approved_status as submit_status
    public function updateSubmitStatus(Policy $policy, Request $request)
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

    public function getSignatureByPolicy(Policy $policy)
    {
        return [
            'signature' => UserFile::select('file_url')->where('user_id', $policy->approved_by)->where('file_type', 'SIGNATURE')->first()
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
        $invoice->address = @$policy->dataMaster->customer->info()->address;
        if ($policy->status == 'APV' && $withSignature) {
            $signature = UserFile::select('file_url')->where('user_id', $policy->approved_by)->where('file_type', 'SIGNATURE')->first();
            if ($signature)
                if (!$signature->file_url)
                    $signature = null;
        }

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Invoice');
        $pdf->loadView('pdf.policies.hs.invoice', ['invoice' => $invoice, 'bank_list' => $bank_list, 'signature' => $signature ?? null]);

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('margin-top', 33);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);

        return $pdf->stream($invoice->inv_cdn_no . '.pdf');
    }

    private function getSignature(DataMaster $hs)
    {

        if (!$hs->quotation && !optional($hs->quotation)->approved_by || optional($hs->quotation)->approved_status != 'APV') return null;

        return UserFile::select('file_url')->where('user_id', $hs->quotation->approved_by)->where('file_type', 'SIGNATURE')->first();
    }

    public function showDetail($id)
    {
        $hs = $this->hsDetail($id);

        return [
            'hs' => $hs,
            'signature' => $this->getSignature($hs)
        ];
    }

    private function hsDetail($id)
    {
        $hs = DataMaster::with([
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
            ->where('customer_no', $hs->customer_no)
            ->first();
        $addressData = AddressCode::select('province', 'district', 'commune')->where('postal_code', $customer->postal_code)->first();
        $country = Country::select('description')->where('country_code', $hs->customer->country_code)->value('description');

        $hs->customer_type = $customer->customer_type;
        $hs->customer_classification = optional($customer->customerClassification)->description;
        $hs->customer_classification_kh = optional($customer->customerClassification)->description_kh;
        $hs->endorsement_clause = $this->getInsuranceClauses($hs->id, 'ENDORSEMENT');
        $hs->general_exclusive = $this->getInsuranceClauses($hs->id, 'EXCLUSION');

        $hs->negotiation_rate = $hs->negotiation_rate;
        $hs->addressData = $addressData;
        $hs->country = $country;

        if ($hs->updated_by)
            $hs->issued_by = $hs->issuedByName($hs->updated_by);
        else if ($hs->created_by)
            $hs->issued_by = $hs->issuedByName($hs->created_by);
        else
            $hs->issued_by = null;

        return $hs;
    }

    public function downloadPolicySchedule(Request $request, Policy $policy, $lang)
    {
        App::setLocale($lang);
        $hs = $this->hsPolicyService->getDataDetail($policy->data_id);
        // fix error when there is a record but empty file_url
        if (isset($hs['signature']))
            if (!$hs['signature']->file_url)
                $hs['signature'] = null;

        $hs['hasLetterHead'] = request()->letterhead;
        $documentNo = $hs['policy_no'] ?? '';
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadView('pdf.policies.hs.hs', compact('hs'));

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
                'hasLetterHead' => request()->letterhead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => request()->letterhead
            ]),
        ]);
        if ($documentNo == '')
            return $pdf->stream('Quotation No.pdf');
        else
            return $pdf->stream($documentNo . '.pdf');
    }

    public function downloadHSCertificate($id)
    {
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'Certificate');

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('zoom', 1);
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $data = $this->getHSCertificateData($id);

        $pdf->loadView('pdf.policies.hs.certificate', $data);

        return $pdf->stream('certificate.pdf');
    }

    private function getHSCertificateData($id)
    {
        $policy = Policy::select('id', 'document_no', 'data_id')->find($id);

        $hs = DataMaster::with(['customer' => function ($query) {
            $query->select('customer_no', 'address_en', 'name_en', 'village_en', 'country_code', 'postal_code');
        }])
            ->select('id', 'customer_no', 'insured_name', 'effective_day', 'effective_date_from', 'effective_date_to')
            ->find($policy->data_id);

        $addressData = AddressCode::select('province', 'district', 'commune')->where('postal_code', $hs->customer->postal_code)->first();
        $hs->addressData = $addressData;

        $country = Country::select('description')->where('country_code', $hs->customer->country_code)->value('description');
        $hs->country = $country;

        $hsDetails = DataDetail::
            // ->select('id', 'product_code', 'master_data_id','selected_cover_pkg', 'cover_pkg_id')
            where('master_data_id', $hs->id)
            ->orderBy('id')
            ->get();

        $hsDetails = $hsDetails->map(function ($item) {
            $deductibles = DeductibleDetail::listByDetailAndProduct($item->id, $item->product_code);
            $item->deductibles = $deductibles;

            $coverArr = explode(',', $item->selected_cover_pkg);

            $covers = CoverPackage::getCoverPackageWithRemainingCovers($item->cover_pkg_id, $coverArr);
            $item->covers = $covers;

            return $item;
        });

        return [
            'policy' => $policy,
            'hs' => $hs,
            'hs_details' => $hsDetails
        ];
    }

    private function getInsuranceClauses($hsId, $clauseType)
    {
        return DataMaster::find($hsId)->insuranceClauses()
            ->select('clause', 'clause_kh', 'clause_zh', 'ins_insurance_clause.id')
            ->where('clause_type', $clauseType)
            ->orderBy('sequence')
            ->get();
    }

    public function exportInsuredPersons($id, $document_no)
    {
        return Excel::download(new InsuredPersonsExport($id,$document_no,true), $document_no . '.xlsx');
    }

    public function isPolicyConfigurationCompleted(Policy $policy)
    {
        return $policy->isPolicyConfigurationCompleted();
    }
}

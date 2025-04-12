<?php

namespace App\Services\Travel\Policy;

use App\Models\BusinessManagement\BusinessChannel;
use App\Models\Insurance\PolicyCommissionData;
use App\Models\Insurance\ReinsuranceData;
use App\Models\RefEnum;
use App\Models\Travel\Policy\PolicyV;
use App\Services\Travel\QuotationService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReinsuranceExport;
use App\Exports\PolicyRegisterExport;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;
use Illuminate\Http\Request;
use App\Models\UserManagement\User\UserFile;
use App\Models\BankInformation\BankInformation;
use Illuminate\Support\Facades\App;
use App\Models\CustomerManagement\Travel\TravelCustomer as Customer;
use App\Models\CustomerManagement\CustomerClassification;
use App\Models\Travel\Policy\DataMaster;
use App\Models\Travel\Policy\InsuredPersonView;
use App\Models\Travel\Policy\Invoice\InvoiceNote;
use App\Models\Travel\Policy\Policy;
use App\Models\Travel\Policy\QuotationDetailDataView;
use App\Models\Insurance\InsuranceClause;
use App\Models\Product\Product;
use Illuminate\Support\Carbon;
use KhmerDateTime\KhmerDateTime;

class PolicyDetailService
{
    public function __construct(private QuotationService $quotationService)
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
        $p_user_id = auth()->id();
        $params = [
            $policyId,
            $p_user_id
        ];

        $generated = DB::select("select * from ins_tv_generate_commission_data(?, ?)", $params);

        if (!empty($generated)) {
            return response()->json([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }

    public function getCommissionData($policyId)
    {
        return PolicyCommissionData::where('policy_id', $policyId)
            ->where('status', 'ACT')
            ->orderBy('detail_id')
            ->get();
    }

    public function getCommissionDataByVehicle($detail_id)
    {
        return PolicyCommissionData::where('detail_id', $detail_id)
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
            ->where('detail_id', $request->detail_id)
            ->where('status', 'ACT')
            ->first();

        $commission_data->premium_tax_fee_rate = $request->premium_tax_fee_rate;
        $commission_data->commission_rate = $request->commission_rate;
        $commission_data->witholding_tax_rate = $request->witholding_tax_rate;
        $commission_data->save();

        $generated = DB::select("select * from ins_regen_commission_data(" . $policyId . ")");

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
        $generated = DB::select("select * from ins_tv_generate_reinsurance_share(" . $policyId . ")");

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }

    public function generateReinsuranceData($policyId)
    {
        $generated = DB::select("select * from ins_tv_generate_reinsurance_data(" . $policyId . ")");

        if (!empty($generated)) {
            return response([
                'success' => true,
                'message' => 'Generated successfully!',
            ]);
        }
    }

    public function getReinsuranceData($policyId)
    {
        $deletedVehicles = ReinsuranceData::where('policy_id', $policyId)->where('endorsement_state', 'DELETION')->distinct()->pluck('detail_id')->toArray();
        return ReinsuranceData::where('policy_id', $policyId)
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->orderBy('id')
            ->get()
            ->filter(function ($item) use ($deletedVehicles) {
                if ($deletedVehicles) {
                    if (in_array($item->detail_id, $deletedVehicles))
                        if ($item->endorsement_state != 'DELETION')
                            return false;
                }
                return true;
            })
            ->map(function ($item) use ($policyId) {
                $subReinsuranceData = ReinsuranceData::where('policy_id', $policyId)
                    ->where('detail_id', $item->detail_id)
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
            })
            ->groupBy('detail_id');
    }

    public function checkIfShareUnderLimit($policyId, $detailId)
    {
        $hasDeletedVehicle = ReinsuranceData::where('policy_id', $policyId)
            ->where('detail_id', $detailId)
            ->where('endorsement_state', 'DELETION')
            ->first() ? true : false;
        if ($hasDeletedVehicle)
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

    public function isPolicyReinsuranceCompleted(Policy $policy)
    {
        return ReinsuranceData::isReinsuranceCompleted($policy->id);
    }

    public function isPolicyConfigurationCompleted(Policy $policy)
    {
        return response()->json($policy->isPolicyConfigurationCompleted(), 200);
    }

    public function listAutoEndorsementTypes()
    {
        return RefEnum::listAutoEndorsementTypes();
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

    public function exportPoilcy(Request $request)
    {
        return Excel::download(new PolicyRegisterExport($request->route('issue_date_from'), $request->route('issue_date_to'), ReinsurancePartner::getAllPartnerNames()), 'Policy.xlsx');
    }

    public function checkCanGenerateEndorsement(Policy $policy)
    {
        if ($policy->status !== 'APV') return response()->json(false);

        // If is policy cancellation endorsement
        if ($policy->auto->endorsement_type === 'CANCELLATION') return response()->json(false);

        return response()->json(Policy::isLatestEndorsement($policy));
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Insurance\Policy $policy
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice($id, $withSignature = null)
    {
        $invoice = InvoiceNote::getInvoiceData($id);

        $bank_list = BankInformation::where('status', 'ACT')->where('default', true)->get();

        $policy = Policy::find($id);
        if ($policy->status == 'APV' && $withSignature) {
            $signature = UserFile::select('file_url')->where('user_id', $policy->approved_by)->where('file_type', 'SIGNATURE')->first();
            if ($signature)
                if (!$signature->file_url)
                    $signature = null;
        }

        $pdf = App::make('snappy.pdf.wrapper');

        $pdf->setOption('title', 'Invoice');
        $pdf->loadView('pdf.policies.invoice', ['invoice' => $invoice, 'bank_list' => $bank_list, 'signature' => $signature ?? null]);

        $pdf->setOption('page-size', 'a4');
        $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('margin-top', 33);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);

        return $pdf->stream($invoice->inv_cdn_no . '.pdf');
    }

    public function getDataDetail($id)
    {
        $policyV = PolicyV::where('id', $id)->first();
        $dataMaster = DataMaster::with('product')->findOr($policyV?->data_id, fn() => abort(404, 'Not found'));
        $policy = Policy::where('id', $id)->first();
        $policy_no = Policy::where('id', $id)->value('document_no');
        $customer = Customer::where('customer_no', $dataMaster->customer_no)->first();

        $customerClassification = CustomerClassification::where('cust_classification', $customer->cust_classification)->select('description', 'description_kh')->first();

        if (!$policy_no) abort(404, 'Not found');

        $baseDataQuery = QuotationDetailDataView::where('data_id', $policyV->data_id);

        $standardBenefits = (clone $baseDataQuery)->where('plan', 'Standard')
            ->get();
        $standardPremiumPerPerson = (clone $baseDataQuery)->where('plan', 'Standard')
            ->first();
        $standardTotalPremium = (clone $baseDataQuery)->where('plan', 'Standard')
            ->first();

        $optionalBenefits = (clone $baseDataQuery)->where('plan', 'Optional')
            ->get();
        $optionalPremiumPerPerson = (clone $baseDataQuery)->where('plan', 'Optional')
            ->first();
        $optionalTotalPremium = (clone $baseDataQuery)->where('plan', 'Optional')
            ->first();

        $additionalBenefits = (clone $baseDataQuery)->where('plan', 'ADDITIONAL')->get();
        $additionalTotalPremium = (clone $baseDataQuery)->where('plan', 'ADDITIONAL')
            ->first();
        $totalAdditionalPremium = $additionalBenefits
            ->map(function ($row) {
                return $row;
            })->sum();

        $premium = (clone $baseDataQuery)
            ->first();
        $standardBasePlanAmount = (clone $baseDataQuery)
            ->first();
        $totalPremium = (clone $baseDataQuery)
            ->first();
        $grandTotal = 0;

        $effectiveDateFrom = App::isLocale('en') ? Carbon::parse($dataMaster?->effective_date_from)->format('d/F/Y') : KhmerDateTime::parse($dataMaster?->effective_date_from)->format('LL');
        $effectiveDateTo = App::isLocale('en') ? Carbon::parse($dataMaster?->effective_date_to)->format('d/F/Y') : KhmerDateTime::parse($dataMaster?->effective_date_to)->format('LL');

        $endorsementClauses = collect();

        $generalExclusions = collect();

        $geographicalLimit = collect();

        $policy->issued_on = $policy->updated_at ?? $policy->created_at ?? NULL;

        if ($policy->updated_by)
            $policy->issued_by = $policy->issuedByName($policy->updated_by);
        else if ($policy->created_by)
            $policy->issued_by = $policy->issuedByName($policy->created_by);
        else
            $policy->issued_by = null;

        $insuredPersons = InsuredPersonView::where('data_id', $id)->get()??collect();
        $product = Product::where('code', $policy->product_code)->first()?? null;

        $address = null;

        if ($policy->dataMaster
            && $customer = $policy->dataMaster->customer) {
            $address = (collect(DB::select('select * from ins_get_customer_info(?)', [$customer->customer_no]))->first())->address;
        }

        return [
            'issued_on' => App::getLocale() == 'en' ? date('d F Y', strtotime($policy->issued_on)) : KhmerDateTime::parse($policy->issued_on)->format('LL'),
            'issued_by' => $policy->issued_by,
            'policy' => $policy,
            'policy_no' => $policy_no,
            'data_id' => $policy->data_id,
            'subjectivity' => $dataMaster->subjectivity,
            'subjectivity_kh' => $dataMaster->subjectivity_kh,
            'warranty' => $dataMaster->warranty,
            'warranty_kh' => $dataMaster->warranty_kh,
            'business_code' => $dataMaster?->business_code,
            'insured_name' => $dataMaster?->insured_name,
            'insured_name_kh' => $dataMaster?->insured_name_kh,
            'correspondence_address' => $customer->info()->address,
            'business_occupation' => $customerClassification->description,
            'business_occupation_kh' => $customerClassification->description_kh,
            'period_of_insurance' => $dataMaster->effective_day . " " . __('Days') . ' - ' . __('From') . ' ' . $effectiveDateFrom . ' ' . __('To') . ' ' . "$effectiveDateTo (" . __('Both Days Inclusive') . ')', //todo
            'coverage' => App::isLocale('en') ? nl2br($product->coverage_en) : nl2br($product->coverage_kh),
            'policy_wording_version' => $dataMaster?->policy_wording_version,
            'geographical_limit' => $geographicalLimit,
            'insured_person_note' => nl2br($dataMaster->insured_person_note),
            'insured_person_note_kh' => nl2br($dataMaster->insured_person_note_kh),

            'standard_benefits' => $standardBenefits,
            'standard_premium_per_person' => $standardPremiumPerPerson,
            'standard_total_premium' => $standardTotalPremium,

            'optional_benefits' => $optionalBenefits,
            'optional_premium_per_person' => $optionalPremiumPerPerson,
            'optional_total_premium' => $optionalTotalPremium,

            'additional_benefits' => $additionalBenefits,
            'additional_total_premium' => $additionalTotalPremium,
            'total_additional_premium' => $totalAdditionalPremium,

            'premium' => $premium,
            'standard_base_plan_amount' => $standardBasePlanAmount,
            'total_premium' => $totalPremium,

            'grand_total_premium' => round($grandTotal, 2),
            'memorandum' => $dataMaster?->memorandum,
            'memorandum_kh' => $dataMaster?->memorandum_kh,
            'remark' => $dataMaster?->remark,
            'remark_kh' => $dataMaster?->remark_kh,
            'jurisdiction' => __('Kingdom of Cambodia'),
            'quotation_validity' => __('30 days from the issuance date'),

            'endorsement_clauses' => $endorsementClauses,
            'general_exclusions' => $generalExclusions,
            'product' => $dataMaster->product,
            ...$this->getSignatureByPolicy($policy),
            'insured_persons' => $insuredPersons
        ];
    }

    public function handleCustomerAddress($address, $village, $commune, $district, $province, $country)
    {
        $collect = collect([$address, $village, $commune, $district, $province . (strtolower($province) == 'phnom penh' ? ' Capital' : ($province ? ' Province' : "")), $country]);
        $collect = $collect->filter()->all();
        return collect($collect)->join(', ');
    }
}

<?php

namespace App\Services\HS;

use App\Models\Address\AddressCode;
use App\Models\CustomerManagement\Country;
use App\Models\CustomerManagement\Customer;
use App\Models\CustomerManagement\CustomerClassification;
use App\Models\HS\DataMaster;
use App\Models\HS\Quotation;
use App\Models\HS\QuotationDetailDataView;
use App\Models\Insurance\InsuranceClause;
use App\Models\Product\Product;
use App\Models\UserManagement\User\UserFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use KhmerDateTime\KhmerDateTime;

class QuotationService
{
  public function __construct(protected Quotation $model, private SchemaDataService $schemaDataService, )
  {
  }

  public function generateNewQuotation($masterDataId, $makerId)
  {
    $params = [
      $masterDataId,
      $makerId
    ];
    info('QuotationService: generateNewQuotation', $params);

    $generated = DB::select("select * from ins_hs_prod_generate_new_quotation(?,?)", $params);
    info('QuotationService: generateNewQuotation response', $generated);

    if (optional($generated[0])->code) {
      return true;
    }
    return false;
  }

  public function getDataDetail($id)
  {
    $dataMaster = DataMaster::findOr($id, fn() => abort(404, 'Not found.'));
    $quotation_no = Quotation::where('data_id', $id)->value('document_no');
    $quotation = Quotation::with(['policy' => function($q){
      $q->where('status','<>','DEL');
    }])->withCount('insuredPersons')->where('data_id', $id)->first();
    $customer = Customer::where('customer_no', $dataMaster->customer_no)->first();
    $customerClassification = CustomerClassification::where('cust_classification', $customer->cust_classification)->select('description', 'description_kh')->first();
    // $insuredPerson = $this->schemaDataService->getNumberOfInsuredPerson($dataMaster->id);

    if (!$quotation_no) {
      abort(404, 'Not found');
    }

    $baseDataQuery = QuotationDetailDataView::where('master_data_id', $id)->where('lang_code', App::getLocale() == 'km' ? 'KM' : 'EN');
    $standardBenefits = (clone $baseDataQuery)->where('schema_type', 'STANDARD')
      ->where('key', 'ITEM')
      ->get();
    $standardBasePlanAmount = (clone $baseDataQuery)->where('schema_type', 'STANDARD')
      ->where('key', 'BASE_PLANS_AMOUNT')
      ->first();
    $standardPremiumPerPerson = (clone $baseDataQuery)->where('schema_type', 'STANDARD')
      ->where('master_data_type', 'QUOTATION')
      ->where('key', 'PREMIUM')
      ->first();
    $standardTotalPremium = (clone $baseDataQuery)->where('schema_type', 'STANDARD')
      ->where('master_data_type', 'QUOTATION')
      ->where('key', 'TOTAL_PREMIUM')
      ->first();

    $optionalBenefits = (clone $baseDataQuery)->where('schema_type', 'OPTIONAL')
      ->where('key', 'ITEM')
      ->where(DB::raw('(coalesce(plan_1,0)+coalesce(plan_2,0)+coalesce(plan_3,0)+coalesce(plan_4,0)+coalesce(plan_5,0))'), ">", 0)
      ->get();
    $optionalPremiumPerPerson = (clone $baseDataQuery)->where('schema_type', 'OPTIONAL')
      ->where('master_data_type', 'QUOTATION')
      ->where('key', 'PREMIUM')
      ->first();
    $optionalTotalPremium = (clone $baseDataQuery)->where('schema_type', 'OPTIONAL')
      ->where('master_data_type', 'QUOTATION')
      ->where('key', 'TOTAL_PREMIUM')
      ->first();

    $additionalBenefits = (clone $baseDataQuery)->where('schema_type', 'ADDITIONAL')
      ->where(DB::raw('(coalesce(plan_1,0)+coalesce(plan_2,0)+coalesce(plan_3,0)+coalesce(plan_4,0)+coalesce(plan_5,0))'), ">", 0)
      ->get();
    $additionalTotalPremium = (clone $baseDataQuery)->where('schema_type', 'ADDITIONAL')
      ->selectRaw('SUM(plan_1) plan_1,SUM(plan_2) plan_2,SUM(plan_3) plan_3,SUM(plan_4) plan_4,SUM(plan_5) plan_5')
      ->first();
    $totalAdditionalPremium = $additionalBenefits
      ->map(function ($row) {
        return $row->plan_1 + $row->plan_2 + $row->plan_3 + $row->plan_4 + $row->plan_5;
      })->sum();

    $premium = (clone $baseDataQuery)->where('key', 'Q_PREMIUM')->first();
    $totalPremium = (clone $baseDataQuery)->where('key', 'Q_TOTAL_PREMIUM')->first();
    $grandTotal = floatval($totalPremium->plan_1) + floatval($totalPremium->plan_2) + floatval($totalPremium->plan_3) + floatval($totalPremium->plan_4) + floatval($totalPremium->plan_5);
    $effectiveDateFrom = App::isLocale('en') ? Carbon::parse($dataMaster?->effective_date_from)->format('d/F/Y') : KhmerDateTime::parse($dataMaster?->effective_date_from)->format('LL');
    $effectiveDateTo = App::isLocale('en') ? Carbon::parse($dataMaster?->effective_date_to)->format('d/F/Y') : KhmerDateTime::parse($dataMaster?->effective_date_to)->format('LL');

    $endorsementClauses = $dataMaster->insuranceClauses()
      ->select('clause', 'clause_kh', 'clause_zh', 'clause_type')
      ->where('clause_type', InsuranceClause::ENDORSEMENT_CLAUSE)
      ->get();

    $generalExclusions = $dataMaster->insuranceClauses()
      ->select('clause', 'clause_kh', 'clause_zh', 'clause_type')
      ->where('clause_type', InsuranceClause::GENERAL_EXCLUSION)
      ->get();

    $geographicalLimit = $dataMaster->insuranceClauses()
      ->select('clause', 'clause_kh', 'clause_zh', 'clause_type')
      ->where('clause_type', InsuranceClause::GEOGRAPHICAL_LIMIT)
      ->first();

    $quotation->issued_on = $quotation->updated_at ?? $quotation->created_at ?? null;
    if ($quotation->updated_by) {
      $quotation->issued_by = $quotation->issuedByName($quotation->updated_by);
    } elseif ($quotation->created_by) {
      $quotation->issued_by = $quotation->issuedByName($quotation->created_by);
    } else {
      $quotation->issued_by = null;
    }

    $addressData = AddressCode::select('province', 'district', 'commune')->where('postal_code', $customer->postal_code)->first();
    $country = Country::select('description')->where('country_code', $customer->country_code)->value('description');
    $product = Product::where('code', $quotation->product_code)->first();

    return [
      'quotation_no' => $quotation_no,
      'product' => $dataMaster->product,
      'quotation' => $quotation,
      'subjectivity' => $dataMaster->subjectivity,
      'subjectivity_kh' => $dataMaster->subjectivity_kh,
      'warranty' => $dataMaster->warranty,
      'warranty_kh' => $dataMaster->warranty_kh,
      'business_code' => $dataMaster?->business_code,
      'insured_name' => $dataMaster?->insured_name,
      'insured_name_kh' => $dataMaster?->insured_name_kh,
      'correspondence_address' => $customer->info()->address,
      'customer' => $customer,
      'addressData' => $addressData,
      'country' => $country,
      'business_occupation' => $customerClassification?->description,
      'business_occupation_kh' => $customerClassification?->description_kh,
      'period_of_insurance' => $dataMaster->effective_day . " " . __('Days') . ' - ' . __('From') . ' ' . $effectiveDateFrom . ' ' . __('To') . ' ' . "$effectiveDateTo  (" . __('Both Days Inclusive') . ')',
      'coverage' => App::isLocale('en') ? nl2br($product->coverage_en) : nl2br($product->coverage_kh),
      'policy_wording_version' => $dataMaster?->policy_wording_version,
      'insured_person_note' => nl2br($dataMaster->insured_person_note),
      'insured_person_note_kh' => nl2br($dataMaster->insured_person_note_kh),

      'standard_benefits' => $standardBenefits,
      'standard_base_plan_amount' => $standardBasePlanAmount,
      'standard_premium_per_person' => $standardPremiumPerPerson,
      'standard_total_premium' => $standardTotalPremium,

      'optional_benefits' => $optionalBenefits,
      'optional_premium_per_person' => $optionalPremiumPerPerson,
      'optional_total_premium' => $optionalTotalPremium,

      'additional_benefits' => $additionalBenefits,
      'additional_total_premium' => $additionalTotalPremium,
      'total_additional_premium' => $totalAdditionalPremium,

      'premium' => $premium,
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
      'geographical_limit' => $geographicalLimit,
      'issued_on' => App::isLocale('en') ? date('d F Y', strtotime($quotation->issued_on)) : KhmerDateTime::parse($quotation->issued_on)->format('LL'),
      'issued_by' => $quotation->issued_by,
      ...$this->getSignatureByQuotation($quotation)
    ];
  }

  public function findByDataId($dataId)
  {
    info('QuotationService: findByDataId ', ['data_id' => $dataId]);
    return $this->model->where('data_id', $dataId)->first();
  }

  public function update(array $data, $id, $quietly = false)
  {
    info('QuotationService: update ' . $id, $data);

    if ($quietly) {
      return $this->model->find($id)->updateQuietly($data);
    }
    return $this->model->find($id)->update($data);
  }

  public function handleCustomerAddress($address, $village, $commune, $district, $province, $country)
  {
    if ($address) {
      return $address;
    }
    $collect = collect([$address, $village, $commune, $district, $province . ($province == 'Phnom Penh' ? ' Capital' : ($province ? ' Province' : "")), $country]);
    $collect = $collect->filter()->all();
    return collect($collect)->join(', ');
  }

  public function getSignatureByQuotation(Quotation $quotation)
  {
    return [
      'signature' => UserFile::select('file_url')->where('user_id', $quotation->approved_by)->where('file_type', 'SIGNATURE')->first()
    ];
  }
}

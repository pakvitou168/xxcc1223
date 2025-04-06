<?php

namespace App\Services\Travel;

use App\Enums\TravelPackage;
use App\Models\BusinessManagement\BusinessChannel;
use App\Models\BusinessManagement\BusinessHandler;
use App\Models\CustomerManagement\Customer;
use App\Models\Insurance\InsuranceClause;
use App\Models\Travel\Country;
use App\Models\Product\Product;
use App\Models\ProductConfiguration\PolicyWordingVersion;
use App\Models\ProductLine\ProductLine;
use App\Models\RecordStatus;
use App\Models\RefEnum;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BaseService
{
    const OPTIONAL_BNF = 'OPTIONAL';
    private $serviceUrl;

    const DEFAULT_LANG = 'EN';

    public function __construct()
    {
        $this->serviceUrl = config('pgi.api_insurance_service_url');
    }

    public function searchInsuredPerson(Request $request)
    {
        $search = $request->search;
        $customerType = $request->customer_type;
        return Customer::whereStatus(RecordStatus::ACTIVE)->when($search, function ($q) use ($search) {
            $q->where('name_en', 'ilike', "%$search%")
                ->orWhere('name_kh', 'ilike', "%$search%")
                ->orWhere('customer_no', 'ilike', "%$search%");
        })
            ->when($customerType, function ($q) use ($customerType) {
                $q->whereCustomerType($customerType);
            })
            ->select('customer_no', 'name_en', 'name_kh')
            ->orderBy('customer_no')
            ->limit($customerType ? -1 : 100)
            ->get();
    }
    public function customerTypeOptions()
    {
        return RefEnum::where('group_id', 'CUSTOMER_TYPE')
            ->get()
            ->map(fn($item) => [
                'label' => $item->name,
                'value' => $item->enum_id
            ]);
    }
    public function endorsementClauseOptions()
    {
        return InsuranceClause::whereStatus(RecordStatus::ACTIVE)->whereProductLineCode(ProductLine::TRAVEL)->whereClauseType(InsuranceClause::ENDORSEMENT_CLAUSE)->orderBy('sequence')->select('id AS value', 'clause AS label')->get();
    }
    public function generalExclusionOptions()
    {
        return InsuranceClause::whereStatus(RecordStatus::ACTIVE)->whereProductLineCode(ProductLine::TRAVEL)->whereClauseType(InsuranceClause::GENERAL_EXCLUSION)->orderBy('sequence')->select('id AS value', 'clause AS label')->get();
    }
    public function packageAndGroupOptions()
    {
        $packageGroups = RefEnum::whereGroupId('POLICY_CONFIG')
            ->whereIn('type_id',['TV_PACKAGE','TV_GROUP'])
            ->select('name AS label', 'enum_id AS value','type_id')->get()
            ->groupBy('type_id');

            return [
                'package_options' => $packageGroups['TV_PACKAGE'],
                'group_options' => $packageGroups['TV_GROUP']
            ];
    }
    public function businessChannelOptions()
    {
        return RefEnum::whereGroupId('BUSINESS_CHANNEL')
            ->whereTypeId('SALE_CHANNEL')
            ->select('name AS label', 'enum_id AS value')
            ->get();
    }
    public function businessOptions($businessCategory)
    {
        return BusinessChannel::select('business_code AS value', DB::raw("concat(business_code,'- ',full_name) AS label"), 'commission_rate')
            ->whereSaleChannel($businessCategory)
            ->whereStatus(RecordStatus::ACTIVE)
            ->orderBy('business_code')->get();
    }
    public function businessHandlerOptions()
    {
        return BusinessHandler::whereStatus(RecordStatus::ACTIVE)
            ->select('name as label', 'handler_code as value')
            ->get();
    }
    public function scheduleBenefitOptions()
    {
        $response = Http::withHeaders(['Accept-Language' => self::DEFAULT_LANG])->get($this->serviceUrl . '/tv/coverage');
        $response->throw();
        $data = json_decode($response->body())->response;

        $data = collect($data)->map(function ($item) {
                $item->is_selected = $item->category === "STANDARD" || $item->category === "AGG_LIMIT";
                return $item;
        });
        return $data;
    }
    
    public function policyWordingOptions()
    {
        return PolicyWordingVersion::whereProductLineCode(ProductLine::TRAVEL)->select('policy_wording AS value', 'policy_wording AS label')->get();
    }
    public function defaultEndorsementClause(): array
    {
        return InsuranceClause::whereStatus(RecordStatus::ACTIVE)->whereProductLineCode(ProductLine::TRAVEL)->whereClauseType(InsuranceClause::ENDORSEMENT_CLAUSE)->whereDefaultInclusion(InsuranceClause::DEFAULT_YES)->pluck('id')->toArray();
    }
    public function defaultGeneralExclusion(): array
    {
        return InsuranceClause::whereStatus(RecordStatus::ACTIVE)->whereProductLineCode(ProductLine::TRAVEL)->whereClauseType(InsuranceClause::GENERAL_EXCLUSION)->whereDefaultInclusion(InsuranceClause::DEFAULT_YES)->pluck('id')->toArray();
    }
    public function productTypeOptions()
    {
        return Product::whereProductLineCode(ProductLine::TRAVEL)->select('code AS value', 'name AS label','default_accumulation_limit_amount','default_discount','default_surcharge')->get();
    }
    public function calcOptions()
    {
        return RefEnum::whereGroupId('PROD_CONFIG')->whereTypeId('CALC_OPTION')->select('enum_id AS label', 'enum_id AS value')->get();
    }
    public function periodOptions()
    {
        return RefEnum::whereGroupId('PROD_CONFIG')
            ->whereTypeId('INSURANCE_PERIOD_TYPE')->select('name as label', 'enum_id AS value')->get();
    }
    public function quotationTmp()
    {
        return '/templates/travel_quotation.xlsx';
    }
    public function autoExtClauseOptions()
    {
        return InsuranceClause::whereProductLineCode(ProductLine::TRAVEL)->whereStatus(RecordStatus::ACTIVE)->whereClauseType('AUTOMATIC_EXTENSION')->select('clause AS label', 'id AS value')->get();
    }
    public function countryOptions()
    {
        return Country::active()->select('name as label', 'code as value')->get();
    }
}
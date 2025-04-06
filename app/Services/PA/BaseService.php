<?php

namespace App\Services\PA;

use App\Models\BusinessManagement\BusinessChannel;
use App\Models\BusinessManagement\BusinessHandler;
use App\Models\CustomerManagement\Customer;
use App\Models\Insurance\InsuranceClause;
use App\Models\PA\BnfExtension;
use App\Models\PA\BnfExtensionData;
use App\Models\PA\Coverage;
use App\Models\PA\DataMaster;
use App\Models\PA\WorkingClass;
use App\Models\Product\Product;
use App\Models\ProductConfiguration\PolicyWordingVersion;
use App\Models\ProductLine\ProductLine;
use App\Models\RecordStatus;
use App\Models\RefEnum;
use App\Models\ReinsuranceConfig\ReinsuranceConfig;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class BaseService
{
    const OPTIONAL_BNF = 'OPTIONAL';
    private $serviceUrl;
    public function __construct(private DataMaster $dataMaster)
    {
        $this->serviceUrl = config('pgi.api_insurance_service_url');
    }
    public function jointOptions()
    {
        return collect([
            [
                'label' => 'Individual',
                'value' => 'S' //previously used Single value S
            ],
            [
                'label' => 'Joint',
                'value' => 'J'
            ]
        ]);
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
    public function jointLevelOptions()
    {
        return collect([
            [
                'value' => 'PRIMARY',
                'label' => 'PRIMARY'
            ],
            [
                'value' => 'SECONDARY',
                'label' => 'SECONDARY'
            ]
        ]);
    }
    public function permissionOptions()
    {
        return collect([
            [
                'value' => 'FULL',
                'label' => 'FULL',
            ]
        ]);
    }
    public function endorsementClauseOptions()
    {
        return InsuranceClause::whereStatus(RecordStatus::ACTIVE)->whereProductLineCode(ProductLine::PA)->whereClauseType(InsuranceClause::ENDORSEMENT_CLAUSE)->orderBy('sequence')->select('id AS value', 'clause AS label')->get();
    }
    public function generalExclusionOptions()
    {
        return InsuranceClause::whereStatus(RecordStatus::ACTIVE)->whereProductLineCode(ProductLine::PA)->whereClauseType(InsuranceClause::GENERAL_EXCLUSION)->orderBy('sequence')->select('id AS value', 'clause AS label')->get();
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
        return BusinessChannel::select('business_code AS value', DB::raw("concat(business_code,'- ',full_name) AS label"), 'commission_rate', 'handler_code')
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
    public function geographicalLimitOptions()
    {
        return Coverage::whereStatus(RecordStatus::ACTIVE)->select('name as label', 'id as value')->get();
    }
    public function policyWordingOptions()
    {
        return PolicyWordingVersion::whereProductLineCode(ProductLine::PA)->select('policy_wording AS value', 'policy_wording AS label')->get();
    }
    public function defaultEndorsementClause(): array
    {
        return InsuranceClause::whereStatus(RecordStatus::ACTIVE)->whereProductLineCode(ProductLine::PA)->whereClauseType(InsuranceClause::ENDORSEMENT_CLAUSE)->whereDefaultInclusion(InsuranceClause::DEFAULT_YES)->pluck('id')->toArray();
    }
    public function defaultGeneralExclusion(): array
    {
        return InsuranceClause::whereStatus(RecordStatus::ACTIVE)->whereProductLineCode(ProductLine::PA)->whereClauseType(InsuranceClause::GENERAL_EXCLUSION)->whereDefaultInclusion(InsuranceClause::DEFAULT_YES)->pluck('id')->toArray();
    }
    public function productTypeOptions()
    {
        return Product::whereProductLineCode(ProductLine::PA)->select('code AS value', 'name AS label', 'default_accumulation_limit_amount', 'default_discount', 'default_surcharge')->get();
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
    public function optionalBnfOptions()
    {
        return BnfExtension::whereType(self::OPTIONAL_BNF)->whereStatus(RecordStatus::ACTIVE)->select('code AS value', 'name AS label', 'id', 'description')->get();
    }
    public function optionalBnfBaseOptions()
    {
        return RefEnum::whereGroupId('PA_EXTENSION_CONFIG')->whereTypeId('PA_AMOUNT_TYPE')->select('name AS label', 'enum_id AS value')->get();
    }
    public function quotationTmp()
    {
        return '/templates/pa_quotation.xlsx';
    }
    public function autoExtClauseOptions()
    {
        return InsuranceClause::whereProductLineCode(ProductLine::PA)->whereStatus(RecordStatus::ACTIVE)->whereClauseType('AUTOMATIC_EXTENSION')->select('clause AS label', 'id AS value')->get();
    }
    public function defaultAutoExtension()
    {
        return InsuranceClause::whereProductLineCode(ProductLine::PA)->whereStatus(RecordStatus::ACTIVE)->whereClauseType('AUTOMATIC_EXTENSION')->whereDefaultInclusion(InsuranceClause::DEFAULT_YES)->pluck('id')->toArray();
    }
    public function classOptions(): array
    {
        return WorkingClass::selection();
    }
    public function validateInsuredData($data)
    {
        $validator = Validator::make($data, [
            '*.insured_person' => ['required'],
            '*.occupation' => ['required'],
            '*.gender' => ['required'],
            '*.date_of_birth' => ['required'],
            '*.sum_insured' => ['required', 'numeric'],
            '*.permanent_disablement' => ['required', 'numeric'],
            '*.medical_expense' => ['required', 'numeric'],
            '*.age' => ['required'],
            '*.relationship' => ['nullable'],
            '*.class' => [Rule::in(WorkingClass::codeList())]
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        } elseif (count($data) == 0) {
            throw ValidationException::withMessages([
                'file' => ['File contains no insured person']
            ]);
        }

        return true;
    }
    public function optionExtensionData($id)
    {
        return BnfExtensionData::whereDataId($id)->with(['benefit' => fn($q) => $q->select('name AS label', 'id', 'code AS value', 'description')])->get();
    }
    public function updateOtpExt($data, $id)
    {
        $master = $this->dataMaster->findOr($id, fn() => throw new ModelNotFoundException("Record not found"));
        $extensionData = array_column(array_map(function ($item) {
            return array_intersect_key($item, array_flip(['extension_id', 'extension_code', 'extension_name', 'extension_description', 'is_selected', 'amount_type', 'rating']));
        }, $data), null, 'extension_id');
        $master->extensions()->sync($extensionData);
    }
    public function businessInfo($businessCode)
    {
        return BusinessChannel::whereBusinessCode($businessCode)->firstOr(fn() => throw new ModelNotFoundException("Business not found"));
    }
    public function businessTypeOptions()
    {
        return RefEnum::select('enum_id', 'name')
            ->whereGroupId('POLICY_CONFIG')
            ->whereTypeId('BUSINESS_TYPE')
            ->select('name AS label', 'enum_id AS value')->get()->toArray();
    }
    public function policyTypeOptions()
    {
        return RefEnum::select('enum_id', 'name')
            ->whereGroupId('POLICY_CONFIG')
            ->whereTypeId('POLICY_TYPE')
            ->select('name AS label', 'enum_id AS value')->get()->toArray();
    }
    public function reinsuranceGroupOptions()
    {
        return ReinsurancePartnerGroup::whereHas('partners', fn($q) => $q->whereStatus(RecordStatus::ACTIVE))->select('name AS label', 'code AS value')->get();
    }
    public function reinsurancedefaultGroups($productCode)
    {
        return ReinsuranceConfig::whereProductCode($productCode)->whereStatus(RecordStatus::ACTIVE)->whereLvl(1)->whereDate('start_to', '>=', Carbon::now())->pluck('partner_code')->toArray();
    }
    public function participantOptions()
    {
        return ReinsurancePartner::whereStatus(RecordStatus::ACTIVE)->whereNotNull('group_code')->select('name AS label', 'code AS value', 'group_code')->get();
    }
    public function endorsementOptions()
    {
        return RefEnum::whereTypeId('PA_ENDORSEMENT_TYPE')
            ->whereGroupId('POLICY_CONFIG')
            ->select('name AS label', 'enum_id AS value')->get();
    }
}
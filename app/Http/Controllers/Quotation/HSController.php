<?php

namespace App\Http\Controllers\Quotation;

use App\Exports\HS\DataDetailExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\HS\QuotationRequest;
use App\Imports\HS\DataImport;
use App\Models\Address\AddressCode;
use App\Models\BusinessManagement\BusinessChannel;
use App\Models\CustomerManagement\Country;
use App\Models\CustomerManagement\Customer;
use App\Models\HS\DataMaster;
use App\Models\HS\Quotation;
use App\Models\HS\QuotationView;
use App\Models\Insurance\InsuranceClause;
use App\Models\Product\Product;
use App\Models\ProductLine\ProductLine;
use App\Models\RefEnum;
use App\Services\HS\DataDetailService;
use App\Services\HS\DataMasterService;
use App\Services\HS\PlanDataDetailService;
use App\Services\HS\PlanDataService;
use App\Services\HS\QuotationService;
use App\Services\HS\SchemaDataService;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HSController extends Controller
{
    use DataTable;

    const JOINT = 'J';

    public function __construct(
        private DataMasterService $dataMasterService,
        private DataDetailService $dataDetailService,
        private PlanDataService $planDataService,
        private PlanDataDetailService $planDataDetailService,
        private SchemaDataService $schemaDataService,
        private QuotationService $quotationService,
    ) {
        $this->middleware('has-permission:HS_QUOTATION.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:HS_QUOTATION.NEW')->only(['store']);
        $this->middleware('has-permission:HS_QUOTATION.APPROVE')->only(['approve']);
        $this->middleware('has-permission:HS_QUOTATION.ACCEPT')->only(['accept']);
        $this->middleware('has-permission:HS_QUOTATION.DELETE')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            QuotationView::with([
                'quotation' => function ($query) {
                    $query->select(
                        'data_id',
                        'approved_at',
                        'approved_status',
                        'approved_reason',
                        'accepted_status',
                        'accepted_reason'
                    );
                }
            ])
                ->whereNotNull('quotation_no')
                ->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuotationRequest $request)
    {
        try {
            DB::beginTransaction();
            // Upload file
            $masterDataId = $this->upload($request->file);

            // Updating data master from additional fields in form
            info('Uploaded master data id: ' . $masterDataId);
            $masterData = [
                'sale_channel' => $request->sale_channel,
                'business_code' => $request->business_code,
                'insured_name' => $request->insured_name,
                'insured_name_kh' => $request->insured_name_kh,
                'warranty' => $request->warranty,
                'warranty_kh' => $request->warranty_kh,
                'memorandum' => $request->memorandum,
                'memorandum_kh' => $request->memorandum_kh,
                'subjectivity' => $request->subjectivity,
                'subjectivity_kh' => $request->subjectivity_kh,
                'remark' => $request->remark,
                'remark_kh' => $request->remark_kh,
                'policy_wording_version' => $request->policy_wording_version,
                'insured_person_note' => $request->insured_person_note,
                'insured_person_note_kh' => $request->insured_person_note_kh,
                'commission_rate' => $request->commission_rate,
                'handler_code' => $request->handler_code,
            ];
            $this->dataMasterService->update($masterData, $masterDataId);

            //calc effective_month, effective_day, insurance_period_val
            $params = [
                $masterDataId,
            ];
            DB::select("select * from ins_hs_prod_gen_period_val (?)", $params);

            // Create joint details
            if ($request->joint_status == self::JOINT) {
                $jointDetails = collect($request->joint_details)->map(function ($item) use ($masterDataId) {
                    unset($item['customer_type']);

                    $item['product_line_code'] = ProductLine::MEDICAL;
                    $item['product_code'] = DataMaster::find($masterDataId)->product_code;
                    $item['data_id'] = $masterDataId;

                    return $item;
                })->toArray();
                info('Create joint details', $jointDetails);
                DataMaster::find($masterDataId)->jointAccountDetails()->createMany($jointDetails);
            }

            // Assign Clauses
            DataMaster::find($masterDataId)->insuranceClauses()->syncWithPivotValues([
                ...$request->endorsement_clauses,
                ...$request->general_exclusions,
                $request->geographical_limit,
            ], ['status' => 'ACT']);

            // Call to generate quote
            $this->quotationService->generateNewQuotation($masterDataId, auth()->id());

            DB::commit();
            return response([
                'success' => true,
                'message' => 'Quote is created successfully.'
            ], 200);
        } catch (\Exception $ex) {
            Db::rollBack();
            report($ex);
            return response([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function upload($file)
    {
        info('Uploading H&S file ===================');

        $data = (new DataImport)
            ->toArray($file);
        [











            ,
            ,
        ,
            $importedMasterData,
            $importedDataDetail,
            $importedPlanData,
            $importedPlanDataDetail,
            $importedSchemaData,
        ] = $data;


        // Create Master Data
        $createdMasterData = $this->createMasterDataFromSheet($importedMasterData);

        if ($createdMasterData->id) {
            // Create Data Details
            $this->createDataDetailsFromSheet($importedDataDetail, $createdMasterData);

            // Create Plan Data
            $schemaTypeIds = $this->createPlanDataFromSheet($importedPlanData, $createdMasterData);

            if ($schemaTypeIds) {
                // Create Plan Data Details
                $this->createPlanDataDetailsFromSheet($importedPlanDataDetail, $schemaTypeIds);
            }

            // Create Schema Data
            $this->createSchemaDataFromSheet($importedSchemaData, $createdMasterData);

            return $createdMasterData->id;
        }
    }

    private function createMasterDataFromSheet($data)
    {
        // Only one master data record per import
        $masterData = collect($data)->first();

        // Format date from excel
        $masterData['effective_date_from'] = $this->getDateFromExcel($masterData['effective_date_from']);
        $masterData['effective_date_to'] = $this->getDateFromExcel($masterData['effective_date_to']);
        $masterData['created_by'] = auth()->id();

        return $this->dataMasterService->create($masterData);
    }

    private function createDataDetailsFromSheet($data, $masterData)
    {
        collect($data)->each(function ($item) use ($masterData) {
            $item['master_data_id'] = $masterData->id;
            $item['master_data_type'] = $masterData->data_type;
            $item['product_code'] = $masterData->product_code;
            $item['date_of_birth'] = $this->getDateFromExcel($item['date_of_birth']);
            $item['created_by'] = auth()->id();

            $this->dataDetailService->create($item);
        });
    }

    private function createPlanDataFromSheet($data, $masterData)
    {
        return collect($data)->map(function ($item) use ($masterData) {
            $item['master_data_id'] = $masterData->id;
            $item['created_by'] = auth()->id();

            return $this->planDataService->create($item);
        })->pluck('id', 'schema_type');
    }

    private function createPlanDataDetailsFromSheet($data, $schemaTypeIds)
    {
        collect($data)->each(function ($item) use ($schemaTypeIds) {
            // Get plan_id from
            $planId = optional($schemaTypeIds)[$item['plan_type']];

            // If doesn't match with planId, skip the record
            if (!$planId) {
                info('Skip', $item);
                return true;
            }
            $item['plan_id'] = optional($schemaTypeIds)[$item['plan_type']];
            $item['created_by'] = auth()->id();

            $this->planDataDetailService->create($item);
        });
    }

    private function createSchemaDataFromSheet($data, $masterData)
    {
        collect($data)->each(function ($item) use ($masterData) {
            $item['master_data_id'] = $masterData->id;
            $item['created_by'] = auth()->id();

            $this->schemaDataService->create($item);
        });
    }

    private function getDateFromExcel($dateString)
    {
        return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->quotationService->getDataDetail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getLovs()
    {
        $saleChannels = RefEnum::where('group_id', 'BUSINESS_CHANNEL')
            ->where('type_id', 'SALE_CHANNEL')
            ->get()
            ->map(fn($item) => [
                'label' => $item->name,
                'value' => $item->enum_id,
            ]);
        $jointStatuses = [
            [
                'value' => 'S',
                'label' => 'Single',
            ],
            [
                'value' => 'J',
                'label' => 'Joint',
            ],
        ];
        $jointLevels = [
            [
                'value' => 'PRIMARY',
                'label' => 'PRIMARY',
            ],
            [
                'value' => 'SECONDARY',
                'label' => 'SECONDARY',
            ],
        ];
        $permissions = [
            [
                'value' => 'FULL',
                'label' => 'FULL',
            ],
        ];
        $customerTypes = RefEnum::where('group_id', 'CUSTOMER_TYPE')
            ->get()
            ->map(fn($item) => [
                'label' => $item->name,
                'value' => $item->enum_id,
            ]);



        return [
            'sale_channels' => $saleChannels,
            'joint_statuses' => $jointStatuses,
            'customer_types' => $customerTypes,
            'joint_levels' => $jointLevels,
            'permissions' => $permissions,
            ...$this->getClausesLovs()
        ];
    }

    private function getClausesLovs()
    {
        $baseQuery = InsuranceClause::select('id', 'clause', 'default_inclusion')
            ->where('product_line_code', ProductLine::MEDICAL)
            ->where('status', 'ACT');

        $defaultEndorsementClauses = (clone $baseQuery)->where('clause_type', InsuranceClause::ENDORSEMENT_CLAUSE)
            ->where('default_inclusion', InsuranceClause::DEFAULT_YES)
            ->pluck('id');

        $defaultGeneralExclusions = (clone $baseQuery)->where('clause_type', InsuranceClause::GENERAL_EXCLUSION)
            ->where('default_inclusion', InsuranceClause::DEFAULT_YES)
            ->pluck('id');

        $endorsementClauses = (clone $baseQuery)->select('id', 'clause', 'default_inclusion')
            ->where('clause_type', InsuranceClause::ENDORSEMENT_CLAUSE)
            ->orderBy('sequence', 'asc')
            ->get()
            ->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->clause,
            ]);

        $generalExclusions = (clone $baseQuery)->select('id', 'clause', 'default_inclusion')
            ->where('clause_type', InsuranceClause::GENERAL_EXCLUSION)
            ->orderBy('sequence', 'asc')
            ->get()
            ->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->clause,
            ]);

        $geographicalLimits = (clone $baseQuery)->select('id', 'clause')
            ->where('clause_type', InsuranceClause::GEOGRAPHICAL_LIMIT)->whereProductLineCode('MEDICAL')
            ->orderBy('sequence', 'asc')
            ->get()
            ->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->clause,
            ]);

        return [
            'default_endorsement_clauses' => $defaultEndorsementClauses,
            'default_general_exclusions' => $defaultGeneralExclusions,
            'endorsement_clauses' => $endorsementClauses,
            'general_exclusions' => $generalExclusions,
            'geographical_limits' => $geographicalLimits,
        ];
    }

    public function getBusinessChannelsLov($saleChannelId)
    {
        return BusinessChannel::select('business_code', 'full_name')
            ->where('sale_channel', $saleChannelId)
            ->where('status', BusinessChannel::ACTIVE)
            ->orderBy('business_code')->get()->map(fn($item) => [
                'value' => $item->business_code,
                'label' => $item->business_code . ' - ' . $item->full_name
            ]);
    }

    public function getCustomersLov($type)
    {
        return Customer::listCustomersByType($type);
    }

    public function approve(Request $request, $id)
    {
        if (auth()->id() == $this->getMakerId($id)) {
            abort(403, "Maker can not approve their own records.");
        }

        $quote = $this->quotationService->findByDataId($id);

        $quoteId = $quote?->id;

        $data = [
            'approved_status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approved_reason' => $request->reason,
            ...($request->status === Quotation::APPROVED ? ['accepted_status' => Quotation::PENDING] : []),
        ];

        $updated = $this->quotationService->update($data, $quoteId, true);

        if ($updated) {
            return response([
                'success' => true,
                'message' => 'Approved successfully'
            ]);
        } else {
            throw new \Exception("Unexpected Error", 400);
        }
    }

    public function accept(Request $request, $id)
    {
        if (auth()->id() == $this->getMakerId($id)) {
            abort(403, "Maker can not approve their own records.");
        }

        $quote = $this->quotationService->findByDataId($id);

        $quoteId = $quote?->id;

        $data = [
            'accepted_status' => $request->status,
            'accepted_by' => auth()->id(),
            'accepted_at' => now(),
            'accepted_reason' => $request->reason,
        ];

        $updated = $this->quotationService->update($data, $quoteId, true);

        if ($updated) {
            return response([
                'success' => true,
                'message' => 'Accepted successfully'
            ]);
        } else {
            throw new \Exception("Unexpected Error", 400);
        }
    }

    private function getMakerId($masterDataId)
    {
        $quote = $this->quotationService->findByDataId($masterDataId);
        return $quote?->updated_by ?: $quote?->created_by;
    }

    public function downloadQuotation($id, $lang)
    {
        App::setLocale($lang);
        $hs = $this->quotationService->getDataDetail($id);
        // fix error when there is a record but empty file_url
        if (isset($hs['signature']) && !$hs['signature']['file_url']) {
            $hs['signature'] = null;
        }

        $hs['hasLetterHead'] = request()->letterhead;
        $documentNo = $hs['quotation_no'] ?? '';
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadView('pdf.quotations.hs', compact('hs'));

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
        if ($documentNo == '') {
            return $pdf->stream('Quotation No.pdf');
        } else {
            return $pdf->stream($documentNo . '.pdf');
        }
    }

    public function proceedToPolicy(Quotation $quotation)
    {
        try {
            if (!$quotation) {
                return response('Not found', 404);
            }
            $params = [
                $quotation->id,
                auth()->id(),
            ];
            $generated = DB::select("select * from ins_hs_prod_generate_new_policy (?,?)", $params);
            if ($generated[0]->status == 'SUC') {
                return response([
                    'success' => true,
                    'message' => $generated[0]->message,
                    'data' => $generated
                ], 200);
            } else {
                return response([
                    'success' => false,
                    'message' => $generated[0]->message,
                    'data' => $generated
                ], 422);
            }
        } catch (\Exception $ex) {
            report($ex);
            Db::rollBack();
            return response([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function showDetail($id)
    {
        $hs = $this->quotationService->getDataDetail($id);

        return $hs;
    }

    private function hsDetail($id)
    {
        $hs = DataMaster::with([
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
            ->where('customer_no', $hs->customer_no)
            ->first();
        $addressData = AddressCode::select('province', 'district', 'commune')->where('postal_code', $customer->postal_code)->first();
        $country = Country::select('description')->where('country_code', $hs->customer->country_code)->value('description');

        $hs->customer_type = $customer->customer_type;
        $hs->customer_classification = optional($customer->customerClassification)->description;
        $hs->customer_classification_kh = optional($customer->customerClassification)->description_kh;
        $hs->endorsement_clause = $this->getInsuranceClausesById($hs->id, 'ENDORSEMENT');
        $hs->general_exclusive = $this->getInsuranceClausesById($hs->id, 'EXCLUSION');

        $hs->addressData = $addressData;
        $hs->country = $country;

        if ($hs->updated_by) {
            $hs->issued_by = $hs->issuedByName($hs->updated_by);
        } elseif ($hs->created_by) {
            $hs->issued_by = $hs->issuedByName($hs->created_by);
        } else {
            $hs->issued_by = null;
        }
        return $hs;
    }

    private function getInsuranceClausesById($hsId, $clauseType)
    {
        return DataMaster::find($hsId)->insuranceClauses()
            ->select('clause', 'clause_kh', 'clause_zh', 'ins_insurance_clause.id')
            ->where('clause_type', $clauseType)
            ->orderBy('sequence')
            ->get();
    }

    public function getProductCodeByUploadExcel(Request $request)
    {
        try {
            $data = (new DataImport)
                ->toArray($request->file);
            [







                ,
                ,
            ,
                $importedMasterData,
                $importedDataDetail,
                $importedPlanData,
                $importedPlanDataDetail,
                $importedSchemaData,
            ] = $data;
            $selectedInsured['insured_name'] = $importedMasterData[0]['insured_name'];
            $selectedInsured['insured_name_kh'] = $importedMasterData[0]['insured_name_kh'];
            $product = Product::whereCode($importedMasterData[0]['product_code'])->firstOr(fn() => throw new \Exception("Incorrect product code"));
            $customer = Customer::whereCustomerNo($importedMasterData[0]['customer_no'])->firstOr(fn() => throw new \Exception("Incorrect customer no"));
            return response()->json(['product_code' => $importedMasterData[0]['product_code'], 'insured_persons' => collect($importedDataDetail)->map(fn($item) => collect($item)->only(['name'])), 'selected_insured' => $selectedInsured]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function getProductCodeFromUploadExcel(Request $request, \App\Services\HS\ExcelReaderService $excelReaderService)
    {
        try {
            $filePath = $request->file('hs_quote')->path();
            $data = $excelReaderService->readHsQuoteData($filePath);

            $product = Product::whereCode($data['product_code'])
                ->firstOr(fn() => throw new \Exception("Incorrect product code"));
            $customer = Customer::whereCustomerNo($data['customer_no'])
                ->firstOr(fn() => throw new \Exception("Incorrect customer no"));

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function findBusinessChannel($businessCode)
    {
        return BusinessChannel::where('business_code', $businessCode)->first();
    }

    public function updateIssuedDate(DataMaster $hs)
    {
        // Manually update updated_at for showing issue date
        $hs->updated_at = now();

        if ($hs->save()) {
            return [
                'success' => true,
                'message' => 'Issue Date is successfully updated.'
            ];
        }
    }
    public function exportInsuredPersons($masterId, $quotationNo)
    {
        return Excel::download(new DataDetailExport($masterId), $quotationNo . '-insured-name.xlsx');
    }
}

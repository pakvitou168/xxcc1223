<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductConfiguration\CoverPackage\CoverPackageServiceController;
use App\Http\Controllers\ProductConfiguration\NoClaimDiscount\NoClaimDiscountServiceController;
use App\Models\BusinessManagement\BusinessChannel;
use App\Models\Cover\Cover;
use App\Models\CoverPackage\CoverPackage;
use App\Models\CustomerManagement\Customer;
use App\Models\Deductible\DeductibleDetail;
use App\Models\Insurance\Auto;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\Auto\AutoTemp;
use App\Models\Insurance\InsuranceClause;
use App\Models\Product\Product;
use App\Models\RefEnum;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\QuotationExport;
use App\Models\BusinessManagement\BusinessHandler;
use App\Models\ProductConfiguration\PolicyWordingVersion;
use App\Models\ProductConfiguration\VehicleUsage;
use App\Models\UserManagement\User\User;
use App\Imports\VehiclesImport;
use App\Models\Insurance\Quotation;
use App\Exports\VehiclesExport;
use Illuminate\Support\Facades\App;

class AutoServiceController extends Controller
{
    public function getServices()
    {
        $product = Product::listAutoProducts();

        $calculateOptions = RefEnum::where('group_id', 'PROD_CONFIG')
            ->where('type_id', 'CALC_OPTION')->select('enum_id AS label', 'enum_id AS value')->get();

        $periodTypes = RefEnum::where('group_id', 'PROD_CONFIG')
            ->where('type_id', 'INSURANCE_PERIOD_TYPE')->select('name as label', 'enum_id AS value')->get();
        $customerTypes = RefEnum::where('group_id', 'CUSTOMER_TYPE')->select('name as label', 'enum_id as value')->get();

        $endorsementClauses = $this->listClausesByType('ENDORSEMENT');
        $generalExclusions = $this->listClausesByType('EXCLUSION');

        $defaultEndorsementClauses = $this->listDefaultClausesByType('ENDORSEMENT');
        $defaultGeneralExclusions = $this->listDefaultClausesByType('EXCLUSION');

        $joinLevels = RefEnum::where('group_id', 'JOINT_DETAIL_CONF')
            ->where('type_id', 'JOINT_LEVEL')->select('name as label', 'enum_id AS value')->get();
        $permissionLevels = RefEnum::where('group_id', 'JOINT_DETAIL_CONF')
            ->where('type_id', 'PERMISSION_LEVEL')->select('name as label', 'enum_id AS value')->get();

        return [
            'productOptions' => $product,
            'calculateOptions' => $calculateOptions,
            'periodTypes' => $periodTypes,
            'customerTypes' => $customerTypes,
            'endorsementClauses' => $endorsementClauses,
            'generalExclusions' => $generalExclusions,
            'defaultEndorsementClauses' => $defaultEndorsementClauses,
            'defaultGeneralExclusions' => $defaultGeneralExclusions,
            'jointDetailsConfig' => [$joinLevels, $permissionLevels]
        ];
    }

    public function listCustomersByType($type)
    {
        return Customer::select('name_en', 'customer_no','name_kh')->where('customer_type', $type)
            ->where('status', 'ACT')->orderBy('customer_no')->get()->map(function ($item) {
                return [
                    'label' => $item->customer_no . ' - ' . $item->name_en,
                    'value' => $item->customer_no,
                    'name_en' => $item->name_en,
                    'name_kh' => $item->name_kh
                ];
            });
    }

    private function listClausesByType($clauseType)
    {
        return $this->getInsuranceClauses($clauseType)->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->clause
            ];
        });
    }

    private function listDefaultClausesByType($clauseType)
    {
        return $this->getInsuranceClauses($clauseType)->filter(function ($item) {
            return $item->default_inclusion == 'Y';
        })->map(function ($item) {
            return strval($item->id);
        })->values();
    }

    private function getInsuranceClauses($clauseType)
    {
        return InsuranceClause::select('id', 'clause', 'default_inclusion')
            ->where('product_line_code', 'AUTO')
            ->where('clause_type', $clauseType)
            ->orderBy('sequence', 'asc')
            ->where('status', 'ACT')->get();
    }

    public function listMakesByProduct($productCode)
    {
        $params = [
            $productCode,
            auth()->id(),
        ];
        return collect(DB::select("select * from ins_get_vehicle_make_list(?,?)", $params))->map(function ($item) {
            return [
                'label' => $item->make,
                'value' => $item->id
            ];
        });
    }

    public function listMakesVehicleUploadByProduct($productCode, Request $request)
    {
        $makeLabelList = collect($request->rows)->map(function ($item) {
            return $item[0];
        })->unique()->values()->all();
        return $this->listMakesByProduct($productCode)->whereIn('label', $makeLabelList);
    }

    public function listModelsByProductAndMake($productCode, $makeId)
    {
        $params = [
            $productCode,
            $makeId,
            auth()->id(),
        ];
        return collect(DB::select("select * from ins_get_vehicle_model_list(?,?,?)", $params))->map(function ($item) use ($makeId) {
            return [
                'label' => $item->model,
                'value' => $item->id,
                'makeId' => $makeId
            ];
        });
    }

    public function listModelsVehicleUploadByProductAndMake($productCode, Request $request)
    {
        $modelList = collect();
        $makeList = collect($request->makeList);
        $modelLabelList = collect($request->rows)->map(function ($item) {
            return $item[1];
        });
        foreach ($makeList as $make) {
            $modelList = $modelList->concat($this->listModelsByProductAndMake($productCode, $make['value'])->whereIn('label', $modelLabelList));
        }
        return $modelList;
    }

    public function listProductCovers($productCode)
    {
        return Cover::select('name', 'code', 'mandatory', 'is_required_vehicle_val')
            ->where('product_code', $productCode)
            ->where('type', 'C')
            ->where('status', 'ACT')
            ->orderBy('seq')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->name . ' (' . $item->code . ')',
                    'value' => $item->code,
                    'disabled' => $item->mandatory,
                    'is_vehicle_val_required' => $item->is_required_vehicle_val ?? false
                ];
            });
    }

    public function listProductMandatoryCovers($productCode)
    {
        return Cover::where('product_code', $productCode)
            ->select('name', 'code')
            ->where('type', 'C')
            ->where('mandatory', true)
            ->where('status', 'ACT')
            ->orderBy('seq')
            ->pluck('code');
    }

    public function findBusinessChannel($businessCode)
    {
        return BusinessChannel::where('business_code', $businessCode)->first();
    }

    public function listBusinessChannelsByCategory($businessCategory)
    {
        return BusinessChannel::select('business_code', 'full_name')
            ->where('sale_channel', $businessCategory)
            ->where('status', 'ACT')
            ->orderBy('business_code')->get()->map(function ($item) {
                return [
                    'value' => $item->business_code,
                    'label' => $item->business_code . ' - ' . $item->full_name
                ];
            });
    }

    public function getInsuredNames($lang)
    {
        $insuredNames = collect(request()->customersNo)->filter(function ($item) {
            return $item != null;
        })->map(function ($item) use ($lang) {
            $customer = Customer::select('name_en', 'name_kh')->where('customer_no', $item)->first();
            if ($lang === 'kh') {
                return $customer ? $customer->name_kh : null;
            }
            return $customer ? $customer->name_en : null;
        })->join(', ');

        return Str::upper($insuredNames);
    }

    public function listCoverPackages($productCode)
    {
        return CoverPackage::select('id', 'name', 'description')
            ->where('product_code', $productCode)
            ->where('status', 'ACT')
            ->get()
            ->map(function ($item) {

                $coversStr = $this->getPackageCoversStr($item);

                return [
                    'label' => $item->name . ' (' . $coversStr . ')',
                    'value' => $item->id,
                    'disabled' => false,
                ];
            });
    }

    private function getPackageCoversStr($package)
    {
        return $package->coverPackageComponents()
            ->select('comp_code')
            ->pluck('comp_code')
            ->join(', ');
    }

    public function listRemainCoversFromPackage($packageId, $productCode)
    {
        $covers = Cover::where('product_code', $productCode)
            ->where('type', 'C')
            ->where('status', 'ACT')
            ->orderBy('seq')
            ->pluck('code');

        if ($packageId && is_numeric($packageId)) {
            $coverPackage = CoverPackage::find($packageId);
            $packageCovers = $coverPackage->coverPackageComponents()->pluck('comp_code');

            $remainCoversArr = $covers->diff($packageCovers)->values();

            return $this->listCoversByCodes($coverPackage->product_code, $remainCoversArr);
        }

        return $this->listCoversByCodes($productCode, $covers);
    }

    private function listCoversByCodes($productCode, $coverCodes)
    {
        return Cover::select('name', 'code', 'mandatory', 'is_required_vehicle_val')
            ->where('product_code', $productCode)
            ->whereIn('code', $coverCodes)
            ->where('type', 'C')
            ->where('status', 'ACT')
            ->orderBy('seq')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->name . ' (' . $item->code . ')',
                    'value' => $item->code,
                    'disabled' => $item->mandatory,
                    'is_vehicle_val_required' => $item->is_required_vehicle_val ?? false
                ];
            });
    }

    public function getDeductibleDetails(Auto $auto)
    {
        // Get all vehicle if it is an endorsement
        $isEndorsement = $auto->endorsement_type != null;
        if ($isEndorsement) {
            $autoDetailIds = $auto->allAutoDetails()->select('id')->pluck('id');
        } else {
            $autoDetailIds = $auto->autoDetails()->select('id')->pluck('id');
        }

        return DeductibleDetail::with(
            [
                'cover' => function ($query) use ($auto) {
                    $query->select('code', 'deductible_label', 'seq')
                        ->where('type', 'C')
                        ->where('product_code', $auto->product_code)
                        ->where('status', 'ACT');
                },
                'autoDetail' => function ($query) {
                    $query->with([
                        'makeModel' => function ($query) {
                            $query->with('make:id,make')
                                ->select('id', 'model', 'make_id');
                        }
                    ])
                        ->select('id', 'selected_cover_pkg', 'vehicle_value', 'model_id', 'vehicle_value');
                }
            ]
        )
            ->select('id', 'product_code', 'detail_id', 'comp_code', 'value', 'cond_value', 'min_value', 'cond_value_type', 'value_label')
            ->whereIn('detail_id', $autoDetailIds)
            ->get()
            ->sortBy('cover.seq')
            ->values()
            ->map(function ($item) {
                $item->deductible_label = optional($item->cover)->deductible_label;
                return $item;
            })
            ->groupBy('detail_id');
    }

    private function listAutoSubClasses()
    {
        return Product::select('code', 'alt_code')->where('product_line_code', 'AUTO')->where('status', 'ACT')->get()->pluck('alt_code', 'code');
    }

    public function exportQuotation(Request $request)
    {

        return Excel::download(
            new QuotationExport(
                $request->route('issue_date_from'),
                $request->route('issue_date_to'),
                $this->listAutoSubClasses(),
                DB::table('ins_business_channel_pc_v')->get()->pluck('full_name', 'business_code'),
                BusinessHandler::select('handler_code', 'name')->where('status', 'ACT')->get()->pluck('name', 'handler_code'),
                User::select('id', 'full_name')->where('status', 'ACT')->get()->pluck('full_name', 'id')
            ),
            'Auto Quotation.xlsx'
        );
    }

    public function getProductSpecification($productCode)
    {
        return Product::getProductSpecificationByCode($productCode);
    }

    public function listVehicleUsageByProductCode($productCode)
    {
        return VehicleUsage::listByProductCode($productCode);
    }

    public function listPolicyWordingVersionByProductCode($productCode)
    {
        return PolicyWordingVersion::listByProductCode($productCode);
    }

    public function getDefaultSurcharge($productCode)
    {
        return Product::getDefaultSurchargeByProductCode($productCode);
    }

    public function getDefaultDiscount($productCode)
    {
        return Product::getDefaultDiscountByProductCode($productCode);
    }

    public function getDefaultNCD($productCode)
    {
        return Product::getDefaultNCDByProductCode($productCode);
    }

    public function getProductCode(Auto $auto)
    {
        return $auto->product_code;
    }

    public function getDefaultEndorsementEffectiveDate(Auto $auto)
    {
        return $auto->endorsement_e_date;
    }

    public function getTotalPremium(Auto $auto)
    {
        return $auto->total_premium;
    }

    public function getQuotationDocumentNo($masterDataId)
    {
        return Quotation::where('data_id', $masterDataId)->value('document_no');
    }

    public function fileVehicleImport(Request $request)
    {
        $vehicleUsageOptions = VehicleUsage::getNameByProductCode($request->productCode);
        $makeList = $this->listMakesByProduct($request->productCode);
        $productSpecification = Product::getProductSpecificationByCode($request->productCode);
        $negotiationRate = Auto::where('id', $request->master_data_id)->value('negotiation_rate');
        AutoTemp::where('master_data_id', $request->master_data_id)->delete();
        Excel::import(
            new VehiclesImport(
                $request->productCode,
                $vehicleUsageOptions,
                $makeList,
                $productSpecification,
                $request->master_data_type,
                $request->master_data_id,
                $negotiationRate
            ),
            $request->file('file')
        );
        if (AutoTemp::where('master_data_id', $request->master_data_id)->count() > 0) {
            return [
                'success' => true,
                'message' => 'Success',
                'count' => AutoTemp::where('master_data_id', $request->master_data_id)->count(),
            ];
        }
    }

    public function saveToAutoDetail(Request $request)
    {
        $this->deleteExistingVehicleBeforeSaveUpload($request->master_data_id);
        AutoTemp::where('master_data_id', $request->master_data_id)->get()
            ->map(function ($item) {
                $autoDetail = new AutoDetail();

            $autoDetail->product_code = $item->product_code;
            $autoDetail->master_data_type = $item->master_data_type;
            $autoDetail->master_data_id = $item->master_data_id;
            $autoDetail->model_id = $item->model_id;
            $autoDetail->plate_no = optional($item)->plate_no;
            $autoDetail->chassis_no = optional($item)->chassis_no;
            $autoDetail->engine_no = optional($item)->engine_no;
            $autoDetail->manufacturing_year = $item->manufacturing_year;
            $autoDetail->cubic = optional($item)->cubic;
            $autoDetail->vehicle_value = $item->vehicle_value;
            $autoDetail->passenger = optional($item)->passenger;
            $autoDetail->tonnage = optional($item)->tonnage;
            $autoDetail->commercial_vehicle_type = optional($item)->commercial_vehicle_type;
            $autoDetail->surcharge = optional($item)->surcharge;
            $autoDetail->discount = optional($item)->discount;
            $autoDetail->ncd = optional($item)->ncd;
            $autoDetail->selected_cover_pkg = $item->selected_cover_pkg;
            $autoDetail->negotiation_rate = $item->negotiation_rate;
            $autoDetail->remark = optional($item)->remark;
            $autoDetail->vehicle_usage = $item->vehicle_usage;
            $autoDetail->vehicle_uuid = Str::uuid();
            $autoDetail->save();
        });
        if(AutoTemp::where('master_data_id', $request->master_data_id)->delete()){
            return [
                'autoData' => [
                    'product_code' => $request->productCode,
                    'request_id' => $request->master_data_id
                ],
                'success' => true,
                'message' => 'Auto Quotation is successfully updated.'
            ];
        }
    }

    public function deleteVehicleUpload(Request $request)
    {
        if (AutoTemp::where('master_data_id', $request->master_data_id)->delete()) {
            return [
                'success' => true,
                'message' => 'Success'
            ];
        }
    }

    private function deleteExistingVehicleBeforeSaveUpload($masterDataId)
    {
        DB::table('ins_auto_data_detail')
            ->where('master_data_id', $masterDataId)
            ->update([
                'status' => 'DEL'
            ]);
    }

    public function uploadVehicleTemplate(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $destinationPath = 'uploads';
            $fileUrl = $destinationPath . '/upload_vehicle_template.xlsm';
            if ($file->move($destinationPath, $fileUrl))
                return [
                    'success' => true,
                    'message' => 'Vehicle Template is uploaded successfully'
                ];
        } else {
            return response()->json(['success' => false, 'message' => 'File not uploaded'], 500);
        }
    }

    public function downloadQuotation($id, $lang)
    {

        App::setLocale($lang);
        $autoData = (new AutoController)->showDetail($id);
        // fix error when there is a record but empty file_url
        if ($autoData['signature'])
            if (!$autoData['signature']['file_url'])
                $autoData['signature'] = null;

        $autoData['hasLetterHead'] = request()->letterhead;
        $documentNo = $autoData['auto']->quotation ? $autoData['auto']->quotation->document_no : '';

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadView('pdf.quotations.auto', $autoData);

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

    public function exportVehicles($id, $document_no)
    {
        return Excel::download(new VehiclesExport($id, $document_no), $document_no . '.xlsx');
    }
    public function vehicleEnums(Request $request)
    {
        $keys = $request->keys;
        $data = [
            'cover_packages' => $this->listCoverPackages($request->product_code),
            'optional_coverages' => app(CoverPackageServiceController::class)->listProductCovers($request->product_code),
            'makes' => $this->listMakesByProduct($request->product_code),
            'ncd' => app(NoClaimDiscountServiceController::class)->listNoClaimDiscounts($request->product_code),
            'product_specs' => $this->getProductSpecification($request->product_code),
            'vehicle_usages' => $this->listVehicleUsageByProductCode($request->product_code),
            'mandatory_covers' => $this->listProductMandatoryCovers($request->product_code)
        ];
        $filteredData = array_intersect_key($data, array_flip($keys));
        return response()->json($filteredData);
    }
}

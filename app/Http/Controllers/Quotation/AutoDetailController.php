<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutoDetailRequest;
use App\Models\CoverPackage\CoverPackage;
use App\Models\Insurance\Auto;
use App\Models\Insurance\AutoDetail;
use App\Models\Insurance\BasePolicy;
use App\Models\Make\MakeModel;
use App\Models\Product\Product;
use Exception;
use Illuminate\Http\Request;
use App\Traits\DataTable;
use App\Scopes\ActiveScope;
use Illuminate\Support\Facades\DB;
use Str;

class AutoDetailController extends Controller
{

    use DataTable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->isEndorsement)
            return $this->generateTableData(
                AutoDetail::withoutGlobalScopes([ActiveScope::class])->with([
                    'makeModel' => function ($query) {
                        $query->with('make:id,make')
                            ->select('id', 'model', 'make_id');
                    }
                ])
                    ->where('master_data_id', $request->master_data_id)
                    ->orderBy('id')
            );
        else
            return $this->generateTableData(
                AutoDetail::with([
                    'makeModel' => function ($query) {
                        $query->with('make:id,make')
                            ->select('id', 'model', 'make_id');
                    }
                ])
                    ->where('master_data_id', $request->master_data_id)
                    ->where('status', 'ACT')
                    ->orderBy('id')
            );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AutoDetailRequest $request)
    {
        try {
            DB::beginTransaction();
            $autoDetail = new AutoDetail();
            $this->assignValues($request, $autoDetail);
            $autoDetail->save();
            DB::commit();
            return response()->json([
                'autoData' => [
                    'product_code' => $autoDetail->product_code,
                    'request_id' => $autoDetail->master_data_id
                ],
                'id' => $autoDetail->master_data_id,
                'vehicle_id' => $autoDetail->id,
                'success' => true,
                'message' => 'Record is created.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    public function saveEndorsementNewVehicle(AutoDetailRequest $request, Auto $auto)
    {
        // If is add/deletion endorsement
        if ($auto->endorsement_type === 'VEHICLE') {
            // Manually update updated_at for showing issue date
            $auto->updated_at = now();
            if ($auto->save()) {
                $autoDetail = new AutoDetail();
                $this->assignAutoDetailEndorsementAddition($request, $autoDetail, $auto);
                $autoDetail->save();
                $this->endorsementVehicleGenerate('ins_do_auto_endor_gen_new_vehicle', $auto, $autoDetail->id);

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

    public function assignValues($request, $model, $auto = null)
    {
        if(!$auto) {
            $auto = Auto::find($request->master_data_id);
        }
            
        $productSpecification = Product::getProductSpecificationByCode($auto->product_code);

        // Cover package component value gets array
        $packageId = $request->cover_pkg_id;
        $optionalCovers = $request->optional_covers ?? [];
        $coversStr = $this->resolveCoversStr($packageId, $optionalCovers);

        $this->assignPassengerTonnage($productSpecification, $request);
        $model->product_code = $auto->product_code;
        $model->master_data_type = $auto->data_type;
        $model->master_data_id = $auto->id;
        $model->model_id = $request->model;
        $model->plate_no = $request->plate_no;
        $model->chassis_no = $request->chassis_no;
        $model->engine_no = $request->engine_no;
        $model->manufacturing_year = $request->manufacturing_year;
        $model->cubic = $request->cubic;
        $model->vehicle_value = $request->vehicle_value;
        $model->passenger = $request->passenger;
        $model->tonnage = $request->tonnage;
        $model->commercial_vehicle_type = $request->commercial_vehicle_type;
        $model->surcharge = $request->surcharge;
        $model->discount = $request->discount;
        $model->ncd = $request->ncd;
        $model->selected_cover_pkg = $coversStr;
        $model->negotiation_rate = $auto->negotiation_rate;
        $model->remark = $request->remark;
        $model->cover_pkg_id = $packageId;
        $model->vehicle_usage = $request->vehicle_usage;
        $model->vehicle_uuid = Str::uuid();
    }

    private function assignPassengerTonnage($productSpecification, $item)
    {

        if ($productSpecification === 'PASSENGER') {
            $item->passenger = optional($item)->passenger_tonnage;
            $item->tonnage = null;
            $item->commercial_vehicle_type = 'PASSENGER';
            return;
        }

        if ($productSpecification === 'TONNAGE') {
            $item->tonnage = optional($item)->passenger_tonnage;
            $item->passenger = null;
            $item->commercial_vehicle_type = 'TONNAGE';
            return;
        }

        $item->passenger = null;
        $item->tonnage = null;
        $item->commercial_vehicle_type = null;
    }

    private function assignAutoDetailEndorsementAddition($request, $model, $auto)
    {
        $this->assignValues($request, $model, $auto);
        $model->endorsement_stage = optional($auto->policy)->document_no;
        $model->endorsement_state = 'ADDITION';
        $model->endorsement_e_date = $request->endorsement_e_date ?? $auto->endorsement_e_date;
    }

    private function resolveCoversStr($packageId, $optionalCovers)
    {

        if (!$packageId)
            return implode(',', $optionalCovers);

        $coverPackage = CoverPackage::select('id')->find($packageId);

        $packageCovers = $coverPackage->coverPackageComponents()->pluck('comp_code');
        $covers = $packageCovers->merge($optionalCovers)->unique()->values();

        return $covers->join(',');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Insurance\AutoDetail  $autoDetail
     * @return \Illuminate\Http\Response
     */
    public function show(AutoDetail $autoDetail)
    {
        return $this->getAutoDetailData($autoDetail);
    }

    public function showEndorsementDeletedVehicle($detail_id)
    {
        $autoDetail = AutoDetail::withoutGlobalScopes([ActiveScope::class])->where('id', $detail_id)->first();
        return $this->getAutoDetailData($autoDetail);
    }

    private function getAutoDetailData($autoDetail)
    {
        $covers = collect(explode(',', $autoDetail->selected_cover_pkg));

        $model = MakeModel::select('make_id', 'model')->find($autoDetail->model_id);
        if (isset($model)) {
            $make = $model->make()->select('id', 'make')->first();
        }

        if (isset($make)) {
            $autoDetail->make = $make->id;
            $autoDetail->make_name = $make->make;
            $autoDetail->model = $autoDetail->model_id;
            $autoDetail->model_name = $model->model;
        }

        $autoDetail->negotiation_rate = $autoDetail->negotiation_rate;
        $autoDetail->surcharge = $autoDetail->surcharge;
        $autoDetail->discount = $autoDetail->discount;
        $autoDetail->ncd = $autoDetail->ncd;
        $autoDetail->passenger_tonnage = $autoDetail->passenger ?? $autoDetail->tonnage;
        $autoDetail->optional_covers = $covers;

        // Cover package component value needs array
        // $autoDetail->cover_pkg_id = $autoDetail->cover_pkg_id ? [$autoDetail->cover_pkg_id] : [];

        //Capitalize Plat No
        $autoDetail->plate_no = strtoupper($autoDetail->plate_no);

        return $autoDetail;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Insurance\AutoDetail  $autoDetail
     * @return \Illuminate\Http\Response
     */
    public function update(AutoDetailRequest $request, AutoDetail $autoDetail)
    {
        try {
            DB::beginTransaction();
            $this->assignValues($request, $autoDetail);
            $autoDetail->update();
            DB::commit();
            return response()->json([
                'autoData' => [
                    'product_code' => $autoDetail->product_code,
                    'request_id' => $autoDetail->master_data_id
                ],
                'id' => $autoDetail->master_data_id,
                'vehicle_id' => $autoDetail->id,
                'success' => true,
                'message' => 'Record is updated.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false,'message' => 'Something went wrong'],500);
        }
    }

    /**
     * Update for general endorsement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Insurance\AutoDetail  $autoDetail
     * @return \Illuminate\Http\Response
     */
    public function updateGeneralEndorsement(Request $request, AutoDetail $autoDetail)
    {
        $request->validate(
            [
                'manufacturing_year' => 'required|numeric|digits:4'
            ],
            [
                'manufacturing_year.required' => 'Year of Manufacture is required.',
                'manufacturing_year.digits' => 'Year of Manufacture is not a valid date.',
            ]
        );

        $autoDetail->plate_no = $request->plate_no;
        $autoDetail->chassis_no = $request->chassis_no;
        $autoDetail->engine_no = $request->engine_no;
        $autoDetail->manufacturing_year = $request->manufacturing_year;

        if ($autoDetail->save()) {
            return [
                'autoData' => [
                    'product_code' => $autoDetail->product_code,
                    'request_id' => $autoDetail->master_data_id
                ],
                'id' => $autoDetail->master_data_id,
                'vehicle_id' => $autoDetail->id,
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    public function updateEndorsementVehicle(AutoDetailRequest $request, Auto $auto, AutoDetail $autoDetail)
    {
        // If is add/deletion endorsement
        if ($auto->endorsement_type === 'VEHICLE') {
            // Manually update updated_at for showing issue date
            $auto->updated_at = now();
            if ($auto->save()) {
                $this->assignAutoDetailEndorsementAddition($request, $autoDetail, $auto);
                $autoDetail->save();
                $this->endorsementVehicleGenerate('ins_do_auto_endor_gen_new_vehicle', $auto, $autoDetail->id);

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

    /**
     * Delete vehicle in endorsement
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteVehicleEndorsement(Request $request, $id)
    {
        $autoDetail = AutoDetail::withoutGlobalScope(ActiveScope::class)->find($id);

        if ($autoDetail->status === 'DEL') {
            return response([
                'success' => false,
                'message' => 'Vehicle has already deleted'
            ], 499);
        }

        $documentNo = BasePolicy::where('id', $request->policy_id)->value('document_no');

        $params = [
            $request->policy_id,
            $autoDetail->product_code,
            'POLICY',
            $autoDetail->master_data_id,
            $autoDetail->id,
            auth()->id(),
        ];
        $success = false;
        // Delete new vehicle
        if ($this->checkIsNewVehicle($autoDetail, $documentNo)) {
            $response = DB::select("select * from ins_do_auto_endor_remove_new_vehicle(?,?,?,?,?,?)", $params);

            $resArr = collect($response)->pluck('code', 'type');
            if ($resArr['STATUS'] === 'SUC') {
                $success = true;
            }
            // Delete existing vehicles
        } else {
            $autoDetail->endorsement_stage = $documentNo;
            $autoDetail->endorsement_state = 'DELETION';
            $autoDetail->endorsement_e_date = $request->endorsement_e_date;

            if ($autoDetail->save()) {
                $response = DB::select("select * from ins_do_auto_endor_remove_vehicle(?,?,?,?,?,?)", $params);

                $resArr = collect($response)->pluck('code', 'type');
                if ($resArr['PREMIUM'] === 'SUC' && $resArr['REINSURANCE_DATA'] === 'SUC') {
                    $success = true;
                }
            }
        }

        if ($success) {
            return [
                'success' => true,
                'message' => 'Vehicle is deleted'
            ];
        }
    }

    private function checkIsNewVehicle(AutoDetail $autoDetail, $documentNo)
    {
        return $autoDetail->endorsement_state === 'ADDITION' && $autoDetail->endorsement_stage === $documentNo;
    }

    private function endorsementVehicleGenerate($func, $auto, $detailId)
    {
        $params = [
            $auto->policy->id,
            $auto->product_code,
            $auto->data_type,
            $auto->id,
            $detailId,
            auth()->id(),
        ];
        $response = DB::select("select * from " . $func . "(?,?,?,?,?,?)", $params);

        info(json_encode($response));
    }
}

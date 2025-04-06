<?php

namespace App\Http\Controllers\ProductConfiguration\VehicleUsage;

use App\Http\Controllers\Controller;
use App\Models\ProductConfiguration\VehicleUsage;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleUsageController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(VehicleUsage::class, 'vehicle_usage');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            VehicleUsage::with(['product' => function($q) {
                $q->select('code', 'name');
            }])
                ->where('status', 'ACT')->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request, null);

        $vehicleUsage = new VehicleUsage();
        $this->assignValues($request, $vehicleUsage);

        if ($vehicleUsage->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request, $vehicleUsage) {
        $request->validate([
            'product_code' => 'required',
            'name' => [
                'required',
                Rule::unique(VehicleUsage::class, 'name')
                    ->where('product_code', $request->product_code)
                    ->where('status', 'ACT')
                    ->ignore($vehicleUsage),
            ],
        ], [
            'product_code.required' => 'Product is required.',
        ]);
    }

    private function assignValues($request, $model) {
        $model->product_code = $request->product_code;
        $model->name = $request->name;
        $model->description = $request->description;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductConfiguration\VehicleUsage  $vehicleUsage
     * @return \Illuminate\Http\Response
     */
    public function show(VehicleUsage $vehicleUsage)
    {
        return $vehicleUsage->load(['product' => function($q) {
            $q->select('code', 'name');
        }]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductConfiguration\VehicleUsage  $vehicleUsage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VehicleUsage $vehicleUsage)
    {
        $this->validateRequest($request, $vehicleUsage);

        $this->assignValues($request, $vehicleUsage);

        if ($vehicleUsage->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductConfiguration\VehicleUsage  $vehicleUsage
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleUsage $vehicleUsage)
    {
        $vehicleUsage->status = "DEL";

        if ($vehicleUsage->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

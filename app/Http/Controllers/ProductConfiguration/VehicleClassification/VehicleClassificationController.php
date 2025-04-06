<?php

namespace App\Http\Controllers\ProductConfiguration\VehicleClassification;

use App\Http\Controllers\Controller;
use App\Models\Make\VehicleClassification;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleClassificationController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(VehicleClassification::class, 'vehicle_classification');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(VehicleClassification::where('status', 'ACT')->orderByDesc('id'));
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

        $vehicleClassification = new VehicleClassification();
        $this->assignValues($request, $vehicleClassification);

        if ($vehicleClassification->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }
    
    private function validateRequest($request, $model) {
        $request->validate([
            'code' => [
                'required',
                Rule::unique(VehicleClassification::class, 'code')->ignore($model)->where('status', 'ACT')
            ],
            'description' => 'required',
            'surcharge' => 'numeric|nullable',
        ]);
    }
    private function assignValues($request, $model) {
        $model->code = $request->post('code');
        $model->description = $request->post('description');
        $model->surcharge = $request->post('surcharge');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Make\VehicleClassification  $vehicleClassification
     * @return \Illuminate\Http\Response
     */
    public function show(VehicleClassification $vehicleClassification)
    {
        return $vehicleClassification;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Make\VehicleClassification  $vehicleClassification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VehicleClassification $vehicleClassification)
    {
        $this->validateRequest($request, $vehicleClassification);

        $this->assignValues($request, $vehicleClassification);

        if ($vehicleClassification->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Make\VehicleClassification  $vehicleClassification
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleClassification $vehicleClassification)
    {
        $vehicleClassification->status = "DEL";
        
        if ($vehicleClassification->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

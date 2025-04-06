<?php

namespace App\Http\Controllers\ProductConfiguration\Model;

use App\Http\Controllers\Controller;
use App\Models\Make\MakeModel;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(MakeModel::class, 'model');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            MakeModel::with('make:id,make')
                ->with('product:code,name')
                ->where('status', 'ACT')
                ->latest('id')
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
        $this->validateRequest($request);

        $model = new MakeModel();
        $this->assignValues($request, $model);

        if ($model->save())
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
    }

    private function validateRequest($request) {
        $request->validate([
            'make_id' => 'required',
            'model' => 'required|max:100',
            'vehicle_type' => 'required',
            'classification' => 'required',
        ]);
    }

    private function assignValues($request, $model) {
        $availableOffline = $request->available_offline ?? false;
        $availableOnline = $request->available_online ?? false;

        $model->make_id = $request->make_id;
        $model->product_code = $request->product_code;
        $model->model = trim($request->model);
        $model->vehicle_type = $request->vehicle_type;
        $model->classification = $request->classification;

        $model->commercial_model = $this->getCommercialModel($model);

        $model->available_offline = $availableOffline ? 'Y' : 'N';
        $model->available_online = $availableOnline ? 'Y' : 'N';
    }

    private function getCommercialModel($model) {
        if ($model->vehicle_type == 'VAN' || $model->vehicle_type == 'BUS') return 'PASSENGER';
        if ($model->vehicle_type == 'TRUCK') return 'TONNAGE';

        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Make\MakeModel  $model
     * @return \Illuminate\Http\Response
     */
    public function show(MakeModel $model)
    {
        $model->load('make');
        $model->load('product');

        $model->available_offline = $model->available_offline == 'Y' ? true : false;
        $model->available_online = $model->available_online == 'Y' ? true : false;

        return $model;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Make\MakeModel  $model
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MakeModel $model)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $model);

        if ($model->save())
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Make\MakeModel  $model
     * @return \Illuminate\Http\Response
     */
    public function destroy(MakeModel $model)
    {
        $model->status = "DEL";

        if ($model->save())
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
    }
}

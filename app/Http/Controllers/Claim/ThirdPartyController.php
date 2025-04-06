<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Models\Claim\ThirdParty;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class ThirdPartyController extends Controller
{
    use DataTable;

    public function __construct() {
        $this->middleware('has-permission:THIRD_PARTY.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:THIRD_PARTY.NEW')->only('store');
        $this->middleware('has-permission:THIRD_PARTY.UPD')->only('update');
        $this->middleware('has-permission:THIRD_PARTY.DEL')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(ThirdParty::latest('id'));
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

        $thirdParty = new ThirdParty();

        $this->assignValues($request, $thirdParty);

        if ($thirdParty->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.',
                'data' => [
                    'id' => $thirdParty->id,
                    'label' => $thirdParty->vehicle_make.' '.$thirdParty->vehicle_model.' ('.$thirdParty->plate_no.')'
                ],
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'license_no' => 'required',
            'plate_no' => 'required',
            'vehicle_make' => 'required',
            'vehicle_model' => 'required',
            'engine_no' => 'required',
        ],
        [
            'license_no.required' => 'The Driving License Number field is required.',
            'vehicle_make.required' => 'The Make field is required.',
            'vehicle_model.required' => 'The Model field is required.',
        ]);
    }

    private function assignValues($request, $model) {
        $model->license_no = $request->license_no;
        $model->vehicle_make = $request->vehicle_make;
        $model->vehicle_model = $request->vehicle_model;
        $model->plate_no = $request->plate_no;
        $model->engine_no = $request->engine_no;
        $model->manufacturing_year = $request->manufacturing_year;
        $model->phone_number = $request->phone_number;
        $model->address = $request->address;
        $model->description = $request->description;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ThirdParty::findOr($id, fn() => abort(404, 'Not found.'));
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
        $thirdParty = ThirdParty::findOr($id, fn() => abort(404, 'Not found.'));

        $this->validateRequest($request);

        $this->assignValues($request, $thirdParty);

        if ($thirdParty->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $thirdParty = ThirdParty::findOr($id, fn() => abort(404, 'Not found.'));

        $thirdParty->status = "DEL";

        if ($thirdParty->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

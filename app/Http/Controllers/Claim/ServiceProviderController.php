<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Models\Claim\ServiceProvider;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    use DataTable;

    public function __construct() {
        $this->middleware('has-permission:SERVICE_PROVIDER.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:SERVICE_PROVIDER.NEW')->only('store');
        $this->middleware('has-permission:SERVICE_PROVIDER.UPD')->only('update');
        $this->middleware('has-permission:SERVICE_PROVIDER.DEL')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(ServiceProvider::latest('id'));
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

        $serviceProvider = new ServiceProvider();

        $this->assignValues($request, $serviceProvider);

        if ($serviceProvider->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone_number' => 'required',
            'type' => 'required',
        ]);
    }

    private function assignValues($request, $model) {
        $model->name = $request->name;
        $model->email = $request->email;
        $model->phone_number = $request->phone_number;
        $model->home_no = $request->home_no;
        $model->street_no = $request->street_no;
        $model->commune = $request->commune;
        $model->district = $request->district;
        $model->city = $request->city;
        $model->latitude = $request->latitude;
        $model->longitude = $request->longitude;
        $model->type = $request->type;
        $model->is_partner = $request->is_partner;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ServiceProvider::findOr($id, fn() => abort(404, 'Not found.'));
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
        $serviceProvider = ServiceProvider::findOr($id, fn() => abort(404, 'Not found.'));

        $this->validateRequest($request);

        $this->assignValues($request, $serviceProvider);

        if ($serviceProvider->save()) {
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
        $serviceProvider = ServiceProvider::findOr($id, fn() => abort(404, 'Not found.'));
        
        $serviceProvider->status = "DEL";

        if ($serviceProvider->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

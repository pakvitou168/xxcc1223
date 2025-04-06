<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Models\Claim\DriverLicense;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class DriverLicenseController extends Controller
{
    use DataTable;

    public function __construct() {
        $this->middleware('has-permission:DRIVER_LICENSE.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:DRIVER_LICENSE.NEW')->only('store');
        $this->middleware('has-permission:DRIVER_LICENSE.UPDATE')->only('update');
        $this->middleware('has-permission:DRIVER_LICENSE.DELETE')->only('destroy');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(DriverLicense::latest('id'));
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

        $driverLicense = new DriverLicense();

        $this->assignValues($request, $driverLicense);

        if ($driverLicense->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.',
                'data' => [
                    'id' => $driverLicense->id,
                    'label' => $driverLicense->name
                ],
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'name' => 'required',
            'gender' => 'required',
            'driver_age' => 'required',
            'license_no' => 'required',
            'license_issue_date' => 'required',
            'license_expire_date' => 'required',
            'email' => 'nullable|email'
        ]);
    }

    private function assignValues($request, $model) {
        $model->name = $request->name;
        $model->gender = $request->gender;
        $model->driver_age = $request->driver_age;
        $model->occupation = $request->occupation;
        $model->license_no = $request->license_no;
        $model->license_issue_date = $request->license_issue_date;
        $model->license_expire_date = $request->license_expire_date;
        $model->postal_code = $request->postal_code;
        $model->home_no = $request->home_no;
        $model->street_no = $request->street_no;
        $model->commune = $request->commune;
        $model->district = $request->district;
        $model->city = $request->city;
        $model->phone_number = $request->phone_number;
        $model->email = $request->email;
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
        return DriverLicense::findOr($id, fn() => abort(404, 'Not found.'));
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
        $driverLicense = DriverLicense::findOr($id, fn() => abort(404, 'Not found.'));

        $this->validateRequest($request);

        $this->assignValues($request, $driverLicense);

        if ($driverLicense->save()) {
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
        $driverLicense = DriverLicense::findOr($id, fn() => abort(404, 'Not found.'));

        $driverLicense->status = "DEL";

        if ($driverLicense->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

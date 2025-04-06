<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Models\Claim\AdjusterCompany;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class AdjusterCompanyController extends Controller
{
    use DataTable;

    public function __construct() {
        $this->middleware('has-permission:ADJUSTER_COMPANY.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:ADJUSTER_COMPANY.NEW')->only('store');
        $this->middleware('has-permission:ADJUSTER_COMPANY.UPDATE')->only('update');
        $this->middleware('has-permission:ADJUSTER_COMPANY.DELETE')->only('destroy');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(AdjusterCompany::latest('id'));
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

        $adjusterCompany = new AdjusterCompany();

        $this->assignValues($request, $adjusterCompany);

        if ($adjusterCompany->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'name_en' => 'required',
            'phone_number' => 'required',
            'email' => 'nullable|email',
        ],['name_en.required'=> 'The Name field is required.']);
    }

    private function assignValues($request, $model) {
        $model->name_en = $request->name_en;
        $model->name_kh = $request->name_kh;
        $model->postal_code=$request->postal_code;
        $model->home_no=$request->home_no;
        $model->street_no=$request->street_no;
        $model->commune=$request->commune;
        $model->district=$request->district;
        $model->city=$request->city;
        $model->phone_number=$request->phone_number;
        $model->email=$request->email;
        $model->address=$request->address;
        $model->description=$request->description;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return AdjusterCompany::findOr($id, fn() => abort(404, 'Not found.'));
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
        $adjusterCompany = AdjusterCompany::findOr($id, fn() => abort(404, 'Not found.'));

        $this->validateRequest($request);

        $this->assignValues($request, $adjusterCompany);

        if ($adjusterCompany->save()) {
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
        $adjusterCompany = AdjusterCompany::findOr($id, fn() => abort(404, 'Not found.'));

        $adjusterCompany->status = "DEL";

        if ($adjusterCompany->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

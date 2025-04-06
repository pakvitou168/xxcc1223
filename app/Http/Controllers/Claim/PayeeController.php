<?php

namespace App\Http\Controllers\Claim;

use App\Http\Controllers\Controller;
use App\Models\Claim\Payee;
use App\Models\RefEnum;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class PayeeController extends Controller
{
    use DataTable;

    public function __construct() {
        $this->middleware('has-permission:PAYEE.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:PAYEE.NEW')->only('store');
        $this->middleware('has-permission:PAYEE.UPD')->only('update');
        $this->middleware('has-permission:PAYEE.DEL')->only('destroy');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(Payee::with([
                'payeeType' => fn($query) =>$query->select('enum_id', 'name')
            ])
            ->latest('id'));
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

        $payee = new Payee();

        $this->assignValues($request, $payee);

        if ($payee->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.',
                'data'=>$payee
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'name_en' => 'required',
            'name_kh' => 'required',
            'type' => 'required',
            'address' => 'required',
        ],[
            'name_en.required'=> 'The Name field is required.',
            'name_kh.required'=> 'The Name (Khmer) field is required.',
        ]);
    }

    private function assignValues($request, $model) {
        $model->name_en = $request->name_en;
        $model->name_kh = $request->name_kh;
        $model->type = $request->type;
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
        return Payee::with([
                'payeeType' => fn($query) =>$query->select('enum_id', 'name')
            ])
            ->findOr($id, fn() => abort(404, 'Not found.'));
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
        $payee = Payee::findOr($id, fn() => abort(404, 'Not found.'));

        $this->validateRequest($request);

        $this->assignValues($request, $payee);

        if ($payee->save()) {
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
        $payee = Payee::findOr($id, fn() => abort(404, 'Not found.'));

        $payee->status = "DEL";

        if ($payee->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }

    public function getLovs() {
        return [
            'payee_types' => RefEnum::listPayeeTypes(), 
        ];
    }
}

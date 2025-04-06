<?php

namespace App\Http\Controllers\ProductConfiguration\ComponentFormulaElement;

use App\Http\Controllers\Controller;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\CompFrmElement\CompFrmElement;

class CompFrmElementController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(CompFrmElement::class, 'comp_form_element');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(CompFrmElement::with('product:code,name','prod_comp:name,code')->where('status', 'ACT')->latest('id'));
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

        $compFormElement = new CompFrmElement();
        
        $this->assignValues($request, $compFormElement);

        if ($compFormElement->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'component_code' => 'required',
            'product_code' => 'required',
            'formula_code' => 'required',
            'calc_option' => 'required',
            'elem_code' => 'required|max:25',
            'elem_type' => 'required|max:2',
            'elem_datatype' => 'required|max:25',
        ]);
    }

    private function assignValues($request, $model) {
        $model->component_code = $request->component_code;
        $model->product_code = $request->product_code;
        $model->formula_code = $request->formula_code;
        $model->calc_option = $request->calc_option;
        $model->elem_code = $request->elem_code;
        $model->elem_type = $request->elem_type;
        $model->elem_datatype = $request->elem_datatype;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompFrmElement\CompFrmElement  $compFormElement
     * @return \Illuminate\Http\Response
     */
    public function show(CompFrmElement $compFormElement)
    {
        $compFormElement->load(['product' => function($query) {
            $query->select('code', 'name');
        }]);
        $compFormElement->load(['prod_comp' => function($query) {
            $query->select('code', 'name');
        }]);

        return $compFormElement;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompFrmElement\CompFrmElement  $compFormElement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompFrmElement $compFormElement)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $compFormElement);

        if ($compFormElement->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompFrmElement\CompFrmElement  $compFormElement
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompFrmElement $compFormElement)
    {
        $compFormElement->status = "DEL";

        if ($compFormElement->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

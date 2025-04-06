<?php

namespace App\Http\Controllers\ProductConfiguration\Formula;

use App\Http\Controllers\Controller;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\Formula\Formula;
use Illuminate\Validation\Rule;

class CompFormulaController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Formula::class, 'formula');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(Formula::with('product:code,name','prodComp:name,code')->where('status','ACT')->latest('id'));
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

        $formula = new Formula();

        $this->assignValues($request, $formula);

        if ($formula->save())
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
    }

    private function validateRequest($request, $formula) {
        $request->validate([
            'product_line_code' => 'required',
            'product_code' => 'required',
            'component_code' => 'required',
            'calc_option' => 'required',
            'formula_code' => [
                'required',
                Rule::unique(Formula::class, 'formula_code')->ignore($formula)->where('product_code', $request->product_code)->where('status', 'ACT')
            ],
            'frm_calc_seq' => 'required',
        ]);
    }

    private function assignValues($request, $formula) {
        $formula->component_code = $request->component_code;
        $formula->product_code = $request->product_code;
        $formula->formula_code = $request->formula_code;
        $formula->calc_option = $request->calc_option;
        $formula->frm_calc_seq = $request->frm_calc_seq;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Formula\Formula  $formula
     * @return \Illuminate\Http\Response
     */
    public function show(Formula $formula)
    {
        $formula->load(['product' => function($query) {
            $query->select('code', 'name', 'product_line_code');
        }]);
        $formula->load(['prodComp' => function($query) {
            $query->select('code', 'name');
        }]);
        $formula->product_line_code = $formula->product->product_line_code;

        return $formula;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Formula\Formula  $formula
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Formula $formula)
    {
        $this->validateRequest($request, $formula);

        $this->assignValues($request, $formula);

        if ($formula->save())
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Formula\Formula  $formula
     * @return \Illuminate\Http\Response
     */
    public function destroy(Formula $formula)
    {
        $formula->status ="DEL";

        if ($formula->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

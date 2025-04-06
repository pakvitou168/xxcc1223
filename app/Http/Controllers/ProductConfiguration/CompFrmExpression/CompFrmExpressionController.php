<?php

namespace App\Http\Controllers\ProductConfiguration\CompFrmExpression;

use App\Http\Controllers\Controller;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\CompFrmExpression\CompFrmExpression;

class CompFrmExpressionController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(CompFrmExpression::class, 'comp_form_expression');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(CompFrmExpression::with('product:code,name','prod_comp:name,code')->where('status', 'ACT')->latest('id'));
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

        $compFormExpression = new CompFrmExpression();

        $this->assignValues($request, $compFormExpression);

        if ($compFormExpression->save()) {
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
            'expr_line' => 'required',
            'expr_type' => 'required',
            'formula_expr' => 'required',
        ]);
    }

    private function assignValues($request, $model) {
        $model->component_code = $request->component_code;
        $model->product_code = $request->product_code;
        $model->formula_code = $request->formula_code;
        $model->calc_option = $request->calc_option;
        $model->expr_line = $request->expr_line;
        $model->expr_type = $request->expr_type;
        $model->formula_expr = $request->formula_expr;
        $model->cond_expr = $request->cond_expr;

        if($request->cond_type == 1){
            $model->cond_type = "Y";
        }else{
            $model->cond_type = "N";
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompFrmExpression\CompFrmExpression  $compFormExpression
     * @return \Illuminate\Http\Response
     */
    public function show(CompFrmExpression $compFormExpression)
    {
        $compFormExpression->load(['product' => function($query) {
            $query->select('code', 'name');
        }]);
        $compFormExpression->load(['prod_comp' => function($query) {
            $query->select('code', 'name');
        }]);

        $compFormExpression->cond_type = $compFormExpression->cond_type == 'Y' ? 1 : 0;

        return $compFormExpression;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompFrmExpression\CompFrmExpression  $compFormExpression
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompFrmExpression $compFormExpression)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $compFormExpression);

        if ($compFormExpression->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompFrmExpression\CompFrmExpression  $compFormExpression
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompFrmExpression $compFormExpression)
    {
        $compFormExpression->status = "DEL";

        if ($compFormExpression->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

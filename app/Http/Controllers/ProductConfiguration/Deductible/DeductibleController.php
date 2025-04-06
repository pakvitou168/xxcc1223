<?php

namespace App\Http\Controllers\ProductConfiguration\Deductible;

use App\Http\Controllers\Controller;
use App\Models\Cover\Cover;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\Deductible\Deductible;
use App\Models\Product\Product;
use App\Models\ProductLine\ProductLine;

class DeductibleController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Deductible::class, 'deductible');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            Deductible::with('product:code,name','cover:code,name')
                ->where('status','ACT')
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

        $deductible = new Deductible();
        $this->assignValues($request, $deductible);
        
        if ($deductible->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'product_line_code' => 'required',
            'product_code' => 'required',
            'comp_code' => 'required',
            'label' => 'required',
            'value' => 'required',
            'cond_value_type' => 'required',
            'cond_value' => 'required',
            'min_value' => 'required',
        ],
        [
            'product_line_code.required' => 'Product Line is required.',
            'product_code.required' => 'Product is required.',
            'comp_code.required' => 'Cover is required.',
        ]);
    }

    private function assignValues($request, $deductible) {
        $deductible->product_code = $request->product_code;
        $deductible->comp_code = $request->comp_code;

        $deductible->label = $request->label;
        $deductible->label_kh = $request->label_kh;
        $deductible->label_zh = $request->label_zh;

        $deductible->description = $request->description;
        $deductible->description_kh = $request->description_kh;
        $deductible->description_zh = $request->description_zh;

        $deductible->value = $request->value;
        $deductible->value_kh = $request->value_kh;
        $deductible->value_zh = $request->value_zh;

        $deductible->cond_value_type = $request->cond_value_type;
        $deductible->cond_value = $request->cond_value ?? 0;
        $deductible->min_value = $request->min_value ?? 0;
        $deductible->max_value = $request->max_value ?? 0;

        $deductible->cond_level = "D";

        if ($request->cond_type == 1) {
            $deductible->cond_type = "Y";
            $deductible->cond_expr = $request->cond_expr;
        } else {
            $deductible->cond_type = "N";
            $deductible->cond_expr = null;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deductible\Deductible  $deductible
     * @return \Illuminate\Http\Response
     */
    public function show(Deductible $deductible)
    {
        $deductible->load('product');
        $deductible->load('cover');

        $deductible->cond_type = $deductible->cond_type == 'Y' ? 1 : 0;
        
        return $deductible;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deductible\Deductible  $deductible
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deductible $deductible)
    {   
        $this->validateRequest($request);

        $this->assignValues($request, $deductible);
        
        if ($deductible->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deductible\Deductible  $deductible
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deductible $deductible)
    {   
        $deductible->status= "DEL";
        
        if ($deductible->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }

    public function getLovs() {
        return [
            'productLines' => ProductLine::listProductLines(),
            'valueTypes' => [
                'AMOUNT' => 'AMOUNT',
                'PERCENTAGE' => 'PERCENTAGE',
            ],
        ];
    }

    public function listProductByProductLineCode($productLineCode = null) {
        if (!$productLineCode) return [];
        return Product::listProductsByProductLine($productLineCode);
    }

    public function listCoversByProductCode($productCode = null) {
        if (!$productCode) return [];
        return Cover::listByProduct($productCode);
    }
}

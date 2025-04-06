<?php

namespace App\Http\Controllers\ProductConfiguration\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(Product::with('productLine:id,code')->latest('id'));
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

        $product = new Product();
        $product->code = $this->generateProductCode();
        $this->assignValues($request, $product);

        if ($product->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'name' => 'required|max:50',
            'product_line_code' => 'required',
            'description' => 'max:500',
            'alt_code' => 'required|max:50',
            'coverage_en'=>'required',
            'coverage_kh'=>'required'
        ],['coverage_en.required' => 'Coverage (English) is required',
        'coverage_kh.required' => 'Coverage (Khmer) is required']);
    }

    private function assignValues($request, $product) {
        $product->name = $request->name;
        $product->name_kh = $request->name_kh;
        $product->name_zh = $request->name_zh;
        $product->description = $request->description;
        $product->description_kh = $request->description_kh;
        $product->description_zh = $request->description_zh;
        $product->product_line_code = $request->product_line_code;
        $product->renewable = $request->renewable;
        $product->alt_code = $request->alt_code;
        $product->limitation_to_use_en = $request->limitation_to_use_en;
        $product->limitation_to_use_kh = $request->limitation_to_use_kh;
        $product->limitation_to_use_zh = $request->limitation_to_use_zh;
        $product->coverage_en = $request->coverage_en;
        $product->coverage_kh = $request->coverage_kh;
        $product->coverage_zh = $request->coverage_zh;

        if ($request->renewable == 1) {
            $product->renewable = "Y";
        } else {
            $product->renewable = "N";
        }

        $product->group_code = $request->group_code;
        $product->default_surcharge = $request->default_surcharge;
        $product->default_discount = $request->default_discount;
        $product->default_ncd = $request->default_ncd;
        $product->prod_specification = $request->prod_specification;
    }

    private function generateProductCode() {
        return collect(DB::select('
                select * from ins_generate_product_code()
            '))
            ->first()
            ->ins_generate_product_code;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->renewable = $product->renewable === 'Y' ? true : false;
        return $product;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $product);

        if ($product->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->status = "DEL";

        if ($product->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

<?php

namespace App\Http\Controllers\ProductConfiguration\Formula;

use App\Http\Controllers\Controller;
use App\Models\Cover\Cover;
use App\Models\Product\Product;
use App\Models\Formula\ProductComponent;

class CompFormulaServiceController extends Controller
{
    public function listProducts() {
        return Product::select('code', 'name')->where('status', 'ACT')->orderBy('code')->get()->map(function($item) {
            return [
                'label' => $item->name,
                'value' => $item->code
            ];
        });
    }

    public function listCoversByProductCode($productCode = null) {
        return Cover::where('type', 'C')->where('product_code', $productCode)->where('status', 'ACT')->orderBy('name')->get()->map(function($item) {
            return [
                'label' => $item->name,
                'value' => $item->code
            ];
        });
    }

    public function listProductsByProductLineCode($productLineCode = null){
        return Product::listProductsByProductLine($productLineCode);
    }

    public function listProductComponents(){
        $prod_comp = ProductComponent::where('status', 'ACT')->get()->pluck('name','code');
        return response()->json($prod_comp);
    }
}

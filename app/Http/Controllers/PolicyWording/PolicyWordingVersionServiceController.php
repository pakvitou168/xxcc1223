<?php

namespace App\Http\Controllers\PolicyWording;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\ProductLine\ProductLine;

class PolicyWordingVersionServiceController extends Controller
{
    public function listProductLines() {
        return ProductLine::where('status', 'ACT')->orderBy('code')->get()->pluck('code', 'code');
    }

    public function listProductsByProductLine($productLineCode) {
        return Product::where('status', 'ACT')
            ->where('product_line_code', $productLineCode)->orderBy('code')->get()->map(function($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->code
                ];
            });
    }

    public function listProductsWithDescByProductLine($productLineCode = null){
        return Product::listProductsByProductLine($productLineCode);
    }
}

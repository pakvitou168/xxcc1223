<?php

namespace App\Http\Controllers\ProductConfiguration\Deductible;

use App\Http\Controllers\Controller;
use App\Models\Cover\Cover;
use App\Models\Product\Product;

class DeductibleServiceController extends Controller
{
    public function listProducts() {
        return Product::select('code', 'name')->where('status', 'ACT')->orderBy('code')->get()->map(function($item) {
            $item->label = $item->name;
            $item->value = $item->code;

            return collect($item)->only(['label', 'value']);
        });
    }

    public function listAutoProductsWithDesc()
    {
        return Product::listAutoProducts();
    }

    public function listCoversByProductCode($productCode) {
        return Cover::where('type', 'C')->where('product_code', $productCode)->where('status', 'ACT')->orderBy('name')->get()->map(function($item) {
            $item->label = $item->name;
            $item->value = $item->code;

            return collect($item)->only(['label', 'value']);
        });
    }
}

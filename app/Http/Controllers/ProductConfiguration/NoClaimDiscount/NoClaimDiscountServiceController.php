<?php

namespace App\Http\Controllers\ProductConfiguration\NoClaimDiscount;

use App\Http\Controllers\Controller;
use App\Models\NoClaimDiscount\NoClaimDiscount;
use App\Models\Product\Product;

class NoClaimDiscountServiceController extends Controller
{
    public function listNoClaimDiscounts($product_code)
    {
        return NoClaimDiscount::where('product_code', $product_code)->where('status', 'ACT')->select('ncd AS label', 'ncd AS value')->get()->map(function($item){
            $item->label = ($item->label*100).'%';
            $item->value = floatval($item->value*100);
            return $item;
        });
    }

    public function listAutoProducts()
    {
        return Product::listAutoProducts();
    }
}

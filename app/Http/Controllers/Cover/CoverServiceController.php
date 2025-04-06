<?php

namespace App\Http\Controllers\Cover;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;

class CoverServiceController extends Controller
{
    public function listAutoProducts()
    {
        return Product::select('name', 'code')
            ->where('product_line_code', 'AUTO')
            ->where('status', 'ACT')
            ->orderBy('code')
            ->get()
            ->pluck('name', 'code');
    }

    public function listAutoProductsWithDesc()
    {
        return Product::listAutoProducts();
    }
}

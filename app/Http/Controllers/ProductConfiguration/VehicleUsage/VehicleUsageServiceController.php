<?php

namespace App\Http\Controllers\ProductConfiguration\VehicleUsage;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;

class VehicleUsageServiceController extends Controller
{
    public function listAutoProducts() {
        return Product::listAutoProducts();
    }
}

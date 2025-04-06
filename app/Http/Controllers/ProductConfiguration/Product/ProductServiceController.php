<?php

namespace App\Http\Controllers\ProductConfiguration\Product;

use App\Http\Controllers\Controller;
use App\Models\RefEnum;

class ProductServiceController extends Controller
{
    public function listAutoProductGroups() {
        return RefEnum::listAutoProductGroups();
    }

    public function listCommercialVehicleTypes() {
        return RefEnum::listCommercialVehicleTypes();
    }
}

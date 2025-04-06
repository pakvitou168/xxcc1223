<?php

namespace App\Http\Controllers\ProductConfiguration\Model;

use App\Http\Controllers\Controller;
use App\Models\Make\Make;
use App\Models\Make\VehicleClassification;
use App\Models\Product\Product;
use App\Models\RefEnum\RefEnum;

class ModelServiceController extends Controller
{
    public function getModelServices() {
        $autoProducts = Product::select('name', 'code')->where('status', 'ACT')->where('product_line_code', 'AUTO')->orderBy('code')->get()->pluck('name', 'code');
        $makes = Make::select('id', 'make')->where('status', 'ACT')->get()->pluck('make', 'id');
        $vehicleTypes = RefEnum::select('name')->where('group_id', 'VEHICLE_CONFIG')->where('type_id', 'VEHICLE_TYPE')->get();
        $vehicleClassifications = VehicleClassification::select('code', 'description')->where('status', 'ACT')->orderBy('id')->get()->pluck('description', 'code');

        return [
            'productOptions' => $autoProducts,
            'makeOptions' => $makes,
            'vehicleTypeOptions' => $vehicleTypes,
            'classificationOptions' => $vehicleClassifications
        ];
    }

    public function listAutoProductsWithDesc()
    {
        return Product::listAutoProducts();
    }
}

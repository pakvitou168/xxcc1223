<?php

namespace App\Http\Controllers\ProductConfiguration\CoverPackage;

use App\Http\Controllers\Controller;
use App\Models\Cover\Cover;

class CoverPackageServiceController extends Controller
{
    public function listProductCovers($productCode = null) {
        return Cover::where('product_code', $productCode)
            ->select('name', 'code', 'mandatory')
            ->where('type', 'C')
            ->where('status', 'ACT')
            ->orderBy('seq')
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->name . ' (' . $item->code . ')',
                    'value' => $item->code,
                    'disabled' => $item->mandatory
                ];
            });
    }

    public function listProductMandatoryCovers($productCode = null) {
        return Cover::where('product_code', $productCode)
            ->select('name', 'code')
            ->where('type', 'C')
            ->where('mandatory', true)
            ->where('status', 'ACT')
            ->orderBy('seq')
            ->get()
            ->pluck('code');
    }
}

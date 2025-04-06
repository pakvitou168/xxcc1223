<?php

namespace App\Http\Controllers\ProductConfiguration\ProductLine;

use App\Http\Controllers\Controller;
use App\Models\ProductLine\ProductLine;

class ProductLineServiceController extends Controller
{
    public function listProductLines()
    {
        return ProductLine::where('status', 'ACT')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->code,
                    'value' => $item->code,
                ];
            })
            ->values()
            ->toArray();
    }
}

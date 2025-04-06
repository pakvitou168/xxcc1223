<?php

namespace App\Models\Cover;

use App\Models\Formula\ProductComponent;
use App\Models\UserPermissionTrait;

class Cover extends ProductComponent
{
    use UserPermissionTrait;

    static $functionCode = 'COVER';
    
    protected $attributes = [
        'type' => 'C',
        'data_type' => 'NUMBER'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
        
    }

    public static function listByProductAndCode($productCode, $codes) {
        return Cover::select('code', 'name', 'name_kh', 'name_zh', 'detail', 'detail_kh', 'detail_zh')
            ->where('product_code', $productCode)
            ->whereIn('code', $codes)
            ->where('type', 'C')
            ->where('status', 'ACT')
            ->orderBy('seq')
            ->get();
    }

    public static function listByProduct($productCode) {
        return Cover::select('code', 'name')
            ->where('type', 'C')
            ->where('product_code', $productCode)
            ->where('status', 'ACT')
            ->orderBy('name')
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->code,
                ];
            });
    }
}

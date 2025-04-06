<?php

namespace App\Models\ProductLine;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class ProductLine extends Model
{
    use UserPermissionTrait;

    const MEDICAL = 'MEDICAL';
    const PA = 'PERSONAL_ACCIDENT';
    const TRAVEL = 'TRAVEL';
    static $functionCode = 'PRODUCT_LINE';
    
    protected $table = 'ins_product_line';

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

    public static function listProductLines() {
        return ProductLine::select('code')
            ->where('status', 'ACT')
            ->orderBy('code')
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->code,
                    'value' => $item->code,
                ];
            });
    }
}

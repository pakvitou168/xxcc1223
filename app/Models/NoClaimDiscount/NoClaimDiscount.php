<?php

namespace App\Models\NoClaimDiscount;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Product;
use App\Models\UserPermissionTrait;

class NoClaimDiscount extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'NO_CLAIM_DISCOUNT';
    protected $table = 'ins_insurance_ncd';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
            $obj->created_by = auth()->id();
        });

        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }

    public function getNcdAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setNcdAttribute($value)
    {
        $this->attributes['ncd'] = round($value / 100, 7);
    }
}

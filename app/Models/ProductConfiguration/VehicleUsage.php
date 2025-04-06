<?php

namespace App\Models\ProductConfiguration;

use App\Models\Product\Product;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class VehicleUsage extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'VEHICLE_USAGE';

    protected $table = 'ins_lov_vehicle_usage';

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

    public static function listByProductCode($productCode) {
        return VehicleUsage::where('product_code', $productCode)
            ->where('status', 'ACT')
            ->select('name AS label', 'name AS value')->get();
    }

    public static function getNameByProductCode($productCode) {
        return VehicleUsage::select('name')->where('product_code', $productCode)
            ->where('status', 'ACT')
            ->get();
    }
}

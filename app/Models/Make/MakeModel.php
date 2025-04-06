<?php

namespace App\Models\Make;

use App\Models\Product\Product;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class MakeModel extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'MODEL';

    protected $table = 'ins_lov_vehicle_model';

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

    public function make()
    {
        return $this->belongsTo(Make::class, 'make_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }

    public function vehicleClassification() {
        return $this->belongsTo(VehicleClassification::class, 'classification', 'code');
    }
}

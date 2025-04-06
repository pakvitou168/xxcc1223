<?php

namespace App\Models\Make;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class VehicleClassification extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'VEHICLE_CLASSIFICATION';
    
    protected $table = 'ins_lov_vehicle_classification';

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

    public function getSurchargeAttribute($value)
    {
        return $value ? round($value * 100, 7) : null;
    }

    public function setSurchargeAttribute($value)
    {
        $this->attributes['surcharge'] = $value ? round($value / 100, 7) : null;
    }
}

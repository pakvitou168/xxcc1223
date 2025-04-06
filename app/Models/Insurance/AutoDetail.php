<?php

namespace App\Models\Insurance;

use App\Models\Make\MakeModel;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class AutoDetail extends Model
{
    protected $table = 'ins_auto_data_detail';

    protected $fillable = [
        'product_code',
        'master_data_type',
        'master_data_id',
        'model_id',
        'plate_no',
        'chassis_no',
        'engine_no',
        'manufacturing_year',
        'cubic',
        'vehicle_value',
        'passenger',
        'tonnage',
        'surcharge',
        'discount',
        'ncd',
        'selected_cover_pkg',
        'negotiation_rate',
        'remark',
        'commercial_vehicle_type',
        'cover_pkg_id',
        'vehicle_uuid'
    ];

    protected static function booted()
    {
        // Getting only active data
        static::addGlobalScope(new ActiveScope);

        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }

    public function auto()
    {
        return $this->belongsTo(Auto::class, 'master_data_id', 'id');
    }

    protected function surcharge(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? round($value * 100, 7) : null,
            set: fn($value) => $value ? round($value / 100, 7) : null
        );
    }
    protected function discount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? round($value * 100, 7) : null,
            set: fn($value) => $value ? round($value / 100, 7) : null
        );
    }

    protected function ncd(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? round($value * 100, 7) : null,
            set: fn($value) => $value ? round($value / 100, 7) : null
        );
    }
    protected function negotiationRate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? round($value * 100, 7) : null,
            set: fn($value) => $value ? round($value / 100, 7) : null
        );
    }
    public function makeModel()
    {
        return $this->belongsTo(MakeModel::class, 'model_id', 'id');
    }
}

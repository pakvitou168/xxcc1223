<?php

namespace App\Models\Renewal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurchargeRule extends Model
{
    use HasFactory;

    protected $table = 'ins_config_surcharge';

    protected static function booted()
    {
        static::addGlobalScope('active', function ($query) {
            $query->where('status', 'ACT');
        });

        static::creating(function ($obj) {
            $obj->status = 'ACT';
            $obj->created_by = auth()->id();
        });

        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}

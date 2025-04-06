<?php

namespace App\Models\Claim;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    protected $table = 'ins_claim_service_provider';

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

    protected function isPartner(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === 'Y' ? 1 : 0),
            set: fn ($value) => ($value === true ? 'Y' : 'N'),
        );
    }
}

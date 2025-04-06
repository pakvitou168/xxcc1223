<?php

namespace App\Models\Claim;

use Illuminate\Database\Eloquent\Model;

class ThirdParty extends Model
{
    protected $table = 'ins_claim_third_party';

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

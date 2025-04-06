<?php

namespace App\Models\Claim\PartialPayment;

use Illuminate\Database\Eloquent\Model;

class ClaimPayment extends Model
{
    protected $table = 'ins_claim_payment';

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

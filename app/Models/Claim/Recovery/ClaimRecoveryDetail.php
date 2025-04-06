<?php

namespace App\Models\Claim\Recovery;

use App\Models\Claim\Process\ClaimTransactionDetail;

class ClaimRecoveryDetail extends ClaimTransactionDetail
{
    protected $attributes = [
        'cond_type' => 'RECOVERY',
    ];
    
    protected static function booted()
    {
        static::addGlobalScope('activeRecovery', function ($query) {
            $query->where('cond_type', 'RECOVERY')->where('status', 'ACT');
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

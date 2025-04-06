<?php

namespace App\Models\Claim\Register;

use Illuminate\Database\Eloquent\Model;

class ClaimDetail extends Model
{
    protected $table = 'ins_claim_detail';

    protected $fillable = [
        'policy_id',
        'data_id',
        'detail_id',
        'claim_no',
        'cause_of_loss_desc',
        'cause_of_loss_code',
        'type',
        'amount',
        'claim_id',
        'recovery_from_third_party',
        'vehicle_uuid'
    ];

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

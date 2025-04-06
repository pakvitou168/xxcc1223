<?php

namespace App\Models\Claim\Process;

use Illuminate\Database\Eloquent\Model;

class ClaimTransactionDetail extends Model
{
    protected $table = 'ins_claim_transaction_detail';

    protected $fillable = [
        'policy_id',
        'data_id',
        'detail_id',
        'payee_id',
        'claim_no',
        'cause_of_loss_code',
        'type',
        'cond_type',
        'payee_address',
        'insured_sharing',
        'partial_amount',
        'remain_amount',
        'deductible',
        'salvage',
        'third_party_recovery',
        'remark',
        'txn_id',
        'deductible_paid',
        'insured_sharing_request',
        'payment_no',
        'payment_type',
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

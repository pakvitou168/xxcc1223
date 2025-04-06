<?php

namespace App\Models\Claim\PartialPayment;

use Illuminate\Database\Eloquent\Model;

class ClaimPaymentDetail extends Model
{
    protected $table = 'ins_claim_payment_detail';

    protected $fillable = [
        'policy_id',
        'data_id',
        'detail_id',
        'claim_no',
        'cause_of_loss_code',
        'amount',
        'payment_id',
        'payee_id',
        'payment_no',
        'payment_type',
        'remark',
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

    public function claimPayment() {
        return $this->belongsTo(ClaimPayment::class, 'payment_id', 'id');
    }
}

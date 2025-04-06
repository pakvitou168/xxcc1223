<?php

namespace App\Models\Claim\Process;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimTransactionDetailTemp extends Model
{
    use HasFactory;
    protected $table='ins_claim_transaction_detail_temp';
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
        'status',
        'created_by',
    ];
}

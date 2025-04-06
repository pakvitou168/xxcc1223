<?php

namespace App\Models\Claim\PartialPayment;

use Illuminate\Database\Eloquent\Model;

class ListClaimPaymentV extends Model
{
    protected $table = 'ins_list_claim_payment_v';

    public function scopeMaster($query)
    {
        return $query->where('cond_type', 'PARTIAL_MASTER');
    }

    public function scopeDetail($query)
    {
        return $query->where('cond_type', '<>', 'PARTIAL_MASTER');
    }
}

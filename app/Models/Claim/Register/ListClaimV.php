<?php

namespace App\Models\Claim\Register;

use Illuminate\Database\Eloquent\Model;

class ListClaimV extends Model
{
    protected $table = 'ins_list_claim_v';

    public function scopeClaim($query)
    {
        return $query->where('type', 'CLAIM');
    }

    public function scopeDetail($query)
    {
        return $query->where('type', '<>', 'CLAIM');
    }
}
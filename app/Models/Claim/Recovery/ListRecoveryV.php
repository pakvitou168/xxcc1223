<?php

namespace App\Models\Claim\Recovery;

use Illuminate\Database\Eloquent\Model;

class ListRecoveryV extends Model
{
    protected $table = 'ins_list_claim_recovery_v';

    public function scopeMaster($query)
    {
        return $query->where('status_type', 'MASTER');
    }

    public function scopeDetail($query)
    {
        return $query->where('status_type', '<>', 'MASTER');
    }
}

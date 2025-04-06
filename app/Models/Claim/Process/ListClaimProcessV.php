<?php

namespace App\Models\Claim\Process;

use Illuminate\Database\Eloquent\Model;

class ListClaimProcessV extends Model
{
    protected $table = 'ins_list_claim_process_v';

    public function scopeMaster($query)
    {
        return $query->where('type', 'MASTER');
    }

    public function scopeDetail($query)
    {
        return $query->where('type', '<>', 'MASTER');
    }
}

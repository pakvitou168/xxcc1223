<?php

namespace App\Models\HS\Claim;

use Illuminate\Database\Eloquent\Model;

class ClaimHistoryV extends Model
{
    protected $table = 'ins_hs_claim_history_v';
    protected $casts = [
        'approved_at' => 'date:d/m/Y'
    ];
}

<?php

namespace App\Models\Claim\Register;

use Illuminate\Database\Eloquent\Model;

class ClaimRegisterPrintV extends Model
{
    protected $table = 'ins_print_claim_register_section1_v';

    public function scopeClaim($query)
    {
        return $query->where('type', 'CLAIM');
    }

    public function scopeDetail($query)
    {
        return $query->where('type', '<>', 'CLAIM');
    }

    public function scopeCover($query)
    {
        return $query->where('type', 'COVER');
    }
}

<?php

namespace App\Models\Claim\Register;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ClaimRegisterReinsurancePrintV extends Model
{
    protected $table = 'ins_print_claim_register_section2_v';

    protected function share(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? round($value * 100, 7) : 0,
        );
    }
}

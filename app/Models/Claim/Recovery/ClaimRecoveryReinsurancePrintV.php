<?php

namespace App\Models\Claim\Recovery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ClaimRecoveryReinsurancePrintV extends Model
{
    protected $table="ins_print_claim_recovery_section2_v";

    protected function percentagedShare(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['share'] ? round($attributes['share'] * 100, 7) : 0,
        );
    }

    protected function share(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? round($value * 100, 7) : 0,
        );
    }
}

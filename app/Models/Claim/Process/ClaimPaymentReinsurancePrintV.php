<?php

namespace App\Models\Claim\Process;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ClaimPaymentReinsurancePrintV extends Model
{
    protected $table = 'ins_print_claim_payment_section2_v';
    
    protected function percentagedShare(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['share'] ? round($attributes['share'] * 100, 7) : 0,
        );
    }
}

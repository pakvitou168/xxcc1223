<?php

namespace App\Models\PA;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BnfExtensionPV extends Pivot
{
    protected function rating(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
}

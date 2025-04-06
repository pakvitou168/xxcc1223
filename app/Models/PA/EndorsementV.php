<?php

namespace App\Models\PA;

use App\Models\Scopes\PA\IsEndorsement;
use App\Models\Scopes\PA\IsPolicy;
use Illuminate\Database\Eloquent\Model;


class EndorsementV extends Model
{
    protected $table = 'ins_pa_policy_v';
    public static function booted()
    {
        static::addGlobalScope(new IsEndorsement);
    }
}

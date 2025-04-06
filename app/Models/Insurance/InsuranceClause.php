<?php

namespace App\Models\Insurance;

use Illuminate\Database\Eloquent\Model;

class InsuranceClause extends Model
{
    const ENDORSEMENT_CLAUSE = 'ENDORSEMENT';
    const GENERAL_EXCLUSION = 'EXCLUSION';
    const GEOGRAPHICAL_LIMIT = 'GEOGRAPHICAL';
    const AUTOMATIC_EXT = 'AUTOMATIC_EXTENSION';

    const DEFAULT_YES = 'Y';
    const DEFAULT_NO = 'N';
    protected $fillable=['status'];
    protected $table = 'ins_insurance_clause';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }
}

<?php

namespace App\Models\HS\Claim;

use Illuminate\Database\Eloquent\Model;

class ClaimSchemaDataV extends Model
{
    protected $table = 'ins_hs_claim_schema_data_v';
    protected $casts = [
        'admission_date' => 'date:Y-m-d',
        'discharge_date' => 'date:Y-m-d'
    ];
}

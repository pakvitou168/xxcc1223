<?php

namespace App\Models\HS;

use Illuminate\Database\Eloquent\Model;

class SchemaData extends Model
{
    protected $table = 'ins_hs_schema_data';

    protected $fillable = [
        'master_data_id',
        'key',
        'age_band',
        'no_female',
        'no_person',
        'rate',
        'plan_1',
        'plan_2',
        'plan_3',
        'plan_4',
        'plan_5',
        'master_data_type',
        'schema_type',
        'schema_detail_code',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}

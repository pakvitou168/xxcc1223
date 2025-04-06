<?php

namespace App\Models\HS;

use Illuminate\Database\Eloquent\Model;

class PlanData extends Model
{
    protected $table = 'ins_hs_plan_data';

    protected $fillable = [
        'master_data_id',
        'schema_type',
        'schema_code',
        'name',
        'description',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}

<?php

namespace App\Models\HS;

use Illuminate\Database\Eloquent\Model;

class PlanDataDetail extends Model
{
    protected $table = 'ins_hs_plan_data_detail';

    protected $fillable = [
        'plan_id',
        'schema_detail_code',
        'name',
        'name_kh',
        'display_kh',
        'sub',
        'amount',
        'internal_text',
        'display',
        'clause_code',
        'rate',
        'discount',
        'plan_1',
        'plan_2',
        'plan_3',
        'plan_4',
        'plan_5',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}

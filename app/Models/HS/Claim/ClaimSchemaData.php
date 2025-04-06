<?php

namespace App\Models\HS\Claim;

use Illuminate\Database\Eloquent\Model;

class ClaimSchemaData extends Model
{
    protected $table = 'ins_hs_claim_schema_data';
    protected $fillable = [
        'status',
        'claim_detail_id',
        'schema_detail_code',
        'admission_date',
        'discharge_date',
        'number_of_day',
        'max_number_of_day',
        'schema_name',
        'limit_amount',
        'actual_incurred_expense',
        'maximum_payable',
        'non_payable_expense',
        'created_by',
        'updated_by'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}

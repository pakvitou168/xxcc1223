<?php

namespace App\Models\Travel\Policy;

use App\Models\RecordStatus;
use Illuminate\Database\Eloquent\Model;

class DataDetail extends Model
{
    protected $table = 'ins_tv_data_detail';
    protected $casts = [
        'date_of_birth' => 'date'
    ];
    protected $fillable = [
        'status',
        'insured_person_uuid',
        'product_code',
        'master_data_type',
        'master_data_id',
        'name',
        'occupation',
        'gender',
        'date_of_birth',
        'passport',
        'is_child',
        'plan_code',
        'endorsement_stage',
        'endorsement_state',
        'inception_date',
        'endorsement_e_date',
        'endos_day_remaining',
        'claim_request_count',
        'remark',
        'previous_id',
        'refund_option',
        'refund_percentage',
        'premium',
        'premium_amt_bf_refund',
        'custom_refund_amount',
        'created_by',
        'updated_by',
    ];

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = RecordStatus::ACTIVE;
            $obj->created_by = auth()->id();
        });
        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}

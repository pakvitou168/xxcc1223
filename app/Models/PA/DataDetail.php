<?php

namespace App\Models\PA;

use App\Models\RecordStatus;
use Illuminate\Database\Eloquent\Model;

class DataDetail extends Model
{
    protected $table = 'ins_pa_data_detail';
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
        'working_class_code',
        'sum_insured',
        'permanent_disablement_amount',
        'medical_expense_amount',
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
        'relationship',
        'endorsement_option'
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

<?php

namespace App\Models\HS\Claim\Register;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimHS extends Model
{
    use HasFactory;
    protected $table = 'ins_hs_claim';
    protected $fillable = [
        'status',
        'seq_no',
        'claim_no',
        'policy_id',
        'data_id',
        'data_detail_id',
        'remark',
        'notification_date',
        'schema_plan',
        'schema_type',
        'schema_detail_code',
        'cause_of_loss',
        'cause_of_loss_disability',
        'date_of_loss',
        'location_of_loss',
        'loss_description',
        'reserve_amount',
        'processing_month',
        'insured_period_from',
        'insured_period_to',
        'clinic_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'approved_by',
        'approved_cmt',
        'approved_status',
        'approved_at',
        'confirmed_final_claim',
        'confirmed_final_claim_at',
        'updated_final_at',
        'updated_final_by'
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

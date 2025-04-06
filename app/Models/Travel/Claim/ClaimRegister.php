<?php

namespace App\Models\Travel\Claim;

use App\Models\Travel\DataDetail;
use App\Models\Travel\DataMaster;
use App\Models\Travel\Policy;
use Illuminate\Database\Eloquent\Model;

class ClaimRegister extends Model
{
    protected $table    = 'ins_tv_claim';
    protected $fillable = [
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'approved_status',
        'approved_by',
        'approved_cmt',
        'approved_at',
        'seq_no',
        'claim_no',
        'policy_id',
        'data_id',
        'data_detail_id',
        'insured_person_uuid',
        'occupation',
        'remark',
        'date_of_loss',
        'notification_date',
        'location_of_loss',
        'loss_description',
        'total_reserve_amount',
        'processing_month',
        'insured_period_from',
        'insured_period_to',
        'deductible',
    ];

    public function detail()
    {
        return $this->hasOne(ClaimRegisterDetail::class, 'claim_id');
    }

    public function insuredPerson()
    {
        return $this->hasOne(DataDetail::class ,'insured_person_uuid', 'insured_person_uuid');
    }

    public function policy()
    {
        return $this->belongsTo(Policy::class, 'policy_id');
    }

    public function dataMaster()
    {
        return $this->belongsTo(DataMaster::class, 'data_id');
    }

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
    public function reinsurances()
    {
        return $this->hasMany(ClaimReinsurance::class, 'claim_no', 'claim_no');
    }
}

<?php

namespace App\Models\HS;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DataDetail extends Model
{
    protected $table = 'ins_hs_data_detail';

    protected $fillable = [
        'product_code',
        'master_data_type',
        'master_data_id',
        'name',
        'occupation',
        'gender',
        'date_of_birth',
        'standard_plan',
        'optional_plan',
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
        'premium_amt_bf_refund',
        'custom_refund_amount',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'premium'
    ];

    protected $casts = [
        'date_of_birth' => 'date:d/M/Y'
    ];

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse(date('Y-m-d', strtotime(str_replace('/', ' ', $this->date_of_birth))))->age
        );
    }

    public function hs()
    {
        return $this->belongsTo(DataMaster::class, 'master_data_id', 'id');
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
}

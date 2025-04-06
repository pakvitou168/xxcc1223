<?php

namespace App\Models\Travel\Claim;

use Illuminate\Database\Eloquent\Model;

class ClaimRegisterDetail extends Model
{
    protected $table = 'ins_tv_claim_detail';
    protected $fillable = [
        'status',
        'claim_id',
        'coverage_code',
        'cause',
        'reserve_amount'
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

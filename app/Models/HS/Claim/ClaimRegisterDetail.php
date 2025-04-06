<?php

namespace App\Models\HS\Claim;

use Illuminate\Database\Eloquent\Model;

class ClaimRegisterDetail extends Model
{
    protected $table = 'ins_hs_claim_detail';
    protected $fillable = [
        'status',
        'version',
        'claim_id',
        'remark',
        'date_of_disability',
        'date_of_completed_doc',
        'due_to',
        'total_actual_incurred_expense',
        'total_maximum_payable',
        'total_non_payable_expense',
        'previous_payment',
        'total_amount_due',
        'created_by',
        'updated_by',
        'prepared_by',
        'prepared_at',
        'approved_by',
        'approved_at'
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

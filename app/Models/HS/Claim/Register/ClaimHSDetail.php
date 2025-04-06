<?php

namespace App\Models\HS\Claim\Register;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimHSDetail extends Model
{
    use HasFactory;
    protected $table = 'ins_hs_claim_detail';
    protected $fillable = [
        'status',
        'version',
        'claim_id',
        'remark',
        'date_of_disability',
        'date_of_completed_doc',
        'duo_to',
        'total_actual_incurred_expense',
        'total_maximum_payable',
        'total_non_payable_expense',
        'previous_payment',
        'total_amount_due',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'approved_by',
        'approved_at'
    ];
}

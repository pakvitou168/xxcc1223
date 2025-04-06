<?php

namespace App\Models\HS\Claim;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimReportV extends Model
{
    use HasFactory;
    protected $table = 'ins_hs_claim_report_v';
    protected $primaryKey = 'claim_id';
}

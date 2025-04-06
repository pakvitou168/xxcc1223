<?php

namespace App\Models\HS\Claim\Payment;

use App\Models\HS\DataDetail;
use App\Models\HS\DataMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClaimTransactionV extends Model
{
    use HasFactory;

    protected $table = 'ins_hs_list_claim_transaction_v';

}

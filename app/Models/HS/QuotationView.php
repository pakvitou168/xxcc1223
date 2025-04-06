<?php

namespace App\Models\HS;

use Illuminate\Database\Eloquent\Model;

class QuotationView extends Model
{
    protected $table = "ins_hs_quotation_v";

    public function quotation() {
        return $this->hasOne(Quotation::class, 'data_id', 'id');
    }
}

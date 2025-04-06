<?php

namespace App\Models\Travel\Policy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class DeductibleData extends Model
{
    protected $table = 'ins_tv_deductible_data';

    protected $appends = ['deductible_formatted'];

    public function getDeductibleFormattedAttribute()
    {
        return $this->ccy . ' ' . $this->value . ' ' . (App::getLocale() === 'en' ? $this->label : $this->label_kh);
    }
}

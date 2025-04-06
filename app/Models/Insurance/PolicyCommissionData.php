<?php

namespace App\Models\Insurance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Insurance\AutoDetail;
class PolicyCommissionData extends Model
{
    protected $table = 'ins_policy_commission_data';

    public function getPremiumTaxFeeRateAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setPremiumTaxFeeRateAttribute($value)
    {
        $this->attributes['premium_tax_fee_rate'] = round($value / 100, 7);
    }

    public function getCommissionRateAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setCommissionRateAttribute($value)
    {
        $this->attributes['commission_rate'] = round($value / 100, 7);
    }

    public function getWitholdingTaxRateAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setWitholdingTaxRateAttribute($value)
    {
        $this->attributes['witholding_tax_rate'] = round($value / 100, 7);
    }

    public function autoDetails() {
        return $this->hasMany(AutoDetail::class, 'id', 'detail_id');
    }
}

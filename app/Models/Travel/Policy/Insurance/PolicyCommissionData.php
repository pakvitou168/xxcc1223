<?php

namespace App\Models\Travel\Policy\Insurance;

use App\Models\Travel\Policy\DataDetail;
use Illuminate\Database\Eloquent\Model;
class PolicyCommissionData extends Model
{
    protected $table = 'ins_tv_policy_commission_data';

    protected $fillable = [
        'policy_id',
        'policy_no',
        'business_category',
        'business_code',
        'gross_written_premium',
        'premium_tax_fee_rate',
        'premium_tax_fee',
        'net_written_premium',
        'commission_rate',
        'commission_amount',
        'witholding_tax_rate',
        'witholding_tax',
        'commission_due_amount',
        'created_by',
        'updated_by',
    ];

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
}
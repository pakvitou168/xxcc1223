<?php

namespace App\Models\PA;

use App\Models\BusinessManagement\BusinessChannel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'ins_pa_policy_commission_data';
    protected $fillable = ['premium_tax_fee_rate', 'commission_rate', 'witholding_tax_rate'];

    protected function commissionRate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    protected function premiumTaxFeeRate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }
    protected function witholdingTaxRate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value * 100,
            set: fn($value) => round($value / 100, 7)
        );
    }

    public function business()
    {
        return $this->belongsTo(BusinessChannel::class, 'business_code', 'business_code');
    }
}

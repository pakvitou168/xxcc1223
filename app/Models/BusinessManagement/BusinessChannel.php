<?php

namespace App\Models\BusinessManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class BusinessChannel extends Model
{
    use UserPermissionTrait;

    const ACTIVE = 'ACT';

    static $functionCode = 'BUSINESS_CHANNEL';

    protected $table = 'ins_business_channel';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }

    public function getCommissionRateAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setCommissionRateAttribute($value)
    {
        $this->attributes['commission_rate'] = round($value / 100, 7);
    }

    public function getPremiumTaxFeeRateAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setPremiumTaxFeeRateAttribute($value)
    {
        $this->attributes['premium_tax_fee_rate'] = round($value / 100, 7);
    }

    public function getWitholdingTaxRateAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setWitholdingTaxRateAttribute($value)
    {
        $this->attributes['witholding_tax_rate'] = round($value / 100, 7);
    }

    public function businessCategory() {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id', 'id');
    }

    public function businessHandler() {
        return $this->belongsTo(BusinessHandler::class, 'handler_code', 'handler_code');
    }

    public function parentChannel() {
        return $this->belongsTo(BusinessChannel::class, 'parent_code', 'business_code');
    }
}

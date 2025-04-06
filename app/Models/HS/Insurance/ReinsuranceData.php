<?php

namespace App\Models\HS\Insurance;

use App\Models\HS\ReinsuranceConfig\ReinsuranceConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;

class ReinsuranceData extends Model
{
    protected $table = 'ins_hs_reinsurance_data';

    protected $fillable = [
        'data_id',
        'product_line_code',
        'product_code',
        'uw_year',
        'treaty_code',
        'lvl',
        'share',
        'ri_commission',
        'tax_fee',
        'endorsement_stage',
    ];

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
            $obj->created_by = auth()->id();
        });

        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }

    public function getShareAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setShareAttribute($value)
    {
        $this->attributes['share'] = round($value / 100, 7);
    }

    public function getRiCommissionAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setRiCommissionAttribute($value)
    {
        $this->attributes['ri_commission'] = round($value / 100, 7);
    }

    public function getTaxFeeAttribute($value)
    {
        return round($value * 100, 7);
    }

    public function setTaxFeeAttribute($value)
    {
        $this->attributes['tax_fee'] = round($value / 100, 7);
    }

    public function getNetPremiumAttribute($value)
    {
        return round($value, 2);
    }

    public static function getTotalShares($policyId)
    {
        $sum = ReinsuranceData::select('share')
            ->where('policy_id', $policyId)
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->sum('share');

        return round(floatval($sum), 3);
    }

    public function getSumByPolicyId($policyId)
    {
        return ReinsuranceData::select(
            DB::raw('sum(share) as share'),
            DB::raw('sum(premium) as premium'),
            DB::raw('sum(ri_commission_amt) as ri_commission_amt'),
            DB::raw('sum(tax_fee_amt) as tax_fee_amt'),
            DB::raw('sum(net_premium) as net_premium'),
        )
            ->where('policy_id', $policyId)
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->first();
    }

    /**
     * Check If reinsurance in policy is completed
     *
     * @param int $policyId
     * @return boolean
     */
    public static function isReinsuranceCompleted($policyId)
    {
        $dataIds = self::getReinsuranceDataIds($policyId);
        foreach ($dataIds as $dataId) {
            $totalShares = null;
            $totalShares = self::getTotalShares($policyId, $dataId);
            if ($totalShares != 1){ return false;}
        }
        return true;
    }

    /**
     * Undocumented function
     *
     * @param int $policyId
     * @return array
     */
    public static function getReinsuranceDataIds($policyId)
    {
        return ReinsuranceData::select('data_id')
            ->where('policy_id', $policyId)
            ->distinct()
            ->pluck('data_id')->toArray();
    }

    public static function getReinsuranceType($productCode, $partnerCode)
    {
        $reinsuranceType = ReinsuranceConfig::where('product_code', $productCode)
            ->where('partner_code', $partnerCode)->value('reinsurance_type');
        if (!$reinsuranceType) {
            $groupCode = ReinsurancePartner::where('code', $partnerCode)->value('group_code');
            return ReinsurancePartnerGroup::where('code', $groupCode)->value('name');
        }
        return $reinsuranceType;
    }
}

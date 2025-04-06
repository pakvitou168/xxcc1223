<?php

namespace App\Models\HS\Reinsurance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Insurance\AutoDetail;
use App\Scopes\ActiveScope;
use App\Models\ReinsuranceConfig\ReinsuranceConfig;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use App\Models\ReinsuranceConfig\ReinsurancePartnerGroup;

class ReinsuranceData extends Model
{
    protected $table = 'ins_hs_reinsurance_data';

    protected $fillable = [
        'data_id',
        'policy_id',
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

    public static function getTotalShares($policyId, $detailId) {
        $sum = ReinsuranceData::select('share')
            ->where('policy_id', $policyId)
            ->where('detail_id', $detailId)
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->sum('share');

        return round(floatval($sum), 3);
    }

    public static function getTotalSharesAddedVehicle($policyId, $detailId) {
        $sum = ReinsuranceData::select('share')
            ->where('policy_id', $policyId)
            ->where('detail_id', $detailId)
            ->where('endorsement_state', 'ADDITION')
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->sum('share');

        return round(floatval($sum), 3);
    }

    public static function getTotalSharesDeletedVehicle($policyId, $detailId) {
        $sum = ReinsuranceData::select('share')
            ->where('policy_id', $policyId)
            ->where('detail_id', $detailId)
            ->where('endorsement_state', 'DELETION')
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->sum('share');

        return round(floatval($sum), 3);
    }

    public static function getTotalSharesCancelledVehicle($policyId, $detailId, $endorsementNo) {
        $sum = ReinsuranceData::select('share')
            ->where('policy_id', $policyId)
            ->where('detail_id', $detailId)
            ->where('endorsement_state', 'CANCEL')
            ->where('lvl', 1)
            ->where('status', 'ACT')
            ->where('endorsement_stage', $endorsementNo)
            ->sum('share');

        return round(floatval($sum), 3);
    }

    public function getSumByPolicyId($policyId, $detailId) {
        return ReinsuranceData::select(
            DB::raw('sum(share) as share'),
            DB::raw('sum(sum_insured) as sum_insured'),
            DB::raw('sum(premium) as premium'),
            DB::raw('sum(ri_commission_amt) as ri_commission_amt'),
            DB::raw('sum(tax_fee_amt) as tax_fee_amt'),
            DB::raw('sum(net_premium) as net_premium'),
        )
        ->where('policy_id', $policyId)
        ->where('detail_id', $detailId)
        ->where('lvl', 1)
        ->where('status', 'ACT')
        ->first();
    }

    public function getSumAddedVehicleByPolicyId($policyId, $detailId){
        return ReinsuranceData::select(
            DB::raw('sum(share) as share'),
            DB::raw('sum(sum_insured) as sum_insured'),
            DB::raw('sum(premium) as premium'),
            DB::raw('sum(ri_commission_amt) as ri_commission_amt'),
            DB::raw('sum(tax_fee_amt) as tax_fee_amt'),
            DB::raw('sum(net_premium) as net_premium'),
        )
        ->where('policy_id', $policyId)
        ->where('detail_id', $detailId)
        ->where('endorsement_state', 'ADDITION')
        ->where('lvl', 1)
        ->where('status', 'ACT')
        ->first();
    }

    public function getSumDeletedVehicleByPolicyId($policyId, $detailId) {
        return ReinsuranceData::select(
            DB::raw('sum(share) as share'),
            DB::raw('sum(sum_insured) as sum_insured'),
            DB::raw('sum(premium) as premium'),
            DB::raw('sum(ri_commission_amt) as ri_commission_amt'),
            DB::raw('sum(tax_fee_amt) as tax_fee_amt'),
            DB::raw('sum(net_premium) as net_premium'),
        )
        ->where('policy_id', $policyId)
        ->where('detail_id', $detailId)
        ->where('endorsement_state', 'DELETION')
        ->where('lvl', 1)
        ->where('status', 'ACT')
        ->first();
    }

    public function getSumCancelledVehicleByPolicyId($policyId, $detailId, $endorsementNo) {
        return ReinsuranceData::select(
            DB::raw('sum(share) as share'),
            DB::raw('sum(sum_insured) as sum_insured'),
            DB::raw('sum(premium) as premium'),
            DB::raw('sum(ri_commission_amt) as ri_commission_amt'),
            DB::raw('sum(tax_fee_amt) as tax_fee_amt'),
            DB::raw('sum(net_premium) as net_premium'),
        )
        ->where('policy_id', $policyId)
        ->where('detail_id', $detailId)
        ->where('endorsement_state', 'CANCEL')
        ->where('endorsement_stage', $endorsementNo)
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
    public static function isReinsuranceCompleted($policyId, $endorsementNo = null) {
        $detailIds = self::getReinsuranceDetailIds($policyId, $endorsementNo);
        foreach($detailIds as $detailId) {
            $totalShares = null;
            if($endorsementNo){
                $isEndorsementAddtion = ReinsuranceData::where('policy_id', $policyId)
                                                    ->where('detail_id', $detailId)
                                                    ->where('endorsement_state','ADDITION')
                                                    ->first();
                $isEndorsementDeletion = ReinsuranceData::where('policy_id', $policyId)
                                                    ->where('detail_id', $detailId)
                                                    ->where('endorsement_state','DELETION')
                                                    ->first();
                $isEndorsementCancel = ReinsuranceData::where('policy_id', $policyId)
                                                    ->where('detail_id', $detailId)
                                                    ->where('endorsement_state','CANCEL')
                                                    ->first();
                if($isEndorsementAddtion)
                    $totalShares = self::getTotalSharesAddedVehicle($policyId, $detailId);
                if($isEndorsementDeletion)
                    $totalShares = self::getTotalSharesDeletedVehicle($policyId, $detailId);
                if($isEndorsementCancel)
                    $totalShares = self::getTotalSharesCancelledVehicle($policyId, $detailId, $endorsementNo);
            }
            else
                $totalShares = self::getTotalShares($policyId, $detailId);
            if ($totalShares != 1) return false;
        }
        return true;
    }

    /**
     * Undocumented function
     *
     * @param int $policyId
     * @return array
     */
    public static function getReinsuranceDetailIds($policyId, $endorsementNo = null) {
        if($endorsementNo)
            return ReinsuranceData::select('detail_id')
                ->where('policy_id', $policyId)
                ->where('endorsement_stage', $endorsementNo)
                ->distinct()
                ->pluck('detail_id');
        else
            return ReinsuranceData::select('detail_id')
                ->where('policy_id', $policyId)
                ->distinct()
                ->pluck('detail_id');
    }

    public static function getVehicleValue($detailId){
        return AutoDetail::withoutGlobalScopes([ActiveScope::class])->where('id', $detailId)->value('vehicle_value');
    }

    public static function getVehicleTotalPremium($detailId){
        return AutoDetail::withoutGlobalScopes([ActiveScope::class])->where('id', $detailId)->value('premium');
    }

    public static function getReinsuranceType($productCode, $partnerCode){
        $reinsuranceType = ReinsuranceConfig::where('product_code', $productCode)->where('partner_code', $partnerCode)->value('reinsurance_type');
        if(!$reinsuranceType) {
            $groupCode = ReinsurancePartner::where('code', $partnerCode)->value('group_code');
            return ReinsurancePartnerGroup::where('code', $groupCode)->value('name');
        }
        return $reinsuranceType;
    }
}

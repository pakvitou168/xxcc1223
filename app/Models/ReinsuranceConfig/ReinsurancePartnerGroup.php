<?php

namespace App\Models\ReinsuranceConfig;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReinsuranceConfig\ReinsurancePartner;

class ReinsurancePartnerGroup extends Model
{
    use UserPermissionTrait;
    static $functionCode = 'REINSURANCE_PARTNER_GROUP';
    protected $table = 'ins_reinsurance_partner_group';

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
    public function partners(){
        return $this->hasMany(ReinsurancePartner::class,'group_code','code');
    }
    public static function getGroupCodeList(){
        return
            ReinsurancePartnerGroup::select('code')
                    ->where('status', 'ACT')
                    ->pluck('code');
    }
    public static function getAllPartnerGroupNames(){
        $assignedPartnerGroupCodes = ReinsurancePartner::distinct('group_code')
                                        ->where('status','ACT')
                                        ->whereNotNull('group_code')
                                        ->pluck('group_code')->toArray();
        return
            ReinsurancePartnerGroup::where('status', 'ACT')
                        ->whereIn('code', $assignedPartnerGroupCodes)
                        ->select('name AS label', 'code AS value')->get();
    }
}

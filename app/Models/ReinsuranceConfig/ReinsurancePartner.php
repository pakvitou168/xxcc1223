<?php

namespace App\Models\ReinsuranceConfig;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class ReinsurancePartner extends Model
{
    use UserPermissionTrait;
    static $functionCode = 'REINSURANCE_PARTNER';
    protected $table = 'ins_reinsurance_partner';

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

    public static function getPartnerNameByCode($code)
    {
        return ReinsurancePartner::where('status', 'ACT')->where('code', $code)
            ->value('name');
    }

    public static function getAllPartnerNames()
    {
        return ReinsurancePartner::where('status', 'ACT')
            ->pluck('name', 'code');
    }

    public static function getPartnerNamesForForm()
    {
        return ReinsurancePartner::where('status', 'ACT')->whereNotNull('group_code')->get()->map(function ($item) {
            return [
                'name' => $item->name,
                'code' => $item->code,
                'group_code' => $item->group_code
            ];
        });
    }
}

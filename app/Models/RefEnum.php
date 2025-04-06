<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefEnum extends Model
{
    protected $table = 'ins_ref_enum';

    public static function listAutoEndorsementTypes()
    {
        return RefEnum::where('type_id', 'AUTO_ENDORSEMENT_TYPE')
            ->where('group_id', 'POLICY_CONFIG')
            ->select('name AS label', 'enum_id AS value')->get();
    }

    public static function listEndorsementTypes(string $type)
    {
        return RefEnum::where('type_id', $type)
            ->where('group_id', 'POLICY_CONFIG')
            ->pluck('name', 'enum_id');
    }

    public static function getCurrency()
    {
        return RefEnum::select('name')
            ->where('group_id', 'SYS_CONFIG')
            ->where('type_id', 'CCY')
            ->get()->map(function ($item) {
                return $item->name;
            });
    }

    public static function getRateType()
    {
        return RefEnum::select('name')
            ->where('group_id', 'SYS_CONFIG')
            ->where('type_id', 'EXCH_RATE_TYPE')
            ->get()->map(function ($item) {
                return $item->name;
            });
    }

    public static function listRefundTypes()
    {
        return RefEnum::select('name', 'enum_id')
            ->where('group_id', 'POLICY_CONFIG')
            ->where('type_id', 'REFUND_TYPE')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->enum_id
                ];
            });
    }

    public static function listAutoProductGroups()
    {
        return RefEnum::select('name', 'enum_id')
            ->where('group_id', 'PL_AUTO_CONFIG')
            ->where('type_id', 'AUTO_PRODUCT_GROUP')
            ->where('status', 'ACT')
            ->orderBy('seq_no')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->enum_id
                ];
            });
    }

    public static function listCommercialVehicleTypes()
    {
        return RefEnum::select('name', 'enum_id')
            ->where('type_id', 'COMMERCIAL_VEHICLE_TYPE')
            ->where('group_id', 'PL_AUTO_CONFIG')
            ->where('status', 'ACT')
            ->orderBy('seq_no')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->enum_id
                ];
            });
    }

    public static function listIdmStatuses()
    {
        return RefEnum::where('type_id', 'IDM_STATUS')
            ->where('group_id', 'IDM')
            ->where('status', 'ACT')
            ->pluck('name', 'enum_id');
    }

    public static function listPayeeTypes()
    {
        return RefEnum::where('type_id', 'PAYEE_TYPE')
            ->where('group_id', 'CLAIM_TYPE')
            ->where('status', 'ACT')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->enum_id
                ];
            });
    }

    public static function listPaymentTypes()
    {
        return RefEnum::where('type_id', 'PAYMENT_TYPE')
            ->where('group_id', 'CLAIM_TYPE')
            ->where('status', 'ACT')
            ->select('name AS label', 'enum_id AS value')->get();
    }
    public static function listClaimEnquiryBenefit()
    {
        return RefEnum::where('group_id', 'CLAIM_ENQUIRY_BENEFIT')
            ->where('status', 'ACT')
            ->select('name AS label', 'enum_id AS value')->get();
    }
    public static function smPermissions()
    {
        return self::whereGroupId('SM')->whereTypeId('SM_PERMISSION')->whereStatus(RecordStatus::ACTIVE)->select('enum_id AS value', 'name AS label')->get();
    }
    public static function smStatuses()
    {
        return self::whereGroupId('SM')->whereTypeId('SM_STATUS')->whereStatus(RecordStatus::ACTIVE)->select('enum_id AS value', 'name AS label')->get();
    }
}

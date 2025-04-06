<?php

namespace App\Models\ReinsuranceConfig;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class ReinsuranceType extends Model
{
    use UserPermissionTrait;
    static $functionCode = 'REINSURANCE_TYPE';
    protected $table = 'ins_reinsurance_type';

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
}

<?php

namespace App\Models\CustomerManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'COUNTRY';

    protected $table = 'ins_country';

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

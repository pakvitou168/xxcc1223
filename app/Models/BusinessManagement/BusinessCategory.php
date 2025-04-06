<?php

namespace App\Models\BusinessManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class BusinessCategory extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'BUSINESS_CATEGORY';

    protected $table = 'ins_business_category';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }
}

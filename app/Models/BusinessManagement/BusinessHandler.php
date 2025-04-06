<?php

namespace App\Models\BusinessManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class BusinessHandler extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'BUSINESS_HANDLER';

    protected $table = 'ins_business_handler';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }
}

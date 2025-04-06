<?php

namespace App\Models\BankInformation;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserPermissionTrait;

class BankInformation extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'BANK_INFORMATION';
    protected $table = 'ins_mode_of_payment';

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

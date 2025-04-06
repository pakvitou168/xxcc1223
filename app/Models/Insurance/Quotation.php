<?php

namespace App\Models\Insurance;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'QUOTATION';

    protected $table = 'ins_quotation';

    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
    }

    public function policy() {
        return $this->hasOne(Policy::class, 'quotation_id', 'id');
    }
}

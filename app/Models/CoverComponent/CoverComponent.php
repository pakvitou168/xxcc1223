<?php

namespace App\Models\CoverComponent;

use App\Models\Formula\ProductComponent;
use App\Models\UserPermissionTrait;

class CoverComponent extends ProductComponent
{
    use UserPermissionTrait;

    static $functionCode = 'COVER_COMPONENT';
    
    protected $attributes = [
        'type' => 'R',
        'data_type' => 'NUMBER'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($obj) {
            $obj->status = 'ACT';
        });
        
    }
}

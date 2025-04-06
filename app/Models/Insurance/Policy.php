<?php

namespace App\Models\Insurance;

use App\Models\UserPermissionTrait;
use App\Scopes\PolicyScope;

class Policy extends BasePolicy
{
    use UserPermissionTrait;

    static $functionCode = 'POLICY';

    protected static function booted()
    {
        // Scope for policy with no version
        static::addGlobalScope(new PolicyScope);

        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}

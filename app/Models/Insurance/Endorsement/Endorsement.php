<?php

namespace App\Models\Insurance\Endorsement;

use App\Models\Insurance\BasePolicy;
use App\Models\UserPermissionTrait;
use App\Scopes\EndorsementScope;

class Endorsement extends BasePolicy
{
    use UserPermissionTrait;

    static $functionCode = 'ENDORSEMENT';

    protected static function booted()
    {
        // Scope for policy with no version (endorsement)
        static::addGlobalScope(new EndorsementScope);
        
        static::updating(function ($obj) {
            $obj->updated_by = auth()->id();
        });
    }
}

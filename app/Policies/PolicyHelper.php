<?php

namespace App\Policies;
use App\Models\SecurityManagement\User;
use Illuminate\Support\Facades\Cache;

class PolicyHelper
{
    public static function isAuthorized(User $user, $code, $permission)
    {
        $allFunctions = auth()->user()->allFunctions ?: collect();
        $funcs = $allFunctions->where('code', $code.'.'.$permission);
        if ($funcs->isNotEmpty()) {
            // foreach ($funcs as $func) {
            //     if (in_array($permission, $func->permission))
                    return true;
            // }
        }
        return false;
    }
}

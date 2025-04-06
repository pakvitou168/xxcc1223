<?php

namespace App\Policies\Make;

use App\Models\User;
use App\Policies\PolicyTrait;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessRulePolicy
{
    use PolicyTrait;

    static $functionCode = 'ACCESS_RULE';
}

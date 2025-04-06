<?php

namespace App\Policies\UserManagement\User;

use App\Policies\PolicyTrait;

class UserPolicy
{
    use PolicyTrait;

    static $functionCode = 'USER';
}

<?php

namespace App\Policies\UserManagement;

use App\Policies\PolicyTrait;

class RolePolicy
{
    use PolicyTrait;

    static $functionCode = 'ROLE';
}

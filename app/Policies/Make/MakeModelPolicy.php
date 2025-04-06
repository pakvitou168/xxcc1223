<?php

namespace App\Policies\Make;

use App\Models\User;
use App\Policies\PolicyTrait;
use Illuminate\Auth\Access\HandlesAuthorization;

class MakeModelPolicy
{
    use PolicyTrait;

    static $functionCode = 'MODEL';
}

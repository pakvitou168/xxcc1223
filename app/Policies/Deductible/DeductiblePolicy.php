<?php

namespace App\Policies\Deductible;

use App\Models\User;
use App\Policies\PolicyTrait;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeductiblePolicy
{
    use PolicyTrait;

    static $functionCode = 'DEDUCTIBLE';
}

<?php

namespace App\Policies\CustomerManagement;

use App\Policies\PolicyTrait;

class CustomerPolicy
{
    use PolicyTrait;

    static $functionCode = 'CUSTOMER';
}

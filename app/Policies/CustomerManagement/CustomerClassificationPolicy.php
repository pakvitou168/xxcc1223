<?php

namespace App\Policies\CustomerManagement;

use App\Policies\PolicyTrait;

class CustomerClassificationPolicy
{
    use PolicyTrait;

    static $functionCode = 'CUSTOMER_CLASSIFICATION';
}

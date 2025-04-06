<?php

namespace App\Policies\NoClaimDiscount;

use App\Policies\PolicyTrait;

class NoClaimDiscountPolicy
{
    use PolicyTrait;

    static $functionCode = 'NO_CLAIM_DISCOUNT';
}

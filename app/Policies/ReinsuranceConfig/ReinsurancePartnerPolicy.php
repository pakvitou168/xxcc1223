<?php

namespace App\Policies\ReinsuranceConfig;

use App\Policies\PolicyTrait;

class ReinsurancePartnerPolicy
{
    use PolicyTrait;

    static $functionCode = 'REINSURANCE_PARTNER';
}

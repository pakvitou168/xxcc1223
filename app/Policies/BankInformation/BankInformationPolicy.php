<?php

namespace App\Policies\BankInformation;

use App\Policies\PolicyTrait;

class BankInformationPolicy
{
    use PolicyTrait;

    static $functionCode = 'BANK_INFORMATION';
}

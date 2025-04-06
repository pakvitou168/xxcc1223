<?php

namespace App\Policies\Insurance;

use App\Policies\PolicyTrait;

class QuotationPolicy
{
    use PolicyTrait;

    static $functionCode = 'QUOTATION';
}

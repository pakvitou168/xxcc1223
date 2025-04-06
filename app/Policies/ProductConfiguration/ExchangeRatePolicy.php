<?php

namespace App\Policies\ProductConfiguration;

use App\Models\SecurityManagement\User;
use App\Policies\PolicyTrait;
use App\Policies\PolicyHelper;

class ExchangeRatePolicy
{
    use PolicyTrait;

    static $functionCode = 'EXCHANGE_RATE';

    public function update(User $user, $model)
    {
        $statusPending = 'PND';

        // If the exchange rate data has already approved
        if ($model->status !== $statusPending) return false;

        return PolicyHelper::isAuthorized($user, self::$functionCode, 'UPD');
    }
}

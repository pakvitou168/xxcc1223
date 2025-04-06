<?php

namespace App\Policies\Insurance;

use App\Models\Insurance\Policy;
use App\Models\SecurityManagement\User;
use App\Policies\PolicyHelper;
use App\Policies\PolicyTrait;

class PolicyPolicy
{
    use PolicyTrait;

    static $functionCode = 'POLICY';

    public function update(User $user, $model)
    {
        $statusPending = 'PND';
        $statusRejected = 'REJ';

        // If the quote has already approved or rejected
        if ($model->status !== $statusPending && $model->status !== $statusRejected) return false;

        return PolicyHelper::isAuthorized($user, self::$functionCode, 'UPD');
    }

    public function delete(User $user, $model)
    {
        if ($model->status === 'APV') return false; 

        return PolicyHelper::isAuthorized($user, self::$functionCode, 'DEL');
    }
}

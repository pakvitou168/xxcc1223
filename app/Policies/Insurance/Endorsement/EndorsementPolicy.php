<?php

namespace App\Policies\Insurance\Endorsement;

use App\Models\SecurityManagement\User;
use App\Policies\PolicyHelper;
use App\Policies\PolicyTrait;

class EndorsementPolicy
{
    use PolicyTrait;

    static $functionCode = 'ENDORSEMENT';

    public function update(User $user, $model)
    {
        $statusPending = 'PND';

        // If the quote has already approved or rejected
        if ($model->status !== $statusPending) return false;
        
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'UPD');
    }

    public function delete(User $user, $model)
    {
        if ($model->status === 'APV') return false;
        
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'DEL');
    }
}

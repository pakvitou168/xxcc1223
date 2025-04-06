<?php

namespace App\Policies\Insurance;

use App\Models\Insurance\Quotation;
use App\Models\SecurityManagement\User;
use App\Policies\PolicyHelper;
use App\Policies\PolicyTrait;

class AutoPolicy
{
    use PolicyTrait;

    static $functionCode = 'AUTO';

    /**
     * Determine whether the user can update the model.
     *
     * @param  App\User  $user
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function update(User $user, $model)
    {
        $statusPending = 'PND';

        // If the quote has already approved or rejected
        if ($model->quotation && $model->quotation->approved_status !== $statusPending) return false;
        
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'UPD');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  App\User  $user
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function delete(User $user, $model)
    {
        $isApproved = Quotation::where('data_id', $model->id)
            ->where('approved_status', 'APV')
            ->exists();

        if ($isApproved) return false;

        return PolicyHelper::isAuthorized($user, self::$functionCode, 'DEL');
    }
}

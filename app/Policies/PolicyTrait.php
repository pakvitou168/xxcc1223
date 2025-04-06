<?php

namespace App\Policies;

use App\Models\SecurityManagement\User;
use Illuminate\Auth\Access\HandlesAuthorization;

trait PolicyTrait
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  App\SecurityManagement\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'VIEW');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  App\SecurityManagement\User  $user
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function view(User $user)
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  App\SecurityManagement\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'NEW');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  App\SecurityManagement\User  $user
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function update(User $user)
    {
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'UPDATE');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  App\SecurityManagement\User  $user
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function delete(User $user)
    {
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'DELETE');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  App\SecurityManagement\User  $user
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function restore(User $user, $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  App\SecurityManagement\User  $user
     * @param  Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function forceDelete(User $user, $model)
    {
        //
    }

    public function approve(User $user) {
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'APPROVE');
    }

    public function revise(User $user) {
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'REVISE');
    }

    public function accept(User $user) {
        return PolicyHelper::isAuthorized($user, self::$functionCode, 'ACCEPT');
    }
}
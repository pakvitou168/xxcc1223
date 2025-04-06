<?php

namespace App\Models\UserManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Functions extends Model
{
    use UserPermissionTrait;

    static $functionCode = 'FUNCTION';

    protected $table = "idm_function";

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($func) {
            $func->create_by = auth()->id();
        });
        static::updated(function ($func) {
            $func->update_by = auth()->id();
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'idm_role_func', 'function_id', 'role_id');
    }
}

<?php

namespace App\Models\UserManagement\User;

use App\Models\UserManagement\Functions\Functions;
use App\Models\UserManagement\Role;
use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use UserPermissionTrait;
    
    static $functionCode = 'USER';

    protected $table = 'idm_user';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'idm_user_role', 'user_id', 'role_id')->withPivot('status');
    }
    public function permissions()
    {
        return $this->belongsToMany(Functions::class, 'idm_user_func', 'user_id', 'function_id')->withPivot('permission', 'status');
    }
    public function branchs()
    {
        return $this->belongsToMany(Branch::class, 'idm_user_branch', 'user_id', 'branch_code');
    }
}

<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class RoleFunction extends Model
{
    protected $table = 'idm_role_func';

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function fnc()
    {
        return $this->belongsTo(Functions::class, 'function_id');
    }
    public function application()
    {
        return $this->belongsTo(Application::class, 'app_code', 'code');
    }
}

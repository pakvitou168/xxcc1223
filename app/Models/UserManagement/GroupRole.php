<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupRole extends Model
{
    protected $table = 'idm_group_role';
    public function application()
    {
        return $this->belongsTo(Application::class, 'app_code', 'code');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}

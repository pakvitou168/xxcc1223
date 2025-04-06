<?php

namespace App\Models\UserManagement\User;

use App\Models\UserManagement\Group;
use App\Models\UserManagement\User\User;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = 'idm_user_group';
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}

<?php

namespace App\Models\SecurityManagement;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $table = 'sm_user_permission';
    protected $fillable = ['permission_id', 'user_id'];
}

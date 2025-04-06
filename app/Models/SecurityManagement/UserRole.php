<?php

namespace App\Models\SecurityManagement;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'sm_user_role';
    protected $fillable = [
        'user_id',
        'role_id'
    ];
}

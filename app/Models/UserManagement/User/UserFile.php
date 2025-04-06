<?php

namespace App\Models\UserManagement\User;

use Illuminate\Database\Eloquent\Model;

class UserFile extends Model
{
    protected $table = 'idm_user_file_storage';

    protected $fillable = ['user_id'];
}
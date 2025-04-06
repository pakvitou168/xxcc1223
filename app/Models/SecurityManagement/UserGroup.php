<?php

namespace App\Models\SecurityManagement;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = 'sm_user_group';
    protected $fillable = [
        'user_id',
        'group_id'
    ];
}

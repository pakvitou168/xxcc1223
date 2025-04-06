<?php

namespace App\Models\SecurityManagement;

use Illuminate\Database\Eloquent\Model;

class GroupRole extends Model
{
    protected $table = 'sm_group_role';
    protected $fillable = ['role_id','group_id'];
}

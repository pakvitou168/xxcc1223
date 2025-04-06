<?php

namespace App\Models\SecurityManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'sm_permission_role';
    protected $fillable = ['role_id', 'permission_id'];
}

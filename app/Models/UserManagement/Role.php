<?php

namespace App\Models\UserManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use UserPermissionTrait;
    
    static $functionCode = 'ROLE';
    
    protected $table = 'idm_role';
}

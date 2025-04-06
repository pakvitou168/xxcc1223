<?php

namespace App\Models\UserManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{

    use UserPermissionTrait;
    
    static $functionCode = 'APPLICATION';

    protected $table = "idm_application";

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}

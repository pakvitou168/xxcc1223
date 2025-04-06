<?php

namespace App\Models\UserManagement;

use App\Models\UserPermissionTrait;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use UserPermissionTrait;
    
    static $functionCode = 'GROUP';

    protected $table = 'idm_group';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}

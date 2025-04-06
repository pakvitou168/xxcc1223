<?php

namespace App\Models\UserManagement\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = 'idm_group';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}

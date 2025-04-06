<?php

namespace App\Models\UserManagement\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $table = 'idm_branch';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}

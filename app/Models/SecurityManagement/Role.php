<?php

namespace App\Models\SecurityManagement;

use App\Actionable;
 use App\Enums\RecordStatus;
use App\Traits\StatusScope;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use Actionable, StatusScope;

    protected $table = 'sm_role';
    protected $hidden = ['pivot'];

    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'status' => RecordStatus::class,
    ];

    public function groups() {
        return $this->belongsToMany(Group::class, 'sm_group_role', 'role_id', 'group_id')->withTimestamps();
    }
    
    public function permissions() {
        return $this->belongsToMany(Permission::class, 'sm_permission_role', 'role_id', 'permission_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'sm_user_role', 'role_id', 'user_id')->withTimestamps();
    }
}

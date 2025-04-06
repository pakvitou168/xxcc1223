<?php

namespace App\Models\SecurityManagement;

use App\Actionable;
 use App\Enums\RecordStatus;
use App\Traits\StatusScope;
use Illuminate\Database\Eloquent\Model;
use Plb\SecurityManagement\Models\Permission as SmPermission;
class Permission extends SmPermission
{
    use Actionable, StatusScope;
    protected $table = 'sm_permission';
    protected $hidden = ['pivot'];

    protected $fillable = [
        'code',
        'name',
        'function_id',
        'app_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'status' => RecordStatus::class
    ];

    public function fnc() {
        return $this->belongsTo(Fnc::class, 'function_id', 'id');
    } 

    public function application() {
        return $this->belongsTo(Application::class, 'app_id', 'id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'sm_user_permission', 'permission_id', 'user_id')->withTimestamps();
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'sm_permission_role', 'permission_id', 'role_id')->withTimestamps();
    }
}

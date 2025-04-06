<?php

namespace App\Models\SecurityManagement;

use App\Actionable;
 use App\Enums\RecordStatus;
use App\Traits\StatusScope;
use Illuminate\Database\Eloquent\Model;
class Group extends Model
{
    use Actionable, StatusScope;

    protected $table = 'sm_group';
    protected $hidden = ['pivot'];

    protected $fillable = [
        'code',
        'name',
        'status',
        'is_default'
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'status' => RecordStatus::class,
        'is_default' => 'boolean'
    ];

    public function roles() {
        return $this->belongsToMany(Role::class, 'sm_group_role', 'group_id', 'role_id')->withTimestamps();
    }  

    public function users() {
        return $this->belongsToMany(User::class, 'sm_user_group', 'group_id', 'user_id')->withTimestamps();
    }
}

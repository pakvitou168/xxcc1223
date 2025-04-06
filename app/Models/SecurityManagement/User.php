<?php

namespace App\Models\SecurityManagement;

use App\Actionable;
use App\Enums\RecordStatus;
use App\Traits\StatusScope;
use Plb\SecurityManagement\Enums\UserStatus;
use Plb\SecurityManagement\Models\User as SmUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;
use Laravel\Passport\HasApiTokens as HasApiTokensPassport;

class User extends SmUser
{
    use Actionable, StatusScope;

    // protected $table = 'sm_user';
    protected $hidden = ['password', 'remember_token', 'pivot'];

    protected $fillable = [
        'username',
        'email',
        'full_name',
        'password',
        'authenticator',
        'remember_token',
        'status',
        'is_default',
        'home_branch'
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'status' => UserStatus::class
    ];

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'sm_user_org', 'user_id', 'org_id')->withTimestamps();
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'sm_user_branch', 'user_id', 'branch_id')->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'sm_user_group', 'user_id', 'group_id')->withTimestamps();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'sm_user_role', 'user_id', 'role_id')->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'sm_user_permission', 'user_id', 'permission_id')->withTimestamps();
    }

    protected function getAllFunctions(): Collection
    {
        return User::active()->find($this->id)->allPermissions;
    }

    protected function getAllPermissionCodes(): Collection
    {
        return User::active()->find($this->id)->allPermissions
            ->map(function ($item) {
                return str_replace('.VIEW', '', $item->code);
            });
    }

    protected function allFunctions(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getAllFunctions(),
        )->shouldCache();
    }

    protected function allPermissionCodes(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getAllPermissionCodes(),
        )->shouldCache();
    }
}

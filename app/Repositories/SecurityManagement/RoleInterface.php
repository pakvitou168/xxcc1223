<?php

namespace App\Repositories\SecurityManagement;

use App\Models\SecurityManagement\Role;
use App\Repositories\CrudInterface;

interface RoleInterface extends CrudInterface {
    public function allByStatusActive();
    public function syncPermissionRoles(Role $role, array $permissionIds);
    public function getPermissionsByRole(int $roleId);
}
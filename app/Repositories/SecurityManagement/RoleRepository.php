<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudRepository;
use App\Models\SecurityManagement\Role;

class RoleRepository extends CrudRepository implements RoleInterface {
  
  protected $model;

  public function __construct(Role $model) {
    parent::__construct($model);
    $this->model = $model;
  }

  public function allByStatusActive() {
    return $this->model
              ->active()
              ->orderBy('name', 'asc')
              ->get();
  }

  public function syncPermissionRoles(Role $role, array $permissionIds) {
    $role->permissions()->sync($permissionIds);
  }

  public function getPermissionsByRole(int $roleId) {
    $role = $this->find($roleId);
    
    return $role->permissions->transform(fn($item) => ['id' =>$item->id, 'name' => $item->name, 'app_id' => $item->app_id]);
  }
}

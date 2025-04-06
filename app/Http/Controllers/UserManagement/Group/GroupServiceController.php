<?php

namespace App\Http\Controllers\UserManagement\Group;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Role;

class GroupServiceController extends Controller
{
    public function getGroupPermissions() {
        $appRoles = Role::select('code', 'app_code', 'name')->where('status', '<>', 'DEL')->orderBy('app_code')->get()->groupBy('app_code');

        $permissions = [];
        $appRoles->each(function($roles, $appCode) use (&$permissions) {
            $rolesArr = $roles->map(function($role) use ($appCode) {
                return $this->makeTreeSelectArray($appCode . '#' . $role->code, $role->name . ' (' . $role->code . ')');
            });

            $permissions[] = $this->makeTreeSelectArray($appCode, $appCode, $rolesArr);
        });

        return $permissions;
    }

    /**
     * 
     */
    private function makeTreeSelectArray($id, $label, $children = null) {
        $arr = [
            'id' => $id,
            'label' => $label
        ];
        if ($children) $arr['children'] = $children;
        return $arr;
    }
}

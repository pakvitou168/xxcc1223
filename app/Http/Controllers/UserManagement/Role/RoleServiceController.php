<?php

namespace App\Http\Controllers\UserManagement\Role;

use App\Http\Controllers\Controller;
use App\Models\RefEnum;
use App\Models\UserManagement\Application;
use App\Models\UserManagement\Functions;
use App\Models\UserManagement\Role;
use Illuminate\Support\Facades\Http;
use App\Traits\DataTable;

class RoleServiceController extends Controller
{
    use DataTable;

    public function listAppCodes() {
        return Application::where('status', 'ACT')
            ->orderBy('name')
            ->pluck('name', 'code');
    }

    public function listPermissions($appCode) {
        $functions = Functions::select('code', 'name', 'permission')
            ->where('app_code', $appCode)
            ->where('status', 'ACT')
            ->orderBy('name')
            ->get();

        $childFunctions = $functions->map(function($item) use ($appCode) {
            $permissionsArr = explode('#', $item->permission);

            $childPermissions = collect($permissionsArr)->map(function($permission) use ($appCode, $item) {
                return $this->makeTreeSelectArray($appCode . '#' . $item->code . '#' . $permission, $permission);
            });

            return $this->makeTreeSelectArray($appCode . '#' . $item->code, $item->name . ' (' . $item->code . ')', $childPermissions);
        });

        return [
            $this->makeTreeSelectArray($appCode, $appCode, $childFunctions)
        ];
    }

    private function makeTreeSelectArray($id, $label, $children = null) {
        $arr = [
            'id' => $id,
            'label' => $label
        ];
        if ($children) $arr['children'] = $children;
        return $arr;
    }

    public function listStatuses() {
        return RefEnum::listIdmStatuses();
    }

    // Show list functions in role detail
    public function listFunctions(Role $role) {
        $response = Http::withHeaders(['User-Id' => auth()->id()])
            ->get(config('pgi.api_base_url') . 'v1/role/' . $role->id);

        if ($response->failed()) {
            return response($response->json(), $response->status());
        }

        $response = $response->collect();
        $functions = $response['functions'];

        $functionIds = collect($functions)->pluck('id');

        return $this->generateTableData(Functions::
            with([
                'roles' => function($query) use ($role) {
                    $query->select('idm_role.id', 'permission')
                        ->where('idm_role.id', $role->id)
                        ->where('idm_role_func.status', 'ACT');
                },
            ])
            ->whereIn('id', $functionIds)
            ->orderBy('name')
        );
    }
}

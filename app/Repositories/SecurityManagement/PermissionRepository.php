<?php

namespace App\Repositories\SecurityManagement;

use App\Models\SecurityManagement\Permission;
use App\Repositories\CrudRepository;
use App\Models\SecurityManagement\Application;
use App\Models\SecurityManagement\Fnc;

class PermissionRepository extends CrudRepository implements PermissionInterface {

    protected $model;

    public function __construct(Permission $model) {
        parent::__construct($model);
        $this->model = $model;
    }

    public function insert(array $datas) {
        return $this->model->insert($datas);
    }

    public function allByFunctionId($function_id) {
        return $this->model
                    ->where('function_id', $function_id)
                    ->active()
                    ->orderBy('id', 'asc')
                    ->get();
    }

    public function destroy(array $ids) {
        return $this->model->destroy($ids);
    }

    public function updateByFunctionId(array $data, $function_id) {
        return $this->model->where('function_id', $function_id)->update($data);
    }

    public function listPermissionsByAppsAndFunctions()
    {
        $groupedFunctionIds = $this->getGroupedFunctionIds();
        $groupedPermissions = $this->listGroupedPermissions();
        $functions = $this->listFunctions();
        return Application::select('id', 'code', 'name')
            ->whereHas('permissions', function($query) {
                $query->active();
            })
            ->orderBy('name')
            ->active()
            ->get()
            ->map(function ($item) use ($groupedFunctionIds, $functions, $groupedPermissions) {
                $functionIds = optional($groupedFunctionIds)[$item->id];

                $item->functions = $functions->whereIn('id', $functionIds)->values();
                $item->functions->transform(function ($i) use ($groupedPermissions) {
                    $i->permissions = optional($groupedPermissions)[$i->id];
                    return $i;
                });

                return $item;
            });
    }

    private function listFunctions()
    {
        $functionIds = Permission::select('function_id')->active()->distinct()->pluck('function_id');

        return Fnc::select('id', 'code', 'name')->whereIn('id', $functionIds)
            ->active()
            ->orderBy('name')
            ->get()->makeHidden(['app','app_name']);
    }

    private function getGroupedFunctionIds()
    {
        return Permission::select('function_id', 'app_id')
            ->active()
            ->get()
            ->groupBy('app_id')
            ->map(function ($item) {
                return $item->unique('function_id')->pluck('function_id');
            });
    }

    private function listGroupedPermissions()
    {
        return Permission::select('id', 'name', 'code', 'function_id')->active()->get()->groupBy('function_id');
    }
}

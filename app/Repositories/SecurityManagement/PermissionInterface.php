<?php

namespace App\Repositories\SecurityManagement;

use App\Repositories\CrudInterface;

interface PermissionInterface extends CrudInterface {
    public function insert(array $datas);
    public function allByFunctionId($function_id);
    public function destroy(array $ids);
    public function updateByFunctionId(array $data, $function_id);
    public function listPermissionsByAppsAndFunctions();
}
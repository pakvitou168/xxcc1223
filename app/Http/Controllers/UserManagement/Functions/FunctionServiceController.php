<?php

namespace App\Http\Controllers\UserManagement\Functions;

use App\Http\Controllers\Controller;
use App\Models\RefEnum\RefEnum;

class FunctionServiceController extends Controller
{
    /**
     * listPermissions
     *
     * @return void
     */
    public function listPermissions()
    {
        return RefEnum::idmActions()->pluck('name', 'enum_id');
    }
    /**
     * listStatuses
     *
     * @return void
     */
    public function listStatuses()
    {
        return RefEnum::idmStatus();
    }
}

<?php

namespace App\Http\Controllers\ProductConfiguration\AccessRule;

use App\Http\Controllers\Controller;
use App\Models\Make\Make;
use App\Models\UserManagement\User\User;

class AccessRuleServiceController extends Controller
{
    public function getAccessRuleServices() {
        $user = User::select('id', 'username')->where('status', 'ACT')->get()->pluck('username', 'id');
        $makes = Make::select('id', 'make')->where('status', 'ACT')->get()->pluck('make', 'id');

        return [
            'userOptions' => $user,
            'makeOptions' => $makes
        ];
    }

    public function listModelsByMakeId($makeId) {
        $make = Make::find($makeId);
        return $make->model()->where('status', 'ACT')->get()->pluck('model', 'id');
    }
}

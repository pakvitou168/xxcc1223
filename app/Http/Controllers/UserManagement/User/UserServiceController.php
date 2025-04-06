<?php

namespace App\Http\Controllers\UserManagement\User;

use App\Http\Controllers\Controller;
use App\Models\RefEnum;
use App\Models\SecurityManagement\Branch;
use App\Models\SecurityManagement\Group;
use App\Models\SecurityManagement\User;
use App\Models\SecurityManagement\UserBranch;
// use App\Models\UserManagement\User\Branch;
// use App\Models\UserManagement\User\Group;
// use App\Models\UserManagement\User\User;
// use App\Models\UserManagement\User\UserBranch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use stdClass;

class UserServiceController extends Controller
{
    public function getService()
    {
        $branch = Branch::get()->pluck('branch_name_en', 'branch_code');
        $group = Group::select('name', 'code')
            ->where('status', 'ACT')
            ->pluck('name', 'code');
        $statuses = RefEnum::listIdmStatuses();

        return array('branch' => $branch, 'group' => $group, 'statuses' => $statuses);
    }
    public function getRole($id)
    {
        $header = [
            'User-Id' => auth()->id()
        ];
        $role = Http::timeout(8)->withHeaders($header)->get(config('pgi.api_base_url') . 'v1/role');
        $groupedPgi = collect(json_decode($role))->groupBy('app_code')['PGI']->map(function ($item) {
            $roleItem = new stdClass();
            $roleItem->id = $item->code . '#' . $item->id;
            $roleItem->label = $item->name;
            return $roleItem;
        });
        $groupRole = [
            'id' => 'PGI',
            'label' => 'PGI',
            "children" => $groupedPgi,
        ];
        // end block get role api

        // get roles by user assign
        $roleAssigned = User::find($id)->roles()->get()->filter(function($item) {
            return $item->pivot->status == 'ACT';
        })->map(function ($item) {
            return  $item->code . '#' . $item->id;
        })->values();

        // end get roles by user assign
        return array('role' => array($groupRole), 'roleAssigned' => $roleAssigned);
    }
    public function getPermission($id)
    {
        // block get permission api
        $header = [
            'User-Id' => auth()->id()
        ];
        $permisson = Http::timeout(8)->withHeaders($header)->get(config('pgi.api_base_url') . 'v1/function');
        $groupPer = collect(json_decode($permisson))->groupBy('app_code')['PGI']->map(function ($item, $key) {
            $roleItem = new stdClass;
            $permission = array();
            if ($item->permission) {
                $expPermission = explode('#', $item->permission);
                foreach ($expPermission as $val) {
                    $permission[] = [
                        "id" => $val . '#' . $item->code,
                        "label" => $val,
                    ];
                }
            }

            $roleItem->id =  'id-' . $item->id;
            $roleItem->label = $item->code;
            $roleItem->children = $permission;

            return $roleItem;
        });
        $groupPgi = [
            'id' => 'PGI',
            'label' => 'PGI',
            "children" => $groupPer,
        ];
        // end block get permission api

        // get permission by user assign
        $assigned = User::find($id)->permissions()->get()->filter(function($item) {
            return $item->pivot->status == 'ACT';
        })->map(function ($item) {
            $value = explode('#', $item->pivot->permission);
            $code = $item->code;

            return collect($value)->map(function ($item) use ($code) {
                return $item . '#' . $code;
            });
        })->flatten();

        // end get permission by user assign
        return array('permission' => array($groupPgi), 'assign' => $assigned);
    }
    public function getBranch($id)
    {

        $branchCode = UserBranch::where('user_id', $id)->where('status', 'ACT')->groupBy('branch_code')->get(['branch_code']);
        $data = collect($branchCode)->pluck('branch_code');

        $branch = collect($branchCode)->map(function ($item) {
            $data = Branch::where('branch_code', $item->branch_code)->first();
            return $data;
        });
        $value = array('brandCode' => $data, 'branch' => $branch);
        return $value;
    }

    public static function getAuthorizedFunctions() {info('functions: '.json_encode( auth()->user()->allPermissionCodes));
        $authorizedFunctions = auth()->user()->allFunctions ?: collect();
        return $authorizedFunctions->map(function($item) {
            return collect($item)->only(['code', 'permission']);
        });
    }
}

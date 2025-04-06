<?php

namespace App\Http\Controllers\UserManagement\Group;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Group;
use Illuminate\Http\Request;
use App\Models\UserManagement\Role;
use App\Traits\DataTable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class GroupController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Group::class, 'group');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(Group::where('status', '!=', 'DEL')->latest('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $reqBody = $this->getRequestBody($request);

        try {
            $response = Http::withHeaders(['User-Id' => auth()->id()])->post(config('pgi.api_base_url') . 'v1/group', $reqBody);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Record is created.'
                ];
            }
        } catch(RequestException $e) {
            info($e);
            return $e;
        }
    }

    private function getRequestBody($request) {
        return [
            'attributes' => [
                "additionalProp1" => "additionalProp1",
            ],
            'code' => $request->code,
            'default_group' => '0',
            'desc' => $request->description,
            'name' => $request->name,
            'roles' => $this->prepareRoles($request->permissions),
            'status' => 'ACT'
        ];
    }

    private function prepareRoles($permissions) {

        return collect($permissions)->map(function($item) {
            $permissionArr = explode('#', $item);

            if (count($permissionArr) == 1) {
                $roles = Role::select('app_code', 'code')->where('status', '<>', 'DEL')->where('app_code', $permissionArr[0])->get();
                return $roles->map(function($item) {
                    return [
                        'app_code' => $item->app_code,
                        'code' => $item->code
                    ];
                });
            } else if (count($permissionArr) == 2) {
                return [
                    [
                        'app_code' => $permissionArr[0],
                        'code' => $permissionArr[1]
                    ]
                ];
            }
        })->flatten(1);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserManagement\Group\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        return $group;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserManagement\Group\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group) {
        try {
            $response = Http::withHeaders(['User-Id' => auth()->id()])->get(config('pgi.api_base_url') . 'v1/group/detail/' . $group->id);
            $data = json_decode($response->body());

            $data->permissions = collect($data->appRole)->map(function($item) {
                $appCode = $item->app_code;
                $groupRolesCount = count($item->roles);
                $appRolesCount = Role::where('status', '<>', 'DEL')->where('app_code', $appCode)->count();
                if ($groupRolesCount == $appRolesCount) return [$appCode];

                return collect($item->roles)->map(function($role) use ($appCode) {
                    return $appCode . '#' . $role->code;
                });
            })->flatten(1);

            return json_encode($data);
        } catch(RequestException $e) {
            info($e);
            return $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserManagement\Group\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $reqBody = $this->getRequestBody($request);
        try {
            $response = Http::withHeaders(['User-Id' => auth()->id()])->put(config('pgi.api_base_url') . 'v1/group/' . $group->id, $reqBody);
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Record is updated.'
                ];
            }
        } catch(RequestException $e) {
            info($e);
            return $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserManagement\Group\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->status = 'DEL';

        if ($group->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

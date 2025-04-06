<?php

namespace App\Http\Controllers\UserManagement\Role;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Functions;
use App\Models\UserManagement\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\DataTable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            Role::where('status', '<>', 'DEL')
                ->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request, null);

        $reqBody = $this->getRequestBody($request);

        try {
            $response = Http::withHeaders(['User-Id' => auth()->id()])
                ->post(config('pgi.api_base_url') . 'v1/role', $reqBody);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Record is created.'
                ];
            }
        } catch(RequestException $e) {
            info($e);
        }
    }

    private function validateRequest($request, $model) {
        $request->validate([
            'code' => [
                'required',
                'max:16',
                Rule::unique(Role::class, 'code')->ignore($model)->where('status', 'ACT')
            ],
            'name' => 'required|max:100',
            'app_code' => 'required',
            'permissions' => 'required'
        ]);
    }

    private function getRequestBody($request) {
        return [
            'app_code' => $request->app_code,
            'attributes' => [
                "additionalProp1" => "additionalProp1",
            ],
            'code' => $request->code,
            'description' => $request->description,
            'functions' => $this->prepareFunctions($request->permissions),
            'name' => $request->name,
            'status' => $request->status ?? 'ACT',
        ];
    }

    // Prepare functions for submit
    private function prepareFunctions($permissions) {
        $result = [];
        $singlePermissions = [];

        collect($permissions)->each(function($item) use (&$result, &$singlePermissions) {
            $permissionArr = explode('#', $item);

            // If select apps
            if (count($permissionArr) === 1) {
                $functions = Functions::select('code', 'app_code', 'permission')
                    ->where('app_code', $permissionArr[0])
                    ->where('status', 'ACT')
                    ->get();

                $appAllFunctions = $functions->map(function($item) {
                    return [
                        'app_code' => $item->app_code,
                        'code' => $item->code,
                        'permission' => $item->permission,
                    ];
                })->toArray();

                $result = [...$result, ...$appAllFunctions];

            // If select functions
            } else if (count($permissionArr) === 2) {
                $functions = Functions::select('code', 'app_code', 'permission')
                    ->where('app_code', $permissionArr[0])
                    ->where('code', $permissionArr[1])
                    ->first();

                $result[] = [
                    'app_code' => $functions->app_code,
                    'code' => $functions->code,
                    'permission' => $functions->permission,
                ];

            // If select single permissions
            } else if (count($permissionArr) === 3) {
                $singlePermissions[] = [
                    'app_code' => $permissionArr[0],
                    'code' => $permissionArr[1],
                    'permission' => $permissionArr[2],
                ];
            }
        });

        // If has single permissions
        if (count($singlePermissions) > 0) {

            $uniqueCodes = [];
            $uniqueFunctions = [];

            foreach($singlePermissions as $singlePermission) {
                if (!in_array($singlePermission['code'], $uniqueCodes)) {
                    $uniqueCodes[] = $singlePermission['code'];
                    $uniqueFunctions [] = [
                        'app_code' => $singlePermission['app_code'],
                        'code' => $singlePermission['code'],
                        'permission' => '',
                    ];
                }
                else continue;
            }

            foreach($singlePermissions as $singlePermission) {
                foreach($uniqueFunctions as &$uniqueFunction) {
                    if ($singlePermission['code'] === $uniqueFunction['code']) {
                        // Join permission string
                        if ($uniqueFunction['permission'] === '')
                            $uniqueFunction['permission'] .= $singlePermission['permission'];
                        else
                            $uniqueFunction['permission'] .= '#' . $singlePermission['permission'];
                    }
                }
            }

            $result = [...$result, ...$uniqueFunctions];
        }

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserManagement\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $response = Http::withHeaders(['User-Id' => auth()->id()])
            ->get(config('pgi.api_base_url') . 'v1/role/' . $role->id);

        if ($response->failed()) {
            return response($response->json(), $response->status());
        }

        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserManagement\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role) {
        $response = Http::withHeaders(['User-Id' => auth()->id()])
            ->get(config('pgi.api_base_url') . 'v1/role/' . $role->id);

        if ($response->failed()) {
            return response($response->json(), $response->status());
        }

        $result = $response->collect();
        $result['permissions'] = $this->preparePermissions($result['app_code'], $result['functions']);

        return $result;
    }

    // Prepare permissions for edit
    private function preparePermissions($appCode, $functions) {
        $result = [];

        $hasSamePermissions = true;
        foreach($functions as $function) {

            $rolePermissions = collect(explode('#', $function['permission']))->sort()->values();
            $allPermissions = collect(explode('#', Functions::where('code', $function['code'])->value('permission')))->sort()->values();

            // Only Role permissions that exists in all permissions
            $acceptedPermissions = $allPermissions->intersect($rolePermissions)->values();

            // If all permissions is the same as role assigned permissions
            if ($rolePermissions == $allPermissions) {
                $result[] = $function['app_code'] . '#' . $function['code'];
            } else {
                // If any functions has different permissions
                $hasSamePermissions = false;

                foreach($acceptedPermissions as $acceptedPermission) {
                    $result[] = $function['app_code'] . '#' . $function['code'] . '#' . $acceptedPermission;
                }
            }
        }

        // If all functions has same permissions and same number of function return appCode
        if ($hasSamePermissions) {

            $roleFunctionsCount = count($functions);
            $allFunctionsCount = Functions::where('app_code', $appCode)
                ->where('status', 'ACT')
                ->count();

            $hasSameFunctionCount = ($roleFunctionsCount === $allFunctionsCount);

            if ($hasSameFunctionCount) return [$appCode];
        }

        return $result;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserManagement\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $this->validateRequest($request, $role);

        $reqBody = $this->getRequestBody($request);

        try {
            $response = Http::withHeaders(['User-Id' => auth()->id()])
                ->put(config('pgi.api_base_url') . 'v1/role/' . $role->id, $reqBody);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Record is updated.'
                ];
            }
        } catch(RequestException $e) {
            info($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserManagement\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $response = Http::delete(config('pgi.api_base_url') . 'v1/role/delete/' . $role->id);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}

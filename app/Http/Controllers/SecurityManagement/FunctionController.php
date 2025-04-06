<?php

namespace App\Http\Controllers\SecurityManagement;

use App\Traits\DataTable;
use Throwable;
use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use App\Constants\ResponseCode;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\SecurityManagement\FunctionInterface;
use App\Repositories\SecurityManagement\PermissionInterface;
use App\Repositories\SecurityManagement\ApplicationInterface;
use App\Http\Requests\SecurityManagement\FunctionRequest as FncRequest;
use Carbon\Carbon;

class FunctionController extends Controller
{
    use DataTable;
    function __construct(
        private FunctionInterface $functionRepository,
        private ApplicationInterface $applicationRepository,
        private PermissionInterface $permissionRepository,
    ) {
        // $this->middleware('has-permission:SM_FUNCTION|VIEW')->only('index', 'show');
        // $this->middleware('has-permission:SM_FUNCTION|NEW')->only('store');
        // $this->middleware('has-permission:SM_FUNCTION|UPD')->only('update');
    }

    public function index(Request $request)
    {
        try {
            return $this->generateTableData($this->functionRepository->query());
        } catch (Throwable $e) {
            Log::error('FUNCTION INDEX: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong']);
        }
    }

    public function store(FncRequest $request)
    {
        DB::beginTransaction();
        try {
            $result = $this->functionRepository->create($request->validated());
            if ($result) {
                $this->storePermission(
                    $request,
                    function_id: $result->id,
                    permissions: $request->permissions
                );
            }

            DB::commit();
            return response()->json(
                ['id' => $result->id,'message' => 'Create success']
            );
        } catch (Throwable $e) {
            Log::error('FUNCTION STORE: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    public function show($id)
    {
        try {
            $resultFunction = $this->functionRepository->find($id);

            /** append two properties for response app_id, permissions */
            if ($resultFunction) {
                $permissions = [];
                $resultPermission = $this->permissionRepository->allByFunctionId($resultFunction->id);

                if (count($resultPermission)) {
                    $permissions = $resultPermission->map(fn($per) => $per->name);
                    $permissions = [...$permissions];
                }

                $resultFunction->permissions = $permissions;
            }

            return response()->json(
                ['functions' => $resultFunction]
            );
        } catch (Throwable $e) {
            Log::error('FUNCTION SHOW: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    public function destroy(Request $request)
    {
    }

    public function update(FncRequest $request)
    {
        try {
            $result = $this->functionRepository->update($request->validated(), $request->id);
            if ($result) {
                $this->updatePermission($request, $request->id);
            }

            return response()->json(['function' => $result,'message' => 'Update Success']);
        } catch (Throwable $e) {
            Log::error('FUNCTION UPDATE: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong']);
        }
    }

    private function updatePermission(Request $request, $function_id)
    {
        /** get all permission by function_id to compare, new or remove*/
        $resultPermission = $this->permissionRepository->allByFunctionId($function_id);
        $currentPermission = $resultPermission->map(fn($permission) => $permission->name);
        $currentPermission = [...$currentPermission];

        $comparation = (object) $this->comparePermission($request->permissions, $currentPermission);
        $appendPermission = $comparation->append;
        $removePermission = $comparation->remove;
        $remainingPermission = $comparation->remaining;

        if (count($appendPermission)) {/** append permission */
            $this->storePermission(
                $request,
                function_id: $function_id,
                permissions: $appendPermission
            );
        }

        if (count($removePermission)) { /** remove permission */
            $getRemove = $resultPermission->whereIn('name', $removePermission);
            $permissionIds = $getRemove->map(fn($permission) => $permission->id);
            $permissionIds = [...$permissionIds];

            /** also remove in permission_role and permission_user  */
            foreach ($permissionIds as $id) {
                $permissionInstance = $this->permissionRepository->find($id);

                /** remove permission role */
                $roleIds = $permissionInstance->roles->pluck('id');
                $permissionInstance->roles()->detach($roleIds);

                /** remove permission user */
                $userIds = $permissionInstance->users->pluck('id');
                $permissionInstance->users()->detach($userIds);
            }

            $this->permissionRepository->destroy($permissionIds);
        }

        if (count($remainingPermission) && $request->app !== $resultPermission[0]->app_id) { /** update app if any change */
            $this->permissionRepository->updateByFunctionId(
                ['app_id' => $request->app_id],
                $function_id
            );
        }
    }

    private function storePermission(Request $request, $function_id, array $permissions)
    {
        $fnc_code = $request->code;
        $app_id = $request->app_id;
        $status = RecordStatus::ACTIVE->value;
        $created_at = Carbon::now();
        $updated_at = Carbon::now();
        $datas = [];

        foreach ((object) $permissions as $permission) {
            array_push($datas, [
                'code' => $fnc_code . '.' . $permission,
                'name' => $permission,
                'function_id' => $function_id,
                'app_id' => $app_id,
                'status' => $status,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
            ]);
        }

        if ($datas)
            $this->permissionRepository->insert($datas);
    }

    private function comparePermission(array $requestPermission, array $currentPermission)
    {
        $append = array_diff($requestPermission, $currentPermission);
        $remove = array_diff($currentPermission, $requestPermission);
        $remaining = array_diff($currentPermission, array_merge($append, $remove));

        return [
            'append' => $append,
            'remove' => $remove,
            'remaining' => $remaining
        ];
    }

    public function listApplication(Request $request)
    {
        try {
            $result = $this->applicationRepository->allByStatusActive();
            return response()->json($result);
        } catch (Throwable $e) {
            Log::error('FUNCTION LIST APPLICATION: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong']);
        }
    }
}

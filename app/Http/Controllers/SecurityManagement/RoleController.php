<?php

namespace App\Http\Controllers\SecurityManagement;

use App\Constants\ResponseCode;
use App\Constants\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityManagement\RoleRequest;
use App\Repositories\SecurityManagement\PermissionInterface;
use App\Repositories\SecurityManagement\RoleInterface;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    use DataTable;
    public function __construct(private RoleInterface $roleRepo, private PermissionInterface $permissionRepo)
    {
        // $this->middleware('has-permission:SM_ROLE|VIEW')->only('index', 'show');
        // $this->middleware('has-permission:SM_ROLE|NEW')->only('store');
        // $this->middleware('has-permission:SM_ROLE|UPD')->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->roleRepo->query();

        return $this->generateTableData($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        DB::beginTransaction();

        $data = $request->all();
        try {
            $role = $this->roleRepo->create($data);

            if ($role) {
                $permissionIds = collect($request->permissions)->pluck('id')->toArray();

                $this->roleRepo->syncPermissionRoles($role, $permissionIds);
                DB::commit();

                return response()->json(['role' => $role, 'message' => 'Record is created successfully.']);
            }
        } catch (\Exception $e) {
            Log::error("Create Role error:" . $e->getMessage());
            DB::rollBack();

            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->roleRepo->find($id);
        $role->permissions = $this->roleRepo->getPermissionsByRole($id);

        if ($role) {
            return response()->json(['role' => $role]);
        }
        return response()->json(['message' => 'Something went wrong'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        DB::beginTransaction();

        $data = $request->all();
        try {
            $updated = $this->roleRepo->update($data, $id);
            if ($updated) {
                $role = $this->roleRepo->find($id);

                $permissionIds = collect($request->permissions)->pluck('id')->toArray();

                $this->roleRepo->syncPermissionRoles($role, $permissionIds);
                DB::commit();

                return response()->json(['role' => $role, 'message' => 'Record is updated successfully.']);
            }
        } catch (\Exception $e) {
            Log::error("Update Role error:" . $e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong']);
        }
    }

    public function listPermissions()
    {
        return $this->permissionRepo->listPermissionsByAppsAndFunctions();
    }
}

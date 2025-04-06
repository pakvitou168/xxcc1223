<?php

namespace App\Http\Controllers\SecurityManagement;

use App\Traits\DataTable;
use Throwable;
use Illuminate\Http\Request;
use App\Constants\ResponseCode;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityManagement\GroupRequest;
use App\Repositories\SecurityManagement\RoleInterface;
use App\Repositories\SecurityManagement\GroupInterface;

class GroupController extends Controller
{
    use DataTable;
    function __construct(
        private GroupInterface $groupRepository,
        private RoleInterface $roleRepository
    ) {
        // $this->middleware('has-permission:SM_GROUP|VIEW')->only('index', 'show');
        // $this->middleware('has-permission:SM_GROUP|NEW')->only('store');
        // $this->middleware('has-permission:SM_GROUP|UPD')->only('update');
    }

    public function index(Request $request)
    {
        try {
            $result = $this->groupRepository->query();

            return $this->generateTableData($result);
        } catch (Throwable $e) {
            Log::error('GROUP INDEX: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong']);
        }
    }

    public function store(GroupRequest $request)
    {
        DB::beginTransaction();
        try {
            $resultGroup = $this->groupRepository->create($request->validated());
            $roles = $request->roles;

            if ($resultGroup && count($roles)) {
                $roleIds = collect($roles)->map(fn($role) => $role['id']);
                $resultGroup->roles()->sync($roleIds);
            }

            DB::commit();
            return response()->json(
                ['id' => $resultGroup->id, 'message' => 'Create success']
            );
        } catch (Throwable $e) {
            Log::error('GROUP STORE: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong']);
        }
    }

    public function show($id)
    {
        try {
            $result = $this->groupRepository->findWithRole($id);

            return response()->json(
                ['group' => $result]
            );
        } catch (Throwable $e) {
            Log::error('GROUP SHOW: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong'],
            );
        }
    }

    public function destroy(Request $request)
    {
    }

    public function update(GroupRequest $request)
    {
        try {
            $groupResult = $this->groupRepository->update($request->validated(), $request->id);

            if ($groupResult) {
                $roleIds = collect($request->roles)->map(fn($role) => $role['id']);
                $group = $this->groupRepository->find($request->id);
                $group->roles()->sync($roleIds);
            }
            return response()->json(
                ['group' => $groupResult, 'message' => 'Update success']
            );
        } catch (Throwable $e) {
            Log::error('GROUP UPDATE: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    public function listRole(Request $request)
    {
        try {
            $result = $this->roleRepository->allByStatusActive();
            return response()->json($result);
        } catch (Throwable $e) {
            Log::error(message: 'GROUP LIST ROLE: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }
}

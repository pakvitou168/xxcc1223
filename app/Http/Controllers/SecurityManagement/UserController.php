<?php

namespace App\Http\Controllers\SecurityManagement;

use App\Traits\SMDataTable;
use Throwable;
use Illuminate\Http\Request;
use App\Constants\ResponseCode;
use App\Constants\ResponseMessage;
use App\Enums\SMUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityManagement\UserRequest;
use App\Repositories\SecurityManagement\OrganizationInterface;
use App\Repositories\SecurityManagement\BranchInterface;
use App\Repositories\SecurityManagement\GroupInterface;
use App\Repositories\SecurityManagement\UserInterface;
use App\Repositories\SecurityManagement\RoleInterface;
use App\Repositories\SecurityManagement\PermissionInterface;
use App\Helpers\ActionEventHelper;
use App\Http\Controllers\UserManagement\User\UserServiceController;
use App\Models\SecurityManagement\User;

class UserController extends Controller
{
    use SMDataTable;
    function __construct(
        private UserInterface $userRepository,
        private OrganizationInterface $orgRepository,
        private BranchInterface $branchRepository,
        private GroupInterface $groupRepository,
        private RoleInterface $roleRepository,
        private PermissionInterface $permissionRepository,
    ) {
        // $this->middleware('has-permission:SM_USER|VIEW')->only('index', 'show');
        // $this->middleware('has-permission:SM_USER|NEW')->only('store');
        // $this->middleware('has-permission:SM_USER|UPD')->only('update');
    }

    public function index(Request $request)
    {
        try {
            $result = $this->generateTableData($this->userRepository->query());
            $result->data->transform(function ($u) {
                $empBranch = '';
                if ($empBranch = $u->branches()->first()) {
                    $empBranch = $empBranch->name;
                }
                $u->emp_branch = $empBranch;
                $u->regional_name = optional(optional($u)->employee)->regional_name;
                $u->position_name = optional(optional($u)->employee)->position_name;
                return $u;
            });
            return response()->json($result);
        } catch (Throwable $e) {
            Log::error('USER INDEX: ' . $e->getMessage());
            return response()->json(
                ['error' => $e->getMessage()]
            );
        }
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            if (!empty($request->password)) {
                $validated['password'] = bcrypt($request->password);
            }

            $result = $this->userRepository->create($validated);

            if ($result) {
                $this->syncDataRelationship($result->organizations(), $request->organizations);
                $this->syncDataRelationship($result->branches(), $this->filterBranchesNestOrganization($request->organizations));
                $this->syncDataRelationship($result->groups(), $request->groups);
                $this->syncDataRelationship($result->roles(), $request->roles);
                $this->syncDataRelationship($result->permissions(), $request->permissions);
            }

            DB::commit();
            return response()->json(
                ['id' => $result->id]);
        } catch (Throwable $e) {
            Log::error('USER STORE: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    public function show($id)
    {
        try {
            $result = $this->userRepository->findWithRelationship($id);

            if ($result) {
                $branches = $result->branches;
                foreach ($result->organizations as $index => $organization) {
                    $result->organizations[$index]['branches'] = $this->findBranchByOrgId($branches, $organization->id);
                }
            }

            $result->makeHidden(['branches']);

            return response()->json(
                ['user' => $result]
            );
        } catch (Throwable $e) {
            Log::error('USER SHOW: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    public function destroy(Request $request)
    {
    }

    public function update(UserRequest $request)
    {
        try {
            $validated = $request->validated();
            if ($request->authenticator === SMUser::LDAP->value || !$validated['password']) {
                $new_validated = collect($validated)->forget('password');
                $validated = $new_validated->toArray();
            } else {
                $validated['password'] = bcrypt($request->password);
            }

            $result = $this->userRepository->update($validated, $request->id);

            if ($result) {
                $userInstance = $this->userRepository->find($request->id);

                if ($userInstance) {
                    $this->syncDataRelationship($userInstance->organizations(), $request->organizations);
                    $this->syncDataRelationship($userInstance->branches(), $this->filterBranchesNestOrganization($request->organizations));
                    $this->syncDataRelationship($userInstance->groups(), $request->groups);
                    $this->syncDataRelationship($userInstance->roles(), $request->roles);
                    $this->syncDataRelationship($userInstance->permissions(), $request->permissions);
                }
            }


            return response()->json(
                ['user' => $result,'message' => 'User updated']
            );
        } catch (Throwable $e) {
            Log::error('USER UPDATE: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    private function syncDataRelationship($relationship, array|null $datas)
    {
        if (isset($datas)) {
            $dataIds = collect($datas)->map(fn($data) => $data['id']);
            $relationship->sync($dataIds);
        }
    }

    private function filterBranchesNestOrganization(array $organizations): array
    {
        $branches = [];

        foreach ($organizations as $organization) {
            foreach ($organization['branches'] as $branch) {
                array_push($branches, $branch);
            }
        }

        return $branches;
    }

    private function findBranchByOrgId($branches, $orgId)
    {
        return collect($branches)->filter(function ($branch) use ($orgId) {
            return $branch->org_id === $orgId;
        })->values();
    }

    public function listOrganization(Request $request)
    {
        try {
            $result = $this->orgRepository->allWithBranch();

            $result->makeHidden(['code', 'status', 'partner_ccy', 'created_at', 'updated_at']);

            return response()->json(
                $result
            );
        } catch (Throwable $e) {
            Log::error('USER LIST ORGANIZATION: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong'],
            );
        }
    }

    public function listGroup(Request $request)
    {
        try {
            $result = $this->groupRepository->allByStatusActive();
            $result->makeHidden(['code', 'status', 'is_default', 'created_at', 'updated_at']);

            return response()->json(
                $result
            );
        } catch (Throwable $e) {
            Log::error('USER LIST GROUP: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    public function listRole(Request $request)
    {
        try {
            $result = $this->roleRepository->allByStatusActive();
            $result->makeHidden(['code', 'status', 'is_default', 'created_at', 'updated_at']);

            return response()->json(
                $result
            );
        } catch (Throwable $e) {
            Log::error('USER LIST GROUP: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Something went wrong']
            );
        }
    }

    public function listPermission(Request $request)
    {
        try {
            $result = $this->permissionRepository->listPermissionsByAppsAndFunctions();

            return response()->json(
                $result
            );
        } catch (Throwable $e) {
            Log::error('USER LIST GROUP: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Somethig went wrong']
            );
        }
    }

    public function listAuthenticator(Request $request)
    {
        try {
            $result = collect([
                [
                    'code' => SMUser::LDAP,
                    'name' => SMUser::LDAP
                ],
                [
                    'code' => SMUser::LARAVEL,
                    'name' => SMUser::LARAVEL
                ]
            ]);

            return response()->json(
                $result
            );
        } catch (Throwable $e) {
            Log::error('LIST AUTHENTICATOR: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Somethig went wrong']
            );
        }
    }

    public function listBranchesByOrg(Request $request)
    {
        try {
            $org_code = $request->org_code;
            $result = $this->branchRepository->listBranchesByOrg($org_code);
            $result->makeHidden(['status', 'business_hour', 'is_full_service', 'created_at', 'updated_at']);

            $result->transform(function ($item) {
                $item->name = $item->code . '-' . $item->name;

                return $item;
            });

            return response()->json(
                $result
            );
        } catch (Throwable $e) {
            Log::error('USER LIST BRANCH: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Somethig went wrong']
            );
        }
    }

    public function actions(Request $request, User $user)
    {
        return ActionEventHelper::actions($request, $user);
    }
}

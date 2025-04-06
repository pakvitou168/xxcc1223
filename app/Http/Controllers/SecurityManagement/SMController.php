<?php

namespace App\Http\Controllers\SecurityManagement;

use App\Exceptions\InsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SM\ChangePasswordRequest;
use App\Models\RecordStatus;
use App\Models\SecurityManagement\Application as SMApplication;
use App\Models\SecurityManagement\Branch;
use App\Models\SecurityManagement\Fnc as SMFnc;
use App\Models\SecurityManagement\Group as SMGroup;
use App\Models\SecurityManagement\GroupRole as SMGroupRole;
use App\Models\SecurityManagement\Permission as SMPermission;
use App\Models\SecurityManagement\Role as SMRole;
use App\Models\SecurityManagement\RolePermission;
use App\Models\SecurityManagement\User as SMUser;
use App\Models\SecurityManagement\UserBranch as SMUserBranch;
use App\Models\SecurityManagement\UserPermission as SMUserPermission;
use App\Models\SecurityManagement\UserRole as SMUserRole;
use App\Models\SecurityManagement\UserGroup as SMUserGroup;
use App\Models\UserManagement\Functions as SMFunction;
use App\Models\UserManagement\GroupRole as IDMGroupRole;
use App\Models\UserManagement\RoleFunction as IDMRoleFunction;
use App\Models\UserManagement\User\UserBranch as IDMUserBranch;
use App\Models\UserManagement\User\UserFunction as IDMUserFnc;
use App\Models\UserManagement\User\UserGroup as IDMUserGroup;

use App\Models\UserManagement\User\UserRole as IDMUserRole;
use Auth;
use DB;
use Hash;
use Log;
use Request;

class SMController extends Controller
{
    public function migrate()
    {
        try {
            DB::beginTransaction();
            // dd(now());
            DB::table('sm_user')->whereIn('username',['demo1','demo2'])->update([
                'password' => bcrypt('demo@1234')
            ]);
            // $this->beSuperAdmin(SMUser::whereUsername('demo1')->first());
            $this->migrateRoleFunction();
            // $this->migrateGroupRole();
            // $this->migrateUserFunction(); //function and permission
            // $this->migrateUserGroup();
            // $this->migrateUserRole();
            // $this->migrateUserBranch();
            DB::commit();
            dd("migrate success");
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            dd($e);
        }
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = SMUser::find(auth()->id());
            if (!Hash::check($request->current_password, $user->password)) {
                throw new InsException("Current password does not match");
            }
            $user->update([
                'password' => bcrypt($request->new_password)
            ]);
            return response()->json(['success' => true, 'message' => 'Passsword is updated']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }
    private function beSuperAdmin($user)
    {
        SMPermission::whereStatus(RecordStatus::ACTIVE)->chunkById(100, function ($pms) use ($user) {
            $pms->each(function ($item) use ($user) {
                $permission = SMUserPermission::firstOrNew([
                    'permission_id' => $item->id,
                    'user_id' => $user->id
                ]);
                $permission->save();
            });
        });

    }
    public function migratePermission()
    {
        SMFunction::whereStatus(RecordStatus::ACTIVE)->chunkById(100, function ($fncs) {
            $fncs->each(function ($fnc) {
                $smPermissions = $this->idmtoSmPermission(explode('#', $fnc->permission));
                $smFnc = SMFnc::whereCode($fnc->code)->first();
                collect($smPermissions)->each(function ($pm) use ($smFnc) {

                });
            });
        });
    }
    private function migrateGroupRole()
    {
        IDMGroupRole::has('group')->has('role')->whereStatus(RecordStatus::ACTIVE)->chunkById(100, function ($groupRoles) {
            $groupRoles->each(function ($gR) {
                $group = SMGroup::whereCode($gR->group->code)->first();
                $role = SMRole::whereCode($gR->role->code)->first();
                $smGR = SMGroupRole::firstOrNew([
                    'group_id' => $group->id,
                    'role_id' => $role->id
                ]);
                $smGR->save();
            });
        });
    }
    private function migrateRoleFunction()
    {
        IDMRoleFunction::has('role')->has('fnc')->has('application')->whereStatus(RecordStatus::ACTIVE)->chunkById(10, function ($roleFncs) {
            $roleFncs->each(function ($rFnc) {
                $smPermissions = $this->idmtoSmPermission(explode('#', $rFnc->permission));
                collect($smPermissions)->each(function ($item) use ($rFnc) {
                    $code = $rFnc->fnc->code . '.' . $item;
                    $app = SMApplication::whereCode($rFnc->application->code)->first();
                    $smPermission = SMPermission::whereCode($code)->whereAppId($app->id)->first();
                    $role = SMRole::whereCode($rFnc->role->code)->first();
                    if (!$smPermission) {
                        Log::warning("Permission not found", ['code' => $code, 'fnc' => json_encode($rFnc)]);
                    } else {
                        $smRP = RolePermission::firstOrNew([
                            'permission_id' => $smPermission->id,
                            'role_id' => $role->id
                        ]);
                        $smRP->save();
                    }
                });
            });
        });
    }
    private function idmtoSmPermission($pms)
    {
        $permissions = [];
        $arr = [
            'DEL' => 'DELETE',
            'UPD' => 'UPDATE',
            'APV' => 'APPROVE',
            'PROC' => 'PROCESS',
            'REV' => 'REVISE',
            'ACP' => 'ACCEPT',
            'UPL' => 'UPLOAD'
        ];
        foreach ($pms as $pm) {
            $smPermission = \App\Models\RefEnum::smPermissions()->filter(function ($item) use ($pm) {
                return str_contains($item->value, $pm);
            })->first();

            if (!$smPermission && isset($arr[$pm])) {
                array_push($permissions, $arr[$pm]);
            } elseif($smPermission) {
                array_push($permissions, $smPermission->value);
            }else{
                throw new \Exception("not found".$pm);
            }
        }
        return $permissions;
    }

    private function migrateUserFunction()
    {
        IDMUserFnc::has('fnc')->has('user')->whereStatus(RecordStatus::ACTIVE)->chunkById(100, function ($uFncs) {
            foreach ($uFncs as $uFnc) {
                $permissions = explode('#', $uFnc->permission);
                $smPermissions = $this->idmtoSmPermission($permissions);
                collect($smPermissions)->each(function ($item) use ($uFnc) {
                    $code = $uFnc->fnc->code . '.' . $item;
                    $smPerm = SMPermission::whereCode($code)->first();
                    $smUser = SMUser::whereUsername($uFnc->user->username)->first();
                    if (!$smPerm) {
                        throw new InsException("Permission not found " . $code);
                    } elseif (!$smUser) {
                        throw new InsException("User not found" . @$uFnc->user->username);
                    } else {
                        $sMuP = SMUserPermission::firstOrNew([
                            'permission_id' => $smPerm->id,
                            'user_id' => $smUser->id
                        ]);
                        $sMuP->save();
                    }
                });
            }
        });
    }
    private function migrateUserGroup()
    {
        IDMUserGroup::has('group')->has('user')->whereStatus(RecordStatus::ACTIVE)->has('user')->chunkById(10, function ($ugroups) {
            $ugroups->each(function ($ug) {
                $group = SMGroup::whereCode($ug->group->code)->firstOr(fn() => throw new InsException("Group not found"));
                $smUser = SMUser::whereUsername($ug->user->username)->first();
                if (!$smUser) {
                    throw new InsException("User not found" . @$ug->user->username);
                }
                $sMuGrp = SMUserGroup::firstOrNew([
                    'user_id' => $smUser->id,
                    'group_id' => $group->id
                ]);
                $sMuGrp->save();
            });
        });
    }
    private function migrateUserRole()
    {
        IDMUserRole::has('role')->has('user')->whereStatus(RecordStatus::ACTIVE)->has('user')->chunkById(10, function ($userRoles) {
            $userRoles->each(function ($uR) {
                $role = SMRole::whereCode($uR->role->code)->first();
                $smUser = SMUser::whereUsername($uR->user->username)->first();
                if (!$smUser) {
                    throw new InsException("User not found" . @$uR->user->username);
                }
                $smUR = SMUserRole::firstOrNew([
                    'user_id' => $smUser->id,
                    'role_id' => $role->id
                ]);
                $smUR->save();
            });
        });
    }
    private function migrateUserBranch()
    {
        IDMUserBranch::has('user')->whereStatus(RecordStatus::ACTIVE)->has('branch')->chunkById(10, function ($userBranches) {
            $userBranches->each(function ($uB) {
                $branch = Branch::whereCode($uB->branch->branch_code)->first();
                $smUser = SMUser::whereUsername($uB->user->username)->first();
                if (!$smUser) {
                    throw new InsException("User not found" . @$uB->user->username);
                }
                $smUB = SMUserBranch::firstOrNew([
                    'user_id' => $smUser->id,
                    'branch_id' => $branch->id
                ]);
                $smUB->save();
            });
        });
    }
}

<?php

namespace App\Http\Controllers\UserManagement\User;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Functions;
use App\Models\UserManagement\User\User;
use App\Models\UserManagement\User\UserFile;
use App\Traits\DataTable;
use Exception;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(User::where('status', '<>', 'DEL')->latest('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   $header = [
            'User-Id' => auth()->id()
        ];

        $userReqBody = $this->userRequestBody($request);

        $response =  Http::withHeaders($header)->post(config('pgi.api_base_url') . 'v1/user', $userReqBody);

        if ($response->successful()) {
            $createdUser = User::latest()->first();
            
            // Upload signature
            $this->uploadSignature($request, $createdUser->id);

            // Assign groups
            $groupReqBody = [
                "groups" => json_decode($request->groups),
                "user_id" => $createdUser ? $createdUser->id : null
            ];

            Http::withHeaders($header)->post(config('pgi.api_base_url') . 'v1/assign/group', $groupReqBody)->throw();

            return [
                'code' => $response->status(),
                'msg' => json_decode($response->body())->message
            ];
        } else {
            return [
                'code' => 400,
                'msg' => json_decode($response->body())->message
            ];
        }        
    }

    private function userRequestBody($request) {

        $password = '';
        $isAd = $request->is_ad == 'false' ? false : true;

        if ($isAd == true) {
            $grantType =  'ldap';
        } else {
            $grantType = 'password';
            $password = !empty($request->password) ? base64_encode($request->password) : '';
        }

        return [
            "app_code" => config('pgi.idm_app_code'),
            'email' => $request->email,
            'employee_id' => '',
            'full_name' =>  $request->full_name,
            'home_branch' =>  $request->home_branch,
            'is_ldap' => $grantType,
            'password' => $password,
            "status" => $request->status ?? 'ACT',
            "username" => $request->username,
        ];
    }

    private function uploadSignature($request, $userId) {
        if (!$request->hasFile('signature')) return;
        
        $file = $request->file('signature');

        $randomName = Str::random(8);
        $destinationPath = 'images/signatures/'.date('Y-m-d');
        $extension = $file->getClientOriginalExtension();
        $fileName = 'images/signatures/' . date('Y-m-d') . '/' . $randomName . '_signature.' . $extension;
        $originalName = $file->getClientOriginalName();
        $file->move($destinationPath, $fileName);

        $userFile = UserFile::firstOrCreate(['user_id' => $userId]);

        $userFile->file_url = $fileName;
        $userFile->file_type = 'SIGNATURE';
        $userFile->storage_option = 'URL';
        $userFile->file_name = $originalName;
        $userFile->status = 'ACT';
        $userFile->created_by = auth()->id();
        
        if (!$userFile->wasRecentlyCreated) {
            $userFile->updated_by = auth()->id();
        }

        return $userFile->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserManagement\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        try {
            $header = [
                'User-Id' => auth()->id()
            ];
            $response = Http::withHeaders($header)->get(config('pgi.api_base_url') . 'v1/user/' . $user->id);

            $data = json_decode($response->body());
            $data->is_ad = $data->is_ldap == 'Y' ? true : false;

            // user signature
            $data->signature = UserFile::where('user_id', $user->id)->first();

            return json_encode($data);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserManagement\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        try {
            $header = [
                'User-Id' => auth()->id()
            ];

            $userReqBody = $this->userRequestBody($request);

            $groupReqBody = [
                "groups" => json_decode($request->groups),
                "user_id" => $user->id
            ];

            $rolesReqBody = $this->assignRolesRequestBody($user->id, $request->roles);

            $permissionsReqBody = $this->assignPermissionsRequestBody($user->id, $request->permissions);

            $branchesReqBody = [
                'branches' => array(["app_code" => "PGI", 'branches' => json_decode($request->branches)]),
                'user_id' => $user->id,
            ];

            $respon = Http::pool(fn (Pool $pool) => [
                $pool->withHeaders($header)->put(config('pgi.api_base_url') . 'v1/user/' . $user->id, $userReqBody),
                $pool->withHeaders($header)->post(config('pgi.api_base_url') . 'v1/assign/group', $groupReqBody),
                $pool->withHeaders($header)->post(config('pgi.api_base_url') . 'v1/assign/role', $rolesReqBody),
                $pool->withHeaders($header)->post(config('pgi.api_base_url') . 'v1/assign/function', $permissionsReqBody),
                $pool->withHeaders($header)->post(config('pgi.api_base_url') . 'v1/assign/branch', $branchesReqBody)
            ]);
            info($respon[0]->body());
            info($respon[1]->body());
            info($respon[2]->body());
            info($respon[3]->body());
            info($respon[4]->body());

            if ($respon[0]->ok()) {

                // Upload signature
                $this->uploadSignature($request, $user->id);

                return [
                    'code' => $respon[0]->status(),
                    'msg' => json_decode($respon[0]->body())->message
                ];
            } else {
                return [
                    'code' => 400,
                    'msg' => json_decode($respon[0]->body())->message
                ];
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }
    }

    private function assignRolesRequestBody($id, $roles) {
        $rolesArr = collect(json_decode($roles))->map(function($item) {
            return [
                'app_code' => config('pgi.idm_app_code'),
                'code' => explode('#', $item)[0]
            ];
        });

        return [
            'roles' => $rolesArr,
            'user_id' => $id
        ];
    }

    private function assignPermissionsRequestBody($id, $permissions) {
        $allPermission = [];
        $perId = [];
        $value = [];
        $checkPermission = json_decode($permissions, true);
        if(empty($checkPermission)) {
            $value = [];
        } else {
            if ($checkPermission[0] === 'PGI') {
                $fun = Functions::where('status', 'ACT')->where('app_code', 'PGI')->get();
                foreach ($fun as $val) {
                    $allPermission[] = array('code' => $val->code, 'permission' => $val->permission);
                }
            } else {
                foreach (json_decode($permissions) as $val) {
                    if ($permissionId = explode('-', $val)) {
                        if ($permissionId[0] == 'id') {
                            $fun = Functions::where('id', $permissionId[1])->where('status', 'ACT')->first();
                            $allPermission[] = array('code' => $fun->code, 'permission' => $fun->permission);
                        }
                        if ($permissionId[0] != 'id') {
                            if ($per = explode('#', $val)) {

                                $perId[] = array(
                                    'key' => $per[1],
                                    'value' => $per[0]
                                );
                            }
                        }
                    }
                }
            }

            if ($allPermission) {
                foreach ($allPermission as $val) {
                    $value[] = array(
                        "app_code" =>  "PGI",
                        "code" =>  $val['code'],
                        "permission" =>  $val['permission'],
                    );
                }
            }
            if ($perId) {
                $data = collect($perId)->groupBy('key')->map(function ($item) {
                    return $item->pluck('value');
                });
                foreach ($data as $key => $val) {
                    $data = collect($val)->join('#');
                    $perValue = array(
                        "app_code" =>  "PGI",
                        "code" =>  $key,
                        "permission" =>  $data,
                    );
                    $value[] = $perValue;
                }
            }
        }
        return [
            'functions' => $value,
            'user_id' => $id,
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserManagement\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user = User::find($user->id);
        $user->status = 'DEL';
        $user->update_by = auth()->id();
        $user->save();

        return response([
            'success' => true,
            'message' => 'user deleted'
        ]);
    }
}

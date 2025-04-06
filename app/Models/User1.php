<?php

namespace App\Models;

use App\Models\UserManagement\Functions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use stdClass;

class User1 extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'idm_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthorizedFunctionsAttribute()
    {
        $baseWhere = ['status' => 'ACT', 'app_code' => config('pgi.idm_app_code', 'PGI')];

        $groups = DB::table('idm_user_group')->where('user_id', $this->id)->where('status', 'ACT')->pluck('group_id')->toArray();
        $groupRoles = DB::table('idm_group_role')->where($baseWhere)->whereIn('group_id', $groups)->pluck('role_id')->toArray();
        $userRoles = DB::table('idm_user_role')->where($baseWhere)->where('user_id', $this->id)->pluck('role_id')->toArray();

        $roles = array_unique(array_merge($groupRoles, $userRoles));
        $roleFuncs = DB::table('idm_role_func')->where($baseWhere)->whereIn('role_id', $roles)->get();

        $userFuncs= DB::table('idm_user_func')->where($baseWhere)->where('user_id', $this->id)->get();
        $funcs = collect();

        foreach($userFuncs as $uf) {
            $uf->permission = explode('#', $uf->permission);

            $func = new stdClass;
            $func->id = $uf->function_id;
            if($rf = $roleFuncs->where('function_id', $uf->function_id)->first()) {
                $rf->permission = explode('#', $rf->permission);
                $func->permission = array_unique(array_merge($uf->permission, $rf->permission));
            }else {
                $func->permission = $uf->permission;
            }
            $funcs->push($func);
        }
        foreach($roleFuncs as $rf) {
            if($funcs->where('id', $rf->function_id)->isEmpty()) {
                $func = new stdClass;
                $func->id = $rf->function_id;
                $func->permission = explode('#',$rf->permission);
                $funcs->push($func);
            }
        }
        $functions = Functions::where($baseWhere)->whereIn('id', $funcs->pluck('id')->toArray())->get();

        foreach($functions as $f) {
            $f->permission = array_values($funcs->where('id', $f->id)->first()->permission);
        }

        return $functions;
    }

    protected function allFunctions() : Attribute
    {
        return Attribute::make(get: fn ($value) => $this->getAuthorizedFunctionsAttribute())->shouldCache();
    }
}

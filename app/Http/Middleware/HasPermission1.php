<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HasPermission1
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permissionStr)
    {
        $permissonArr = explode('#', $permissionStr);
        if (count($permissonArr) !== 2) {
            Log::error('The passing permission string is not the right format');
            abort(403,"Unauthorized");
        }

        if ($this->isAuthorized($permissonArr[0], $permissonArr[1])) return $next($request);

        abort(403,"Unauthorized");
    }

    private function isAuthorized($code, $permission) {
        $allFunctions = auth()->user()->allFunctions ?: collect();

        $funcs = $allFunctions->where('code', $code);
        if ($funcs->isNotEmpty()) {
            foreach ($funcs as $func) {
                if (in_array($permission, $func->permission))
                    return true;
            }
        }
        return false;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next , $extra_permission = '')
    {
        $routeName = explode('.', \Request::route()->getName());

        if($routeName[0] == 'reports'){
            if(auth()->user()->hasPermissionTo($extra_permission)){
                return $next($request);
            }else{
                abort(403, 'Unauthorized action.');
            }

        }

        if(auth()->user()->hasPermissionTo($routeName[0])){
            return $next($request);
        }

        // none authorized request
        abort(403, 'Unauthorized action.');


    }
}

<?php

namespace App\Http\Middleware;

use Flash;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDeletePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the request is a DELETE request
        if ($request->isMethod('delete')) {
            // Get the authenticated user
            $user = Auth::user();
            // Check if the user has the "delete" permission
            if (!$user || !$user->can('deletes')) {
                Flash::error("You don't have permission to delete this!");
                return redirect()->back();
            }
        }

        return $next($request);
    }
}

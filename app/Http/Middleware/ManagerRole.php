<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || ! $user->hasRole('manager')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Manager role required',
            ], 403);
        }

        return $next($request);
    }
}
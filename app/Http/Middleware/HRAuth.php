<?php

namespace app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use app\Models\Role;

class HRAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $acceptedRoles = [Role::SuperAdmin()->id, Role::Admin()->id, Role::HR()->id];
        if ( Auth::check() && in_array(Auth::user()->role_id, $acceptedRoles)) 
        {
            return $next($request);
        }
        return response()->json(["message" => "unauthorized"], 401);
    }
}

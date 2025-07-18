<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SessionManagement
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Update last activity
            Session::put('last_activity', time());
            Session::put('user_role', $user->role);
            Session::put('user_permissions', $user->permissions);
            
            // Check session timeout (30 minutes for employees, 2 hours for admins)
            $timeout = $user->isAdmin() ? 7200 : 1800; // seconds
            
            if (Session::has('last_activity') && 
                (time() - Session::get('last_activity')) > $timeout) {
                
                Auth::logout();
                Session::flush();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Session expired',
                    ], 401);
                }
                
                return redirect()->route('login')->withErrors(['error' => 'Session expired']);
            }
        }

        return $next($request);
    }
}

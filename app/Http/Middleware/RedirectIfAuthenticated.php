<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * ✅ Handle an incoming request - Redirect authenticated users away from auth pages
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // ✅ Role-based redirects for authenticated users
                if ($user) {
                    switch ($user->role) {
                        case 'super_admin':
                            return redirect()->route('dashboard');
                        case 'it_admin':
                            return redirect()->route('employees.index');
                        case 'hr':
                            return redirect()->route('employees.index');
                        case 'manager':
                            return redirect()->route('dashboard');
                        case 'express':
                            return redirect()->route('employees.index');
                        case 'employee':
                        default:
                            return redirect()->route('dashboard');
                    }
                }
                
                // ✅ Fallback redirect
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}

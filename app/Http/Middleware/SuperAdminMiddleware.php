<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request for Super Admin only
     */
    public function handle(Request $request, Closure $next)
    {
        // ตรวจสอบว่า login แล้วหรือยัง
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }
            return redirect('/login');
        }

        $user = Auth::user();

        // ✅ ข้าม status check - ให้ super admin เข้าได้เสมอ
        // if (!$user->is_active) { // DISABLED
        //     Auth::logout();
        //     if ($request->ajax()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Account is inactive'
        //         ], 403);
        //     }
        //     return redirect('/login')->withErrors(['error' => 'Account is inactive']);
        // }

        // ตรวจสอบ role - เฉพาะ super_admin เท่านั้น
        if ($user->role !== 'super_admin') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Super Admin privileges required.'
                ], 403);
            }
            
            return redirect('/dashboard')->with('error', 'Access denied. Super Admin privileges required.');
        }

        return $next($request);
    }

    /**
     * ตรวจสอบว่า user active หรือไม่ (ปิดการใช้งานชั่วคราว)
     */
    private function isActiveUser($user)
    {
        // ✅ ให้ return true เสมอ - ข้าม status check
        return true;
    }
}

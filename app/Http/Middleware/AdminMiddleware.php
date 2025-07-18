<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request for admin access
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

        // ✅ Super Admin bypass - ให้ super_admin เข้าได้ทุกอย่างเสมอ
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // ✅ ข้าม status check - ให้ admin เข้าได้เสมอ
        // if (!$user->is_active) { // DISABLED

        // ตรวจสอบ role admin
        $adminRoles = ['super_admin', 'admin', 'it_admin'];
        
        if (!in_array($user->role, $adminRoles)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Admin privileges required.'
                ], 403);
            }
            
            return redirect('/dashboard')->with('error', 'Access denied. Admin privileges required.');
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
        
        // Original logic (ถูก disable)
        // $activeStatuses = ['active', '1', 1, true, 'Y', 'yes', 'enabled'];
        // return in_array($user->status, $activeStatuses, true) || 
        //        in_array($user->status, $activeStatuses, false);
    }
}

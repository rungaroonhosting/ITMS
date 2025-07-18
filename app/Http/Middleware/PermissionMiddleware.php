<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request for permission-based access
     */
    public function handle(Request $request, Closure $next, ...$permissions)
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

        // ✅ ข้าม status check - ให้ทุกคนเข้าได้
        // if (!$user->is_active) { // DISABLED

        // ตรวจสอบ permissions
        if (!empty($permissions)) {
            $userRole = $user->role;
            
            // กำหนด role hierarchy
            $roleHierarchy = [
                'super_admin' => ['super_admin', 'admin', 'it_admin', 'manager', 'employee'],
                'admin' => ['admin', 'it_admin', 'manager', 'employee'],
                'it_admin' => ['it_admin', 'manager', 'employee'],
                'manager' => ['manager', 'employee'],
                'employee' => ['employee']
            ];

            $allowedRoles = $roleHierarchy[$userRole] ?? [$userRole];
            
            // ตรวจสอบว่า user มี permission ที่ต้องการหรือไม่
            $hasPermission = false;
            foreach ($permissions as $permission) {
                if (in_array($permission, $allowedRoles)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied. Insufficient permissions.'
                    ], 403);
                }
                
                return redirect('/dashboard')->with('error', 'Access denied. Insufficient permissions.');
            }
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

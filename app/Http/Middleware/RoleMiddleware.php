<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request for role-based access control
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // ตรวจสอบว่า login แล้วหรือยัง
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();

        // ✅ Super Admin bypass - ให้ super_admin เข้าได้ทุกอย่างเสมอ
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // ตรวจสอบสถานะผู้ใช้
        if (isset($user->status) && $user->status !== 'active') {
            Auth::logout();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive'
                ], 403);
            }
            return redirect()->route('login')->with('error', 'บัญชีของคุณถูกระงับการใช้งาน');
        }

        // ตรวจสอบ role ถ้ามีการระบุ
        if (!empty($roles)) {
            $userRole = $user->role;
            
            // กำหนด role hierarchy - role ที่สูงกว่าสามารถเข้าถึงระดับที่ต่ำกว่าได้
            $roleHierarchy = [
                'super_admin' => ['super_admin', 'it_admin', 'manager', 'employee'],
                'it_admin' => ['it_admin', 'manager', 'employee'],
                'manager' => ['manager', 'employee'],
                'employee' => ['employee']
            ];

            $allowedRoles = $roleHierarchy[$userRole] ?? [$userRole];
            
            // ตรวจสอบว่า user มี permission ที่ต้องการหรือไม่
            $hasPermission = false;
            foreach ($roles as $role) {
                if (in_array($role, $allowedRoles)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied. Insufficient permissions.'
                    ], 403);
                }
                
                return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            }
        }

        return $next($request);
    }

    /**
     * Helper methods สำหรับตรวจสอบสิทธิ์
     */
    public static function canViewPasswords($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    public static function canManageEmployees($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'manager']);
    }

    public static function canManageDepartments($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    public static function canExportData($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'manager']);
    }
}

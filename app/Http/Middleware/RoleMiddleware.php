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
                    'message' => 'Unauthorized access - Authentication required'
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
                'super_admin' => ['super_admin', 'it_admin', 'hr', 'manager', 'express', 'employee'],
                'it_admin' => ['it_admin', 'hr', 'manager', 'express', 'employee'],
                'hr' => ['hr', 'manager', 'express', 'employee'],
                'manager' => ['manager', 'employee'],
                'express' => ['express', 'employee'], // Express มีสิทธิ์พิเศษในแผนกบัญชี
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
                        'message' => 'Access denied. Insufficient permissions.',
                        'required_roles' => $roles,
                        'user_role' => $userRole
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

    public static function canViewExpressPasswords($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    public static function canManageEmployees($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    public static function canManageDepartments($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    public static function canExportData($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    public static function canAccessExpressFeatures($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'express']);
    }

    public static function canCreateExpressUsers($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'express']);
    }

    public static function canViewExpressReports($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    public static function isAccountingDepartment($departmentName): bool
    {
        $accountingKeywords = ['บัญชี', 'การเงิน', 'accounting', 'finance'];
        
        foreach ($accountingKeywords as $keyword) {
            if (stripos($departmentName, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * ตรวจสอบสิทธิ์เฉพาะ Express
     */
    public static function canAccessExpress($user, $departmentName = null): bool
    {
        // SuperAdmin และ IT Admin เข้าได้เสมอ
        if (in_array($user->role, ['super_admin', 'it_admin'])) {
            return true;
        }

        // HR เข้าได้
        if ($user->role === 'hr') {
            return true;
        }

        // Express role ต้องเป็นแผนกบัญชี
        if ($user->role === 'express') {
            if ($departmentName) {
                return self::isAccountingDepartment($departmentName);
            }
            return true; // ให้เข้าได้ถ้าไม่ระบุแผนก
        }

        return false;
    }

    /**
     * ตรวจสอบว่าสามารถดูรหัสผ่าน Express ได้หรือไม่
     */
    public static function canViewExpressCredentials($user, $employeeId = null): bool
    {
        // SuperAdmin และ IT Admin เห็นได้เสมอ
        if (in_array($user->role, ['super_admin', 'it_admin'])) {
            return true;
        }

        // HR เห็นได้ แต่รหัสผ่านจะถูกซ่อน
        if ($user->role === 'hr') {
            return true;
        }

        // Express role และ Manager เห็นได้ แต่รหัสผ่านซ่อน
        if (in_array($user->role, ['express', 'manager'])) {
            return true;
        }

        // Employee เห็นได้เฉพาะตัวเอง
        if ($user->role === 'employee' && $employeeId) {
            return $user->id == $employeeId;
        }

        return false;
    }

    /**
     * Get role display name in Thai
     */
    public static function getRoleDisplayName($role): string
    {
        $roleNames = [
            'super_admin' => 'ผู้ดูแลระบบสูงสุด',
            'it_admin' => 'ผู้ดูแลระบบ IT',
            'hr' => 'ฝ่ายบุคคล',
            'manager' => 'ผู้จัดการ',
            'express' => 'Express User',
            'employee' => 'พนักงาน'
        ];

        return $roleNames[$role] ?? 'ไม่ระบุ';
    }

    /**
     * Get role permissions
     */
    public static function getRolePermissions($role): array
    {
        $permissions = [
            'super_admin' => [
                'view_all_employees',
                'create_employees',
                'edit_employees', 
                'delete_employees',
                'view_all_passwords',
                'manage_departments',
                'manage_users',
                'view_reports',
                'export_data',
                'access_express',
                'view_express_passwords',
                'manage_express_users',
                'system_settings'
            ],
            'it_admin' => [
                'view_all_employees',
                'create_employees',
                'edit_employees',
                'view_all_passwords',
                'manage_departments',
                'view_reports',
                'export_data',
                'access_express',
                'view_express_passwords',
                'manage_express_users'
            ],
            'hr' => [
                'view_all_employees',
                'create_employees',
                'edit_employees',
                'view_reports',
                'export_data',
                'access_express'
            ],
            'manager' => [
                'view_department_employees',
                'create_employees',
                'edit_department_employees',
                'view_reports',
                'access_express'
            ],
            'express' => [
                'view_accounting_employees',
                'create_accounting_employees',
                'edit_accounting_employees',
                'access_express',
                'create_express_users'
            ],
            'employee' => [
                'view_own_profile',
                'edit_own_profile'
            ]
        ];

        return $permissions[$role] ?? [];
    }

    /**
     * Check if user has specific permission
     */
    public static function hasPermission($user, $permission): bool
    {
        $userPermissions = self::getRolePermissions($user->role);
        return in_array($permission, $userPermissions);
    }

    /**
     * Middleware สำหรับตรวจสอบ Express permissions
     */
    public static function expressMiddleware(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if (!self::canAccessExpressFeatures($user)) {
            return redirect()->route('dashboard')
                ->with('error', 'ไม่มีสิทธิ์เข้าถึงฟีเจอร์ Express');
        }

        return $next($request);
    }

    /**
     * Get user's accessible departments
     */
    public static function getAccessibleDepartments($user): array
    {
        switch ($user->role) {
            case 'super_admin':
            case 'it_admin':
            case 'hr':
                // เข้าถึงทุกแผนก
                return ['all'];
                
            case 'manager':
                // เข้าถึงแผนกของตัวเอง (ต้องมี department_id ใน user record)
                return isset($user->department_id) ? [$user->department_id] : [];
                
            case 'express':
                // เข้าถึงเฉพาะแผนกบัญชี
                return ['accounting'];
                
            case 'employee':
            default:
                // เข้าถึงแผนกของตัวเองเท่านั้น
                return isset($user->department_id) ? [$user->department_id] : [];
        }
    }
}

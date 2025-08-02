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

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์จัดการสาขา
     */
    public static function canManageBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์ดูข้อมูลสาขา
     */
    public static function canViewBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager', 'express']);
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์แก้ไขสาขา
     */
    public static function canEditBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์ลบสาขา
     */
    public static function canDeleteBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์จัดการพนักงานในสาขา
     */
    public static function canManageBranchEmployees($user, $branchId = null): bool
    {
        if (in_array($user->role, ['super_admin', 'it_admin', 'hr'])) {
            return true;
        }

        // Manager สามารถจัดการพนักงานในสาขาของตัวเองได้
        if ($user->role === 'manager' && $branchId && isset($user->branch_id)) {
            return $user->branch_id == $branchId;
        }

        return false;
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

    /**
     * ✅ Updated: ตรวจสอบแผนกที่รองรับ Express (ใช้ express_enabled แทน hardcode)
     */
    public static function isDepartmentExpressEnabled($departmentId): bool
    {
        try {
            $department = \App\Models\Department::find($departmentId);
            return $department ? (bool) $department->express_enabled : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 🔄 Backward compatibility - แผนกบัญชี
     */
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
     * ✅ Updated: ตรวจสอบสิทธิ์เฉพาะ Express
     */
    public static function canAccessExpress($user, $departmentId = null): bool
    {
        // SuperAdmin และ IT Admin เข้าได้เสมอ
        if (in_array($user->role, ['super_admin', 'it_admin'])) {
            return true;
        }

        // HR เข้าได้
        if ($user->role === 'hr') {
            return true;
        }

        // Express role ต้องเป็นแผนกที่เปิด Express
        if ($user->role === 'express') {
            if ($departmentId) {
                return self::isDepartmentExpressEnabled($departmentId);
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
     * ✅ NEW: ตรวจสอบสิทธิ์การเข้าถึงถังขยะ
     */
    public static function canAccessTrash($user): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์การลบอย่างถาวร
     */
    public static function canForceDelete($user): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์การกู้คืน
     */
    public static function canRestore($user): bool
    {
        return $user->role === 'super_admin';
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
                'manage_branches', // ✅ NEW
                'manage_users',
                'view_reports',
                'export_data',
                'access_express',
                'view_express_passwords',
                'manage_express_users',
                'system_settings',
                'access_trash',
                'force_delete',
                'restore_employees',
                'manage_soft_deletes'
            ],
            'it_admin' => [
                'view_all_employees',
                'create_employees',
                'edit_employees',
                'view_all_passwords',
                'manage_departments',
                'manage_branches', // ✅ NEW
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
                'view_branches', // ✅ NEW
                'manage_branches', // ✅ NEW
                'view_reports',
                'export_data',
                'access_express'
            ],
            'manager' => [
                'view_department_employees',
                'create_employees',
                'edit_department_employees',
                'view_branches', // ✅ NEW
                'manage_branch_employees', // ✅ NEW
                'view_reports',
                'access_express'
            ],
            'express' => [
                'view_accounting_employees',
                'create_accounting_employees',
                'edit_accounting_employees',
                'view_branches', // ✅ NEW
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
     * ✅ NEW: Middleware สำหรับตรวจสอบสิทธิ์ถังขยะ
     */
    public static function trashMiddleware(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if (!self::canAccessTrash($user)) {
            return redirect()->route('dashboard')
                ->with('error', 'ไม่มีสิทธิ์เข้าถึงถังขยะ (เฉพาะ Super Admin)');
        }

        return $next($request);
    }

    /**
     * ✅ NEW: Middleware สำหรับตรวจสอบสิทธิ์สาขา
     */
    public static function branchMiddleware(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if (!self::canManageBranches($user)) {
            return redirect()->route('dashboard')
                ->with('error', 'ไม่มีสิทธิ์เข้าถึงระบบจัดการสาขา');
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
                // เข้าถึงเฉพาะแผนกที่เปิด Express
                return ['express_enabled'];
                
            case 'employee':
            default:
                // เข้าถึงแผนกของตัวเองเท่านั้น
                return isset($user->department_id) ? [$user->department_id] : [];
        }
    }

    /**
     * ✅ NEW: Get user's accessible branches
     */
    public static function getAccessibleBranches($user): array
    {
        switch ($user->role) {
            case 'super_admin':
            case 'it_admin':
            case 'hr':
                // เข้าถึงทุกสาขา
                return ['all'];
                
            case 'manager':
                // เข้าถึงสาขาของตัวเอง
                return isset($user->branch_id) ? [$user->branch_id] : [];
                
            case 'express':
                // เข้าถึงสาขาที่มีแผนก Express enabled
                return ['express_branches'];
                
            case 'employee':
            default:
                // เข้าถึงสาขาของตัวเองเท่านั้น
                return isset($user->branch_id) ? [$user->branch_id] : [];
        }
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์การดู Phone Duplicates
     */
    public static function canViewPhoneDuplicates($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์การจัดการ Bulk Actions
     */
    public static function canPerformBulkActions($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์การส่งออกข้อมูล
     */
    public static function canExportEmployees($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์การดูรายงาน
     */
    public static function canViewReports($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    /**
     * ✅ NEW: Get feature flags based on user role
     */
    public static function getFeatureFlags($user): array
    {
        return [
            'can_view_passwords' => self::canViewPasswords($user),
            'can_view_express_passwords' => self::canViewExpressPasswords($user),
            'can_manage_employees' => self::canManageEmployees($user),
            'can_manage_departments' => self::canManageDepartments($user),
            'can_manage_branches' => self::canManageBranches($user), // ✅ NEW
            'can_view_branches' => self::canViewBranches($user), // ✅ NEW
            'can_edit_branches' => self::canEditBranches($user), // ✅ NEW
            'can_delete_branches' => self::canDeleteBranches($user), // ✅ NEW
            'can_export_data' => self::canExportData($user),
            'can_access_express' => self::canAccessExpressFeatures($user),
            'can_create_express_users' => self::canCreateExpressUsers($user),
            'can_view_express_reports' => self::canViewExpressReports($user),
            'can_access_trash' => self::canAccessTrash($user),
            'can_force_delete' => self::canForceDelete($user),
            'can_restore' => self::canRestore($user),
            'can_view_phone_duplicates' => self::canViewPhoneDuplicates($user),
            'can_perform_bulk_actions' => self::canPerformBulkActions($user),
            'can_export_employees' => self::canExportEmployees($user),
            'can_view_reports' => self::canViewReports($user),
        ];
    }
}

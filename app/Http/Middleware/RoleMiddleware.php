<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * ✅ ENHANCED: Handle an incoming request for role-based access control
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // ✅ STEP 1: Enhanced Authentication Check
        if (!Auth::check()) {
            Log::warning('🚫 Authentication failed - User not logged in', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access - Authentication required',
                    'error_code' => 'NOT_AUTHENTICATED',
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();

        // ✅ STEP 2: Enhanced User Status Check
        if (isset($user->status) && $user->status !== 'active') {
            Log::warning('🚫 Inactive user access attempt', [
                'user_id' => $user->id,
                'user_status' => $user->status,
                'user_role' => $user->role,
                'url' => $request->fullUrl()
            ]);
            
            Auth::logout();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive',
                    'error_code' => 'ACCOUNT_INACTIVE'
                ], 403);
            }
            return redirect()->route('login')->with('error', 'บัญชีของคุณถูกระงับการใช้งาน');
        }

        // ✅ STEP 3: Super Admin Universal Access
        if ($user->role === 'super_admin') {
            Log::info('✅ Super Admin access granted', [
                'user_id' => $user->id,
                'user_name' => $user->full_name_th ?? $user->name,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'roles_required' => $roles
            ]);
            return $next($request);
        }

        // ✅ STEP 4: Role-based Authorization Check
        if (!empty($roles)) {
            $userRole = $user->role;
            
            // ✅ Enhanced role hierarchy with explicit permissions
            $roleHierarchy = [
                'super_admin' => ['super_admin', 'it_admin', 'hr', 'manager', 'express', 'employee'],
                'it_admin' => ['it_admin', 'hr', 'manager', 'express', 'employee'],
                'hr' => ['hr', 'manager', 'express', 'employee'],
                'manager' => ['manager', 'employee'],
                'express' => ['express', 'employee'],
                'employee' => ['employee']
            ];

            $allowedRoles = $roleHierarchy[$userRole] ?? [$userRole];
            
            // ✅ Check if user has required permissions
            $hasPermission = false;
            foreach ($roles as $role) {
                if (in_array($role, $allowedRoles)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                Log::warning('🚫 Role authorization failed', [
                    'user_id' => $user->id,
                    'user_role' => $userRole,
                    'required_roles' => $roles,
                    'allowed_roles' => $allowedRoles,
                    'url' => $request->fullUrl(),
                    'method' => $request->method()
                ]);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied. Insufficient permissions.',
                        'error_code' => 'INSUFFICIENT_PERMISSIONS',
                        'required_roles' => $roles,
                        'user_role' => $userRole
                    ], 403);
                }
                
                return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            }
        }

        // ✅ STEP 5: Log successful access
        Log::info('✅ Role-based access granted', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_roles' => $roles,
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);

        return $next($request);
    }

    /**
     * ✅ ENHANCED: Helper methods for specific permission checks
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
     * ✅ ENHANCED: Branch management permissions
     */
    public static function canManageBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    public static function canViewBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager', 'express']);
    }

    public static function canEditBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    public static function canDeleteBranches($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    /**
     * ✅ CRITICAL: Bulk action permissions
     */
    public static function canPerformBulkActions($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    public static function canBulkUpdateStatus($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    public static function canBulkUpdateDepartment($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    public static function canBulkSendEmail($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    public static function canBulkExport($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    /**
     * ✅ MOST CRITICAL: Permanent delete permissions
     */
    public static function canPermanentDelete($user): bool
    {
        return $user->role === 'super_admin';
    }

    public static function canBulkPermanentDelete($user): bool
    {
        return $user->role === 'super_admin';
    }

    public static function canAccessTrash($user): bool
    {
        return $user->role === 'super_admin';
    }

    public static function canForceDelete($user): bool
    {
        return $user->role === 'super_admin';
    }

    public static function canRestore($user): bool
    {
        return $user->role === 'super_admin';
    }

    public static function canEmptyTrash($user): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * ✅ Branch-specific employee management
     */
    public static function canManageBranchEmployees($user, $branchId = null): bool
    {
        if (in_array($user->role, ['super_admin', 'it_admin', 'hr'])) {
            return true;
        }

        // Manager can manage employees in their own branch
        if ($user->role === 'manager' && $branchId && isset($user->branch_id)) {
            return $user->branch_id == $branchId;
        }

        return false;
    }

    /**
     * ✅ Express system permissions
     */
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
     * ✅ Enhanced department Express enablement check
     */
    public static function isDepartmentExpressEnabled($departmentId): bool
    {
        try {
            $department = \App\Models\Department::find($departmentId);
            return $department ? (bool) $department->express_enabled : false;
        } catch (\Exception $e) {
            Log::error('Error checking department Express enablement: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ Enhanced Express access check
     */
    public static function canAccessExpress($user, $departmentId = null): bool
    {
        // SuperAdmin and IT Admin always have access
        if (in_array($user->role, ['super_admin', 'it_admin'])) {
            return true;
        }

        // HR has access
        if ($user->role === 'hr') {
            return true;
        }

        // Express role must be in Express-enabled department
        if ($user->role === 'express') {
            if ($departmentId) {
                return self::isDepartmentExpressEnabled($departmentId);
            }
            return true; // Allow if no specific department check
        }

        return false;
    }

    /**
     * ✅ Enhanced Express credentials viewing
     */
    public static function canViewExpressCredentials($user, $employeeId = null): bool
    {
        // SuperAdmin and IT Admin see everything
        if (in_array($user->role, ['super_admin', 'it_admin'])) {
            return true;
        }

        // HR can see but passwords may be masked
        if ($user->role === 'hr') {
            return true;
        }

        // Express role and Manager can see but passwords masked
        if (in_array($user->role, ['express', 'manager'])) {
            return true;
        }

        // Employee can see their own only
        if ($user->role === 'employee' && $employeeId) {
            return $user->id == $employeeId;
        }

        return false;
    }

    /**
     * ✅ Photo management permissions
     */
    public static function canManagePhotos($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    public static function canUploadPhotos($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    public static function canDeletePhotos($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin']);
    }

    public static function canViewPhotoReports($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    /**
     * ✅ Export and reporting permissions
     */
    public static function canExportData($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    public static function canExportEmployees($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    public static function canViewReports($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr', 'manager']);
    }

    public static function canViewPhoneDuplicates($user): bool
    {
        return in_array($user->role, ['super_admin', 'it_admin', 'hr']);
    }

    /**
     * ✅ Enhanced role display name
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

        return $roleNames[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }

    /**
     * ✅ Enhanced role permissions mapping
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
                'manage_branches',
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
                'manage_soft_deletes',
                'bulk_permanent_delete', // ✅ Critical permission
                'bulk_actions',
                'photo_management',
                'view_debug_info'
            ],
            'it_admin' => [
                'view_all_employees',
                'create_employees',
                'edit_employees',
                'view_all_passwords',
                'manage_departments',
                'manage_branches',
                'view_reports',
                'export_data',
                'access_express',
                'view_express_passwords',
                'manage_express_users',
                'bulk_actions',
                'photo_management'
            ],
            'hr' => [
                'view_all_employees',
                'create_employees',
                'edit_employees',
                'view_branches',
                'manage_branches',
                'view_reports',
                'export_data',
                'access_express',
                'bulk_actions',
                'photo_upload'
            ],
            'manager' => [
                'view_department_employees',
                'create_employees',
                'edit_department_employees',
                'view_branches',
                'manage_branch_employees',
                'view_reports',
                'access_express',
                'export_data'
            ],
            'express' => [
                'view_accounting_employees',
                'create_accounting_employees',
                'edit_accounting_employees',
                'view_branches',
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
     * ✅ Enhanced permission checking
     */
    public static function hasPermission($user, $permission): bool
    {
        $userPermissions = self::getRolePermissions($user->role);
        return in_array($permission, $userPermissions);
    }

    /**
     * ✅ Get user's accessible departments
     */
    public static function getAccessibleDepartments($user): array
    {
        switch ($user->role) {
            case 'super_admin':
            case 'it_admin':
            case 'hr':
                return ['all'];
                
            case 'manager':
                return isset($user->department_id) ? [$user->department_id] : [];
                
            case 'express':
                return ['express_enabled'];
                
            case 'employee':
            default:
                return isset($user->department_id) ? [$user->department_id] : [];
        }
    }

    /**
     * ✅ Get user's accessible branches
     */
    public static function getAccessibleBranches($user): array
    {
        switch ($user->role) {
            case 'super_admin':
            case 'it_admin':
            case 'hr':
                return ['all'];
                
            case 'manager':
                return isset($user->branch_id) ? [$user->branch_id] : [];
                
            case 'express':
                return ['express_branches'];
                
            case 'employee':
            default:
                return isset($user->branch_id) ? [$user->branch_id] : [];
        }
    }

    /**
     * ✅ Enhanced feature flags for frontend
     */
    public static function getFeatureFlags($user): array
    {
        return [
            'can_view_passwords' => self::canViewPasswords($user),
            'can_view_express_passwords' => self::canViewExpressPasswords($user),
            'can_manage_employees' => self::canManageEmployees($user),
            'can_manage_departments' => self::canManageDepartments($user),
            'can_manage_branches' => self::canManageBranches($user),
            'can_view_branches' => self::canViewBranches($user),
            'can_edit_branches' => self::canEditBranches($user),
            'can_delete_branches' => self::canDeleteBranches($user),
            'can_export_data' => self::canExportData($user),
            'can_access_express' => self::canAccessExpressFeatures($user),
            'can_create_express_users' => self::canCreateExpressUsers($user),
            'can_view_express_reports' => self::canViewExpressReports($user),
            'can_access_trash' => self::canAccessTrash($user),
            'can_force_delete' => self::canForceDelete($user),
            'can_restore' => self::canRestore($user),
            'can_permanent_delete' => self::canPermanentDelete($user),
            'can_bulk_permanent_delete' => self::canBulkPermanentDelete($user), // ✅ Critical flag
            'can_view_phone_duplicates' => self::canViewPhoneDuplicates($user),
            'can_perform_bulk_actions' => self::canPerformBulkActions($user),
            'can_export_employees' => self::canExportEmployees($user),
            'can_view_reports' => self::canViewReports($user),
            'can_manage_photos' => self::canManagePhotos($user),
            'can_upload_photos' => self::canUploadPhotos($user),
            'can_delete_photos' => self::canDeletePhotos($user),
        ];
    }

    /**
     * ✅ Middleware for permanent delete operations
     */
    public static function permanentDeleteMiddleware(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            Log::critical('🚨 Permanent delete attempt without authentication', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'ไม่ได้เข้าสู่ระบบ',
                'error_code' => 'NOT_AUTHENTICATED'
            ], 401);
        }

        if (!self::canBulkPermanentDelete($user)) {
            Log::critical('🚨 Unauthorized permanent delete attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_name' => $user->full_name_th ?? $user->name,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ในการลบถาวร (เฉพาะ Super Admin)',
                'error_code' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }

        return $next($request);
    }

    /**
     * ✅ Enhanced middleware for Express operations
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
     * ✅ Enhanced middleware for trash operations
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
     * ✅ Enhanced middleware for branch operations
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
}

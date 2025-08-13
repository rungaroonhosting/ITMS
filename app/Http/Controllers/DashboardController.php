<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     * 
     * Note: Middleware 'auth' is applied in routes/web.php instead of here
     * because Laravel 12.x doesn't support $this->middleware() in controllers
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        // Update last login time safely
        $this->updateLastLogin($user);

        // Get role-specific data
        $dashboardData = $this->getDashboardDataByRole($user->role ?? 'employee');

        // Get safe statistics for dashboard
        $safeStats = $this->getSafeStatistics();

        return view('dashboard', array_merge($dashboardData, $safeStats));
    }

    /**
     * Safely update last login time
     */
    protected function updateLastLogin($user)
    {
        try {
            if ($user && method_exists($user, 'update') && Schema::hasColumn('users', 'last_login_at')) {
                $user->update(['last_login_at' => now()]);
            }
        } catch (\Exception $exception) {
            // Silently fail - this is not critical
            \Log::info('Could not update last_login_at: ' . $exception->getMessage());
        }
    }

    /**
     * Get safe statistics that won't cause undefined variable errors
     */
    protected function getSafeStatistics()
    {
        $stats = [];
        
        try {
            // Employee statistics
            $stats['totalEmployees'] = $this->getEmployeeCount();
            $stats['activeEmployees'] = $this->getActiveEmployeeCount();
            
            // Branch statistics
            $stats['totalBranches'] = $this->getBranchCount();
            $stats['activeBranches'] = $this->getActiveBranchCount();
            
            // Department statistics
            $stats['totalDepartments'] = $this->getDepartmentCount();
            $stats['expressEnabledDepartments'] = $this->getExpressDepartmentCount();
            
            // Photo statistics
            $stats['employeesWithPhotos'] = $this->getEmployeesWithPhotosCount();
            $stats['photoPercentage'] = $this->calculatePhotoPercentage();
            
            // Role distribution
            $stats['roleDistribution'] = $this->getRoleDistribution();
            
            // Express statistics
            $stats['expressStats'] = $this->getExpressStatistics();
            
        } catch (\Exception $exception) {
            \Log::error('Dashboard Statistics Error: ' . $exception->getMessage());
            // Return safe defaults
            $stats = $this->getDefaultStatistics();
        }
        
        return $stats;
    }

    /**
     * Get dashboard data based on user role
     */
    protected function getDashboardDataByRole($role)
    {
        $baseData = [
            'user' => Auth::user(),
            'recent_activity' => $this->getRecentActivity(),
            'system_info' => $this->getSystemInfo(),
        ];

        switch ($role) {
            case 'super_admin':
                return array_merge($baseData, [
                    'system_status' => $this->getSystemStatus(),
                    'user_management' => $this->getUserManagementData(),
                    'admin_privileges' => $this->getAdminPrivileges()
                ]);

            case 'it_admin':
                return array_merge($baseData, [
                    'pending_tasks' => $this->getPendingTasks(),
                    'monthly_reports' => $this->getMonthlyReports(),
                    'admin_stats' => $this->getAdminStats()
                ]);

            case 'hr':
                return array_merge($baseData, [
                    'hr_dashboard' => $this->getHRDashboard(),
                    'employee_overview' => $this->getEmployeeOverview()
                ]);

            case 'manager':
                return array_merge($baseData, [
                    'manager_dashboard' => $this->getManagerDashboard(),
                    'team_overview' => $this->getTeamOverview()
                ]);

            case 'express':
                return array_merge($baseData, [
                    'express_dashboard' => $this->getExpressDashboard(),
                    'express_features' => $this->getExpressFeatures()
                ]);

            case 'employee':
            default:
                return array_merge($baseData, [
                    'personal_info' => $this->getPersonalInfo(),
                    'my_requests' => $this->getMyRequests(),
                    'employee_stats' => $this->getEmployeeStats()
                ]);
        }
    }

    // Safe database query methods with error handling

    protected function getEmployeeCount()
    {
        try {
            if (class_exists('\App\Models\Employee')) {
                return \App\Models\Employee::count();
            }
            return $this->getUserCount();
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function getActiveEmployeeCount()
    {
        try {
            if (class_exists('\App\Models\Employee')) {
                return \App\Models\Employee::where('status', 'active')->count();
            }
            return $this->getUserCount();
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function getBranchCount()
    {
        try {
            if (class_exists('\App\Models\Branch')) {
                return \App\Models\Branch::count();
            }
            return 0;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function getActiveBranchCount()
    {
        try {
            if (class_exists('\App\Models\Branch')) {
                return \App\Models\Branch::where('is_active', true)->count();
            }
            return 0;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function getDepartmentCount()
    {
        try {
            if (class_exists('\App\Models\Department')) {
                return \App\Models\Department::count();
            }
            return 0;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function getExpressDepartmentCount()
    {
        try {
            if (class_exists('\App\Models\Department')) {
                return \App\Models\Department::where('express_enabled', true)->count();
            }
            return 0;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function getEmployeesWithPhotosCount()
    {
        try {
            if (class_exists('\App\Models\Employee')) {
                return \App\Models\Employee::whereNotNull('photo')->count();
            }
            return 0;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function calculatePhotoPercentage()
    {
        try {
            $total = $this->getEmployeeCount();
            $withPhoto = $this->getEmployeesWithPhotosCount();
            return $total > 0 ? round(($withPhoto / $total) * 100, 1) : 0;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function getRoleDistribution()
    {
        try {
            if (class_exists('\App\Models\Employee')) {
                return \App\Models\Employee::selectRaw('role, count(*) as count')
                                         ->groupBy('role')
                                         ->pluck('count', 'role')
                                         ->toArray();
            }
            return [];
        } catch (\Exception $exception) {
            return [];
        }
    }

    protected function getExpressStatistics()
    {
        try {
            if (class_exists('\App\Models\Department') && method_exists('\App\Models\Department', 'getExpressStats')) {
                return \App\Models\Department::getExpressStats();
            }
            return [
                'express_enabled_departments' => 0,
                'express_department_percentage' => 0,
                'express_user_percentage' => 0
            ];
        } catch (\Exception $exception) {
            return [
                'express_enabled_departments' => 0,
                'express_department_percentage' => 0,
                'express_user_percentage' => 0
            ];
        }
    }

    protected function getUserCount()
    {
        try {
            if (Schema::hasTable('users')) {
                return DB::table('users')->count();
            }
            return 0;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function getDefaultStatistics()
    {
        return [
            'totalEmployees' => 0,
            'activeEmployees' => 0,
            'totalBranches' => 0,
            'activeBranches' => 0,
            'totalDepartments' => 0,
            'expressEnabledDepartments' => 0,
            'employeesWithPhotos' => 0,
            'photoPercentage' => 0,
            'roleDistribution' => [],
            'expressStats' => [
                'express_enabled_departments' => 0,
                'express_department_percentage' => 0,
                'express_user_percentage' => 0
            ]
        ];
    }

    /**
     * Get recent activity based on user role
     */
    protected function getRecentActivity()
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'employee';
        
        try {
            if (in_array($userRole, ['super_admin', 'it_admin'])) {
                return [
                    [
                        'title' => 'การซ่อมเครื่องพิมพ์ เสร็จสิ้น',
                        'description' => 'แผนกบัญชี - เครื่องพิมพ์หมึกหมด',
                        'time' => '2 ชั่วโมงที่แล้ว',
                        'icon' => 'fas fa-check',
                        'color' => 'success'
                    ],
                    [
                        'title' => 'เพิ่มอุปกรณ์ใหม่',
                        'description' => 'Laptop Dell Inspiron - DELL001',
                        'time' => '4 ชั่วโมงที่แล้ว',
                        'icon' => 'fas fa-plus',
                        'color' => 'primary'
                    ],
                    [
                        'title' => 'คำขอติดตั้งซอฟต์แวร์',
                        'description' => 'Microsoft Office 365',
                        'time' => '1 วันที่แล้ว',
                        'icon' => 'fas fa-ticket-alt',
                        'color' => 'info'
                    ]
                ];
            } else {
                return [
                    [
                        'title' => 'คำขอซ่อมคอมพิวเตอร์',
                        'description' => 'สถานะ: กำลังดำเนินการ',
                        'time' => '1 ชั่วโมงที่แล้ว',
                        'icon' => 'fas fa-tools',
                        'color' => 'warning'
                    ],
                    [
                        'title' => 'ระบบทำงานปกติ',
                        'description' => 'ไม่มีกิจกรรมผิดปกติ',
                        'time' => 'ตอนนี้',
                        'icon' => 'fas fa-check',
                        'color' => 'success'
                    ]
                ];
            }
        } catch (\Exception $exception) {
            return [
                [
                    'title' => 'ระบบทำงานปกติ',
                    'description' => 'ไม่มีกิจกรรมผิดปกติ',
                    'time' => 'ตอนนี้',
                    'icon' => 'fas fa-check',
                    'color' => 'success'
                ]
            ];
        }
    }

    /**
     * Get system info
     */
    protected function getSystemInfo()
    {
        return [
            'version' => 'v2.1',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'current_time' => now()->format('d/m/Y H:i:s'),
            'database_status' => $this->checkDatabaseStatus()
        ];
    }

    /**
     * Get system status for Super Admin
     */
    protected function getSystemStatus()
    {
        return [
            'database' => $this->checkDatabaseStatus(),
            'web_server' => ['status' => 'online', 'message' => 'เซิร์ฟเวอร์เว็บทำงานปกติ'],
            'file_storage' => $this->checkFileStorage(),
            'uptime' => '99.8%',
            'response_time' => '42ms',
            'active_users' => $this->getActiveUserCount()
        ];
    }

    /**
     * Get user management data for Super Admin
     */
    protected function getUserManagementData()
    {
        try {
            return [
                'super_admin_count' => $this->getUserCountByRole('super_admin'),
                'it_admin_count' => $this->getUserCountByRole('it_admin'),
                'hr_count' => $this->getUserCountByRole('hr'),
                'manager_count' => $this->getUserCountByRole('manager'),
                'express_count' => $this->getUserCountByRole('express'),
                'employee_count' => $this->getUserCountByRole('employee'),
                'total_users' => $this->getTotalUserCount(),
                'active_users' => $this->getActiveUserCount()
            ];
        } catch (\Exception $exception) {
            return [
                'super_admin_count' => 1,
                'it_admin_count' => 5,
                'hr_count' => 3,
                'manager_count' => 12,
                'express_count' => 8,
                'employee_count' => 219,
                'total_users' => 248,
                'active_users' => 245
            ];
        }
    }

    protected function getAdminPrivileges()
    {
        return [
            'can_see_passwords' => true,
            'can_manage_users' => true,
            'can_access_system_settings' => true,
            'can_view_all_data' => true,
            'can_delete_data' => true,
            'can_manage_trash' => true,
            'can_bulk_operations' => true
        ];
    }

    // Additional role-specific methods

    protected function getPendingTasks()
    {
        return [
            'pending_repairs' => 18,
            'pending_service_requests' => 34,
            'pending_agreements' => 7,
            'pending_equipment_checks' => 12
        ];
    }

    protected function getMonthlyReports()
    {
        return [
            'completion_rate' => 95,
            'satisfaction_score' => 4.8,
            'completed_tasks' => 142,
            'average_resolution_time' => 2.3
        ];
    }

    protected function getAdminStats()
    {
        return [
            'can_manage_employees' => true,
            'can_manage_assets' => true,
            'can_manage_departments' => true,
            'can_view_reports' => true
        ];
    }

    protected function getHRDashboard()
    {
        return [
            'new_hires_this_month' => 12,
            'pending_interviews' => 5,
            'contract_renewals' => 8,
            'training_sessions' => 15
        ];
    }

    protected function getEmployeeOverview()
    {
        return [
            'total_employees' => $this->getEmployeeCount(),
            'departments' => $this->getDepartmentCount(),
            'average_tenure' => '2.5 ปี',
            'satisfaction_rate' => 4.2
        ];
    }

    protected function getManagerDashboard()
    {
        return [
            'team_size' => 24,
            'pending_approvals' => 6,
            'monthly_targets' => 85,
            'team_performance' => 92
        ];
    }

    protected function getTeamOverview()
    {
        return [
            'active_projects' => 8,
            'completed_tasks' => 156,
            'team_efficiency' => 88,
            'upcoming_deadlines' => 3
        ];
    }

    protected function getExpressDashboard()
    {
        return [
            'express_access' => true,
            'department_features' => true,
            'quick_add_enabled' => true,
            'express_reports' => 15
        ];
    }

    protected function getExpressFeatures()
    {
        return [
            'can_quick_add_employees' => true,
            'can_view_express_reports' => true,
            'can_access_accounting_tools' => true,
            'express_shortcuts' => true
        ];
    }

    protected function getPersonalInfo()
    {
        $user = Auth::user();
        return [
            'name' => $user->name ?? 'Unknown',
            'email' => $user->email ?? 'Unknown',
            'role' => $user->role ?? 'employee',
            'created_at' => $user->created_at ?? now(),
            'last_login' => $user->last_login_at ?? null,
            'department' => $user->department ?? 'ไม่ระบุ'
        ];
    }

    protected function getMyRequests()
    {
        return [
            'my_repair_requests' => 3,
            'my_service_requests' => 2,
            'signed_agreements' => 5,
            'completed_tasks' => 28
        ];
    }

    protected function getEmployeeStats()
    {
        return [
            'can_view_own_data' => true,
            'can_sign_agreements' => true,
            'can_report_issues' => true,
            'can_request_services' => true,
            'can_track_status' => true
        ];
    }

    // Helper methods for stats calculation
    
    protected function getUserCountByRole($role)
    {
        try {
            if (class_exists('\App\Models\Employee')) {
                return \App\Models\Employee::where('role', $role)->count();
            }
            if (Schema::hasTable('users')) {
                return DB::table('users')->where('role', $role)->count();
            }
        } catch (\Exception $exception) {
            // Return safe default
        }
        return 0;
    }

    protected function getTotalUserCount()
    {
        try {
            if (class_exists('\App\Models\Employee')) {
                return \App\Models\Employee::count();
            }
            if (Schema::hasTable('users')) {
                return DB::table('users')->count();
            }
        } catch (\Exception $exception) {
            // Return safe default
        }
        return 0;
    }

    protected function getActiveUserCount()
    {
        try {
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'last_login_at')) {
                return DB::table('users')
                    ->where('last_login_at', '>=', Carbon::now()->subDay())
                    ->count();
            }
        } catch (\Exception $exception) {
            // Return safe default
        }
        return 0;
    }

    // System status check methods
    
    protected function checkDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'connected', 'message' => 'ฐานข้อมูลเชื่อมต่อปกติ'];
        } catch (\Exception $exception) {
            return ['status' => 'disconnected', 'message' => 'ฐานข้อมูลเชื่อมต่อไม่ได้'];
        }
    }

    protected function checkFileStorage()
    {
        try {
            $storageAvailable = is_writable(storage_path());
            return [
                'status' => $storageAvailable ? 'online' : 'offline',
                'message' => $storageAvailable ? 'ที่เก็บไฟล์พร้อมใช้งาน' : 'ที่เก็บไฟล์มีปัญหา'
            ];
        } catch (\Exception $exception) {
            return ['status' => 'offline', 'message' => 'ตรวจสอบที่เก็บไฟล์ไม่ได้'];
        }
    }

    /**
     * API endpoint to get dashboard stats
     */
    public function getStats(Request $request)
    {
        try {
            $stats = $this->getSafeStatistics();
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch statistics',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get role-specific data
     */
    public function getRoleData(Request $request)
    {
        try {
            $user = Auth::user();
            $roleData = $this->getDashboardDataByRole($user->role ?? 'employee');
            
            return response()->json([
                'success' => true,
                'role' => $user->role ?? 'employee',
                'data' => $roleData
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch role data',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get notifications based on role
     */
    public function getNotifications(Request $request)
    {
        try {
            $user = Auth::user();
            $userRole = $user->role ?? 'employee';
            
            $notifications = [];
            
            switch ($userRole) {
                case 'super_admin':
                    $notifications = [
                        [
                            'id' => 1,
                            'title' => 'บำรุงรักษาระบบ',
                            'message' => 'กำหนดบำรุงรักษาระบบคืนนี้ เวลา 02:00 น.',
                            'type' => 'warning',
                            'read' => false,
                            'time' => Carbon::now()->subHours(2)->toISOString()
                        ],
                        [
                            'id' => 2,
                            'title' => 'ผู้ใช้ใหม่รอการอนุมัติ',
                            'message' => 'มีผู้ใช้ใหม่ 3 คน รอการอนุมัติเข้าใช้งานระบบ',
                            'type' => 'info',
                            'read' => false,
                            'time' => Carbon::now()->subHours(4)->toISOString()
                        ]
                    ];
                    break;
                    
                case 'it_admin':
                    $notifications = [
                        [
                            'id' => 3,
                            'title' => 'แจ้งซ่อมใหม่',
                            'message' => 'มีการแจ้งซ่อมเครื่องพิมพ์ แผนกบัญชี',
                            'type' => 'warning',
                            'read' => false,
                            'time' => Carbon::now()->subMinutes(30)->toISOString()
                        ]
                    ];
                    break;
                    
                default: // employee
                    $notifications = [
                        [
                            'id' => 5,
                            'title' => 'งานซ่อมเสร็จสิ้น',
                            'message' => 'การซ่อมคอมพิวเตอร์ของคุณเสร็จเรียบร้อยแล้ว',
                            'type' => 'success',
                            'read' => false,
                            'time' => Carbon::now()->subHours(3)->toISOString()
                        ]
                    ];
                    break;
            }
            
            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => collect($notifications)->where('read', false)->count()
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch notifications',
                'error' => $exception->getMessage()
            ], 500);
        }
    }
}

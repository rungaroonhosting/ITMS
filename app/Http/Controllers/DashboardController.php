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
        
        // Update last login time
        if ($user && method_exists($user, 'update')) {
            try {
                $user->update([
                    'last_login_at' => now()
                ]);
            } catch (\Exception $e) {
                // Ignore if column doesn't exist
            }
        }

        // Get role-specific data
        $dashboardData = $this->getDashboardDataByRole($user->role ?? 'employee');

        return view('dashboard', $dashboardData);
    }

    /**
     * Get dashboard data based on user role
     */
    protected function getDashboardDataByRole($role)
    {
        $baseData = [
            'user' => Auth::user(),
            'stats' => $this->getBasicStats(),
            'recent_activity' => $this->getRecentActivity(),
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

            case 'employee':
            default:
                return array_merge($baseData, [
                    'personal_info' => $this->getPersonalInfo(),
                    'my_requests' => $this->getMyRequests(),
                    'employee_stats' => $this->getEmployeeStats()
                ]);
        }
    }

    /**
     * Get basic statistics for dashboard cards
     */
    protected function getBasicStats()
    {
        try {
            return [
                'employees' => [
                    'count' => $this->getEmployeeCount(),
                    'change' => 8,
                    'trend' => 'up'
                ],
                'assets' => [
                    'count' => $this->getAssetCount(),
                    'change' => 15,
                    'trend' => 'up'
                ],
                'repairs' => [
                    'count' => $this->getRepairCount(),
                    'change' => -3,
                    'trend' => 'down'
                ],
                'service_requests' => [
                    'count' => $this->getServiceRequestCount(),
                    'change' => 22,
                    'trend' => 'up'
                ]
            ];
        } catch (\Exception $e) {
            return [
                'employees' => ['count' => 248, 'change' => 8, 'trend' => 'up'],
                'assets' => ['count' => 456, 'change' => 15, 'trend' => 'up'],
                'repairs' => ['count' => 18, 'change' => -3, 'trend' => 'down'],
                'service_requests' => ['count' => 34, 'change' => 22, 'trend' => 'up']
            ];
        }
    }

    /**
     * Get recent activity based on user role
     */
    protected function getRecentActivity()
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'employee';
        
        try {
            if ($userRole === 'super_admin' || $userRole === 'it_admin') {
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
                    ],
                    [
                        'title' => 'ข้อตกลง IT ใหม่',
                        'description' => 'นายสมชาย ใจดี - แผนกขาย',
                        'time' => '2 วันที่แล้ว',
                        'icon' => 'fas fa-file-signature',
                        'color' => 'warning'
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
                        'title' => 'ข้อตกลง IT ลงนามแล้ว',
                        'description' => 'ข้อตกลงการใช้งานระบบ',
                        'time' => '3 วันที่แล้ว',
                        'icon' => 'fas fa-file-signature',
                        'color' => 'success'
                    ],
                    [
                        'title' => 'คำขอใช้ซอฟต์แวร์',
                        'description' => 'Adobe Photoshop - รออนุมัติ',
                        'time' => '5 วันที่แล้ว',
                        'icon' => 'fas fa-ticket-alt',
                        'color' => 'info'
                    ]
                ];
            }
        } catch (\Exception $e) {
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
     * Get system status for Super Admin
     */
    protected function getSystemStatus()
    {
        return [
            'database' => $this->checkDatabaseStatus(),
            'web_server' => ['status' => 'online', 'message' => 'เซิร์ฟเวอร์เว็บทำงานปกติ'],
            'email_server' => ['status' => 'maintenance', 'message' => 'อีเมลเซิร์ฟเวอร์อยู่ระหว่างบำรุงรักษา'],
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
                'employee_count' => $this->getUserCountByRole('employee'),
                'total_users' => $this->getTotalUserCount(),
                'active_users' => $this->getActiveUserCount()
            ];
        } catch (\Exception $e) {
            return [
                'super_admin_count' => 1,
                'it_admin_count' => 5,
                'employee_count' => 242,
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
            'can_delete_data' => true
        ];
    }

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
            'can_see_passwords' => true,
            'can_manage_employees' => true,
            'can_manage_assets' => true,
            'can_manage_agreements' => true,
            'can_view_reports' => true
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
    
    protected function getEmployeeCount()
    {
        try {
            if (Schema::hasTable('users')) {
                return DB::table('users')->count();
            }
        } catch (\Exception $e) {
            // Ignore error
        }
        return 248;
    }

    protected function getAssetCount()
    {
        try {
            if (Schema::hasTable('assets')) {
                return DB::table('assets')->count();
            }
        } catch (\Exception $e) {
            // Ignore error
        }
        return 456;
    }

    protected function getRepairCount()
    {
        try {
            if (Schema::hasTable('repair_requests')) {
                return DB::table('repair_requests')->where('status', '!=', 'completed')->count();
            }
        } catch (\Exception $e) {
            // Ignore error
        }
        return 18;
    }

    protected function getServiceRequestCount()
    {
        try {
            if (Schema::hasTable('service_requests')) {
                return DB::table('service_requests')->where('status', '!=', 'completed')->count();
            }
        } catch (\Exception $e) {
            // Ignore error
        }
        return 34;
    }

    protected function getUserCountByRole($role)
    {
        try {
            if (Schema::hasTable('users')) {
                return DB::table('users')->where('role', $role)->count();
            }
        } catch (\Exception $e) {
            // Ignore error
        }
        return 0;
    }

    protected function getTotalUserCount()
    {
        try {
            if (Schema::hasTable('users')) {
                return DB::table('users')->count();
            }
        } catch (\Exception $e) {
            // Ignore error
        }
        return 248;
    }

    protected function getActiveUserCount()
    {
        try {
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'last_login_at')) {
                return DB::table('users')
                    ->where('last_login_at', '>=', Carbon::now()->subDay())
                    ->count();
            }
        } catch (\Exception $e) {
            // Ignore error
        }
        return 156;
    }

    // System status check methods
    
    protected function checkDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'online', 'message' => 'ฐานข้อมูลเชื่อมต่อปกติ'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'ฐานข้อมูลเชื่อมต่อไม่ได้'];
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
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'ตรวจสอบที่เก็บไฟล์ไม่ได้'];
        }
    }

    /**
     * API endpoint to get dashboard stats
     */
    public function getStats(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->getBasicStats()
        ]);
    }

    /**
     * API endpoint to get role-specific data
     */
    public function getRoleData(Request $request)
    {
        $user = Auth::user();
        $roleData = $this->getDashboardDataByRole($user->role ?? 'employee');
        
        return response()->json([
            'success' => true,
            'role' => $user->role ?? 'employee',
            'data' => $roleData
        ]);
    }

    /**
     * API endpoint to get notifications based on role
     */
    public function getNotifications(Request $request)
    {
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
                    ],
                    [
                        'id' => 4,
                        'title' => 'คำขอบริการใหม่',
                        'message' => 'คำขอติดตั้งซอฟต์แวร์ Adobe Creative Suite',
                        'type' => 'info',
                        'read' => false,
                        'time' => Carbon::now()->subHours(1)->toISOString()
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
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Employee;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 🔥 Authorization Gates สำหรับ Employee Management

        // Super Admin Gates
        Gate::define('manage-trash', function ($user) {
            return $user->role === 'super_admin';
        });

        Gate::define('view-all-passwords', function ($user) {
            return $user->role === 'super_admin';
        });

        Gate::define('manage-system-settings', function ($user) {
            return $user->role === 'super_admin';
        });

        Gate::define('force-delete-employees', function ($user) {
            return $user->role === 'super_admin';
        });

        // Admin Gates (Super Admin + IT Admin)
        Gate::define('manage-employees', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin']);
        });

        Gate::define('view-express-passwords', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin']);
        });

        Gate::define('bulk-actions', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin']);
        });

        Gate::define('export-data', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin', 'manager']);
        });

        // Manager Gates
        Gate::define('view-employee-details', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin', 'manager', 'hr']);
        });

        Gate::define('edit-employee-profile', function ($user, Employee $employee) {
            // Super admin และ IT admin สามารถแก้ไขได้ทุกคน
            if (in_array($user->role, ['super_admin', 'it_admin'])) {
                return true;
            }
            
            // HR สามารถแก้ไขได้ทุกคน ยกเว้น super admin
            if ($user->role === 'hr' && $employee->role !== 'super_admin') {
                return true;
            }
            
            // Manager สามารถแก้ไขพนักงานในแผนกตัวเองได้
            if ($user->role === 'manager' && $user->department_id === $employee->department_id) {
                return true;
            }
            
            // พนักงานสามารถแก้ไขข้อมูลตัวเองได้ (บางส่วน)
            if ($user->id === $employee->id) {
                return true;
            }
            
            return false;
        });

        // Express System Gates
        Gate::define('use-express-system', function ($user) {
            return !empty($user->express_username);
        });

        Gate::define('generate-express-credentials', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin']);
        });

        // Department Gates
        Gate::define('manage-departments', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin']);
        });

        // Reporting Gates
        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin', 'manager', 'hr']);
        });

        Gate::define('view-analytics', function ($user) {
            return in_array($user->role, ['super_admin', 'it_admin', 'manager']);
        });

        // 🔥 Special Gates สำหรับ Role-based Features

        // สามารถดู sensitive data (passwords, etc.)
        Gate::define('view-sensitive-data', function ($user, Employee $employee) {
            // Super admin ดูได้ทั้งหมด
            if ($user->role === 'super_admin') {
                return true;
            }
            
            // IT admin ดูได้เฉพาะ non-super admin
            if ($user->role === 'it_admin' && $employee->role !== 'super_admin') {
                return true;
            }
            
            // ดูข้อมูลตัวเองได้
            if ($user->id === $employee->id) {
                return true;
            }
            
            return false;
        });

        // สามารถเปลี่ยนสถานะ employee ได้
        Gate::define('change-employee-status', function ($user, Employee $employee) {
            // Super admin เปลี่ยนได้ทั้งหมด
            if ($user->role === 'super_admin') {
                return true;
            }
            
            // IT admin เปลี่ยนได้ ยกเว้น super admin
            if ($user->role === 'it_admin' && $employee->role !== 'super_admin') {
                return true;
            }
            
            // HR เปลี่ยนได้ ยกเว้น admin roles
            if ($user->role === 'hr' && !in_array($employee->role, ['super_admin', 'it_admin'])) {
                return true;
            }
            
            return false;
        });

        // สามารถลบ employee ได้
        Gate::define('delete-employee', function ($user, Employee $employee) {
            // เฉพาะ Super admin เท่านั้นที่ลบได้
            if ($user->role === 'super_admin') {
                return true;
            }
            
            // IT admin สามารถ soft delete ได้ ยกเว้น super admin
            if ($user->role === 'it_admin' && $employee->role !== 'super_admin') {
                return true;
            }
            
            return false;
        });

        // 🔥 Dynamic Gates สำหรับ Department-based permissions
        Gate::define('access-department-data', function ($user, $departmentId = null) {
            // Super admin และ IT admin เข้าถึงได้ทั้งหมด
            if (in_array($user->role, ['super_admin', 'it_admin'])) {
                return true;
            }
            
            // HR เข้าถึงได้ทั้งหมด
            if ($user->role === 'hr') {
                return true;
            }
            
            // Manager เข้าถึงได้เฉพาะแผนกตัวเอง
            if ($user->role === 'manager') {
                return $departmentId ? ($user->department_id == $departmentId) : true;
            }
            
            // Employee เข้าถึงได้เฉพาะแผนกตัวเอง (อ่านอย่างเดียว)
            if ($user->role === 'employee') {
                return $departmentId ? ($user->department_id == $departmentId) : false;
            }
            
            return false;
        });
    }
}
<?php

namespace App\Helpers;

use App\Models\Employee;
use Illuminate\Support\Str;

class EmployeeHelper
{
    /**
     * Get list of departments
     */
    public static function getDepartments()
    {
        return [
            'แผนกบริหาร',
            'แผนกการเงิน', 
            'แผนกบัญชี',
            'แผนกบัญชีและการเงิน',
            'แผนกขาย',
            'แผนกการตลาด',
            'แผนกผลิต',
            'แผนกควบคุมคุณภาพ',
            'แผนกจัดซื้อ',
            'แผนกทรัพยากรบุคคล',
            'แผนกเทคโนโลยีสารสนเทศ',
            'แผนกกฎหมาย',
            'แผนกวิจัยและพัฒนา'
        ];
    }

    /**
     * Check if department is accounting related
     */
    public static function isAccountingDepartment($department)
    {
        $accountingDepartments = [
            'แผนกบัญชี',
            'แผนกบัญชีและการเงิน',
            'แผนกการเงิน'
        ];
        
        return in_array($department, $accountingDepartments);
    }

    /**
     * Generate Express Username from English name
     */
    public static function generateExpressUsername($englishName)
    {
        if (empty($englishName)) {
            return '';
        }
        
        // Remove spaces and special characters, keep only letters
        $clean = preg_replace('/[^a-zA-Z]/', '', $englishName);
        $username = strtolower(substr($clean, 0, 7));
        
        // Ensure minimum length
        if (strlen($username) < 3) {
            $username = $username . Str::random(7 - strlen($username));
        }
        
        // Pad to 7 characters if needed
        if (strlen($username) < 7) {
            $username = str_pad($username, 7, 'x');
        }
        
        // Check for uniqueness
        $originalUsername = $username;
        $counter = 1;
        while (Employee::where('express_username', $username)->exists()) {
            $username = substr($originalUsername, 0, 5) . sprintf('%02d', $counter);
            $counter++;
            
            // Prevent infinite loop
            if ($counter > 99) {
                $username = Str::random(7);
                break;
            }
        }
        
        return $username;
    }

    /**
     * Generate Express Password (4 characters with at least one number)
     */
    public static function generateExpressPassword()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';
        
        // Ensure at least one number
        $password .= rand(0, 9);
        
        // Fill remaining 3 characters
        for ($i = 0; $i < 3; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Generate random employee ID
     */
    public static function generateEmployeeId($prefix = 'EMP')
    {
        do {
            $number = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $employeeId = $prefix . $number;
        } while (Employee::where('employee_id', $employeeId)->exists());
        
        return $employeeId;
    }

    /**
     * Get employee statistics
     */
    public static function getEmployeeStats()
    {
        $employees = Employee::withoutTrashed()->get();
        
        return [
            'total' => $employees->count(),
            'active' => $employees->where('status', 'active')->count(),
            'inactive' => $employees->where('status', 'inactive')->count(),
            'express_users' => $employees->whereNotNull('express_username')->count(),
            'departments' => $employees->groupBy('department')->map->count(),
            'trashed' => Employee::onlyTrashed()->count(),
        ];
    }

    /**
     * Get role permissions
     */
    public static function getRolePermissions($role)
    {
        $permissions = [
            'super_admin' => [
                'view_all_employees' => true,
                'create_employee' => true,
                'edit_employee' => true,
                'delete_employee' => true,
                'view_password' => true,
                'manage_trash' => true,
                'bulk_actions' => true,
                'export_data' => true,
                'reset_password' => true,
                'manage_express' => true,
            ],
            'it_admin' => [
                'view_all_employees' => true,
                'create_employee' => true,
                'edit_employee' => true,
                'delete_employee' => true,
                'view_password' => false,
                'manage_trash' => false,
                'bulk_actions' => true,
                'export_data' => true,
                'reset_password' => true,
                'manage_express' => true,
            ],
            'hr' => [
                'view_all_employees' => true,
                'create_employee' => true,
                'edit_employee' => true,
                'delete_employee' => false,
                'view_password' => false,
                'manage_trash' => false,
                'bulk_actions' => true,
                'export_data' => true,
                'reset_password' => false,
                'manage_express' => false,
            ],
            'express' => [
                'view_all_employees' => false,
                'create_employee' => true,
                'edit_employee' => true,
                'delete_employee' => false,
                'view_password' => false,
                'manage_trash' => false,
                'bulk_actions' => false,
                'export_data' => false,
                'reset_password' => false,
                'manage_express' => true,
            ],
            'employee' => [
                'view_all_employees' => false,
                'create_employee' => false,
                'edit_employee' => false,
                'delete_employee' => false,
                'view_password' => false,
                'manage_trash' => false,
                'bulk_actions' => false,
                'export_data' => false,
                'reset_password' => false,
                'manage_express' => false,
            ],
        ];

        return $permissions[$role] ?? $permissions['employee'];
    }

    /**
     * Check if user has permission
     */
    public static function hasPermission($user, $permission)
    {
        $rolePermissions = self::getRolePermissions($user->role);
        return $rolePermissions[$permission] ?? false;
    }

    /**
     * Format salary for display
     */
    public static function formatSalary($salary)
    {
        if (empty($salary)) {
            return 'ไม่ระบุ';
        }
        
        return number_format($salary, 2) . ' บาท';
    }

    /**
     * Get status badge color
     */
    public static function getStatusBadgeColor($status)
    {
        return $status === 'active' ? 'success' : 'secondary';
    }

    /**
     * Get years of service
     */
    public static function getYearsOfService($hireDate)
    {
        if (empty($hireDate)) {
            return 0;
        }
        
        return $hireDate->diffInYears(now());
    }

    /**
     * Export employee data to array
     */
    public static function exportEmployeeData($employee, $includePassword = false)
    {
        $data = [
            'employee_id' => $employee->employee_id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'english_name' => $employee->english_name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'department' => $employee->department,
            'position' => $employee->position,
            'hire_date' => $employee->hire_date ? $employee->hire_date->format('Y-m-d') : null,
            'salary' => $employee->salary,
            'status' => $employee->status,
            'created_at' => $employee->created_at ? $employee->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $employee->updated_at ? $employee->updated_at->format('Y-m-d H:i:s') : null,
        ];

        if ($includePassword) {
            $data['password'] = '[Encrypted]';
            $data['express_username'] = $employee->express_username;
            $data['express_password'] = $employee->express_password;
        }

        return $data;
    }

    /**
     * Validate Express credentials
     */
    public static function validateExpressCredentials($username, $password)
    {
        $errors = [];

        // Validate username
        if (!empty($username)) {
            if (strlen($username) !== 7) {
                $errors['express_username'] = 'Express Username ต้องมี 7 ตัวอักษร';
            }
            
            if (!preg_match('/^[a-z0-9]+$/', $username)) {
                $errors['express_username'] = 'Express Username ต้องเป็นตัวอักษรภาษาอังกฤษตัวเล็กและตัวเลขเท่านั้น';
            }
        }

        // Validate password
        if (!empty($password)) {
            if (strlen($password) !== 4) {
                $errors['express_password'] = 'Express Password ต้องมี 4 ตัวอักษร';
            }
            
            if (!preg_match('/^[a-z0-9]+$/', $password)) {
                $errors['express_password'] = 'Express Password ต้องเป็นตัวอักษรภาษาอังกฤษตัวเล็กและตัวเลขเท่านั้น';
            }
            
            if (!preg_match('/\d/', $password)) {
                $errors['express_password'] = 'Express Password ต้องมีตัวเลขอย่างน้อย 1 ตัว';
            }
        }

        return $errors;
    }

    /**
     * Search employees
     */
    public static function searchEmployees($query, $filters = [])
    {
        $employees = Employee::query();

        // Basic search
        if (!empty($query)) {
            $employees->where(function ($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('employee_id', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('department', 'LIKE', "%{$query}%")
                  ->orWhere('position', 'LIKE', "%{$query}%")
                  ->orWhere('express_username', 'LIKE', "%{$query}%");
            });
        }

        // Apply filters
        if (!empty($filters['department'])) {
            $employees->where('department', $filters['department']);
        }

        if (!empty($filters['status'])) {
            $employees->where('status', $filters['status']);
        }

        if (!empty($filters['express_only'])) {
            $employees->whereNotNull('express_username');
        }

        return $employees;
    }

    /**
     * Generate random password
     */
    public static function generateRandomPassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }
}
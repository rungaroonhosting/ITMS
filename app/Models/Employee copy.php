<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code',
        'keycard_id',
        'first_name_th',
        'last_name_th',
        'first_name_en',
        'last_name_en',
        'nickname',
        'phone',
        'email',
        'login_email',
        'username',
        'password',
        'computer_password',
        'email_password',
        'copier_code',
        'express_username',
        'express_code',
        'department_id',
        'position',
        'role',
        'status',
        'can_print_color',
        'can_use_vpn',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'computer_password',
        'email_password',
        'express_code',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'can_print_color' => 'boolean',
        'can_use_vpn' => 'boolean',
    ];

    /**
     * Get the department that the employee belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the employee's full Thai name.
     */
    public function getFullNameThAttribute()
    {
        $firstName = $this->first_name_th ?? '';
        $lastName = $this->last_name_th ?? '';
        
        return trim($firstName . ' ' . $lastName) ?: '-';
    }

    /**
     * Get the employee's full English name.
     */
    public function getFullNameEnAttribute()
    {
        $firstName = $this->first_name_en ?? '';
        $lastName = $this->last_name_en ?? '';
        
        return trim($firstName . ' ' . $lastName) ?: '-';
    }

    /**
     * Check if user can view passwords (Super Admin/IT Admin only).
     */
    public function canViewPasswords()
    {
        return in_array($this->role ?? '', ['super_admin', 'it_admin']);
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin()
    {
        return ($this->role ?? '') === 'super_admin';
    }

    /**
     * Check if user is IT Admin.
     */
    public function isItAdmin()
    {
        return ($this->role ?? '') === 'it_admin';
    }

    /**
     * Check if user is Express role.
     */
    public function isExpress()
    {
        return ($this->role ?? '') === 'express';
    }

    /**
     * Get role display name.
     */
    public function getRoleDisplayAttribute()
    {
        $roles = [
            'employee' => 'พนักงานทั่วไป',
            'hr' => 'ฝ่ายบุคคล',
            'manager' => 'ผู้จัดการ',
            'it_admin' => 'IT Admin',
            'super_admin' => 'Super Admin',
            'express' => 'Express',
        ];

        return $roles[$this->role ?? ''] ?? ($this->role ?? 'ไม่ระบุ');
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'active' => 'ใช้งาน',
            'inactive' => 'ไม่ใช้งาน',
        ];

        return $statuses[$this->status ?? ''] ?? ($this->status ?? 'ไม่ระบุ');
    }

    /**
     * Get department name.
     */
    public function getDepartmentNameAttribute()
    {
        // Handle different cases safely
        if ($this->department && is_object($this->department) && isset($this->department->name)) {
            return $this->department->name;
        }
        
        // Fallback: try to get department by ID
        if ($this->department_id) {
            try {
                $dept = Department::find($this->department_id);
                return $dept ? $dept->name : $this->getDepartmentNameFallback();
            } catch (\Exception $e) {
                return $this->getDepartmentNameFallback();
            }
        }
        
        return '-';
    }

    /**
     * Get department name fallback (static list).
     */
    private function getDepartmentNameFallback()
    {
        $departments = [
            '1' => 'บัญชี',
            '2' => 'IT', 
            '3' => 'ฝ่ายขาย',
            '4' => 'การตลาด',
            '5' => 'บุคคล',
            '6' => 'ผลิต',
            '7' => 'คลังสินค้า',
            '8' => 'บริหาร'
        ];
        
        return $departments[$this->department_id] ?? '-';
    }

    /**
     * Scope to get active employees only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get employees by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to get employees by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get available roles.
     */
    public static function getRoles()
    {
        return [
            'employee' => 'พนักงานทั่วไป',
            'hr' => 'ฝ่ายบุคคล',
            'manager' => 'ผู้จัดการ',
            'it_admin' => 'IT Admin',
            'super_admin' => 'Super Admin',
            'express' => 'Express',
        ];
    }

    /**
     * Get available statuses.
     */
    public static function getStatuses()
    {
        return [
            'active' => 'ใช้งาน',
            'inactive' => 'ไม่ใช้งาน',
        ];
    }

    /**
     * Get available departments.
     */
    public static function getDepartments()
    {
        try {
            return Department::active()->pluck('name', 'id');
        } catch (\Exception $e) {
            // Fallback if Department model doesn't exist
            return collect([
                '1' => 'บัญชี',
                '2' => 'IT',
                '3' => 'ฝ่ายขาย',
                '4' => 'การตลาด',
                '5' => 'บุคคล',
                '6' => 'ผลิต',
                '7' => 'คลังสินค้า',
                '8' => 'บริหาร',
            ]);
        }
    }
}
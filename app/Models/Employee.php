<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'phone',
        'nickname',
        'username',
        'computer_password',
        'copier_code',
        'email',
        'email_password',
        'express_username',
        'express_password',
        'department_id',
        'position',
        'role',
        'status',
        'login_email',
        'password',
        'email_verified_at',
        'remember_token',
        'hire_date',
        // ✅ FIXED: เพิ่ม Permission Fields ที่หายไป
        'vpn_access',
        'color_printing',
        'remote_work',      // เพิ่มเติม
        'admin_access',     // เพิ่มเติม
        // 'salary',    // ❌ เอาออกตามที่ขอ - ข้อ 4
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
        'express_password',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'hire_date' => 'date',
        // ✅ FIXED: เพิ่ม Casting สำหรับ Permission Fields
        'vpn_access' => 'boolean',
        'color_printing' => 'boolean',
        'remote_work' => 'boolean',
        'admin_access' => 'boolean',
        // 'salary' => 'decimal:2',    // ❌ เอาออกตามที่ขอ - ข้อ 4
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name_th',
        'full_name_en',
        'full_name',          // ✅ เพิ่ม
        'employee_id',        // ✅ เพิ่ม
        'role_display',
        'status_display',
        'status_badge',       // ✅ เพิ่ม
        'status_thai',        // ✅ เพิ่ม
        'years_of_service',   // ✅ เพิ่ม
        'permissions_summary', // ✅ NEW: สรุปสิทธิ์
        // 'formatted_salary',   // ❌ เอาออกตามที่ขอ - ข้อ 4
        // 'display_password',   // ❌ เอาออก - จะจัดการแยก
        // 'canBeManaged',       // ❌ เอาออก - นี่คือสาเหตุของ error!
    ];

    // ===========================================
    // AUTHENTICATION METHODS
    // ===========================================

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'email'; // or 'login_email' if you prefer
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        // You can implement email verification if needed
    }

    // ===========================================
    // RELATIONSHIPS
    // ===========================================

    /**
     * Get the department that owns the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get departments managed by this employee.
     */
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    // ===========================================
    // ACCESSORS & MUTATORS
    // ===========================================

    /**
     * Get the employee's full name in Thai.
     */
    public function getFullNameThAttribute()
    {
        return trim($this->first_name_th . ' ' . $this->last_name_th);
    }

    /**
     * Get the employee's full name in English.
     */
    public function getFullNameEnAttribute()
    {
        return trim($this->first_name_en . ' ' . $this->last_name_en);
    }

    /**
     * ✅ เพิ่ม: Get the employee's full name (prefer Thai).
     */
    public function getFullNameAttribute()
    {
        return $this->full_name_th ?: $this->full_name_en;
    }

    /**
     * ✅ เพิ่ม: Get employee ID (same as employee_code).
     */
    public function getEmployeeIdAttribute()
    {
        return $this->employee_code;
    }

    /**
     * ✅ เพิ่ม: Get English name.
     */
    public function getEnglishNameAttribute()
    {
        return $this->full_name_en;
    }

    /**
     * ✅ เพิ่ม: Get department name only.
     */
    public function getDepartmentNameAttribute()
    {
        return $this->department ? $this->department->name : 'ไม่ระบุแผนก';
    }

    /**
     * ✅ เพิ่ม: Check if in accounting department.
     */
    public function getIsAccountingDepartmentAttribute()
    {
        $dept = $this->department()->first();
        return $dept && ($dept->name === 'บัญชี' || $dept->express_enabled);
    }

    /**
     * ✅ เพิ่ม: Get years of service.
     */
    public function getYearsOfServiceAttribute()
    {
        if (!$this->hire_date) {
            return 0;
        }
        
        return $this->hire_date->diffInYears(now());
    }

    /**
     * ✅ NEW: Get permissions summary.
     */
    public function getPermissionsSummaryAttribute()
    {
        $permissions = [];
        
        if ($this->vpn_access) {
            $permissions[] = 'VPN';
        }
        if ($this->color_printing) {
            $permissions[] = 'Print Color';
        }
        if ($this->remote_work) {
            $permissions[] = 'Remote Work';
        }
        if ($this->admin_access) {
            $permissions[] = 'Admin Panel';
        }
        
        return empty($permissions) ? 'Basic Access' : implode(', ', $permissions);
    }

    /**
     * ❌ เอาออกตามที่ขอ - ข้อ 4: ไม่แสดงข้อมูลเงินเดือน
     * Get formatted salary.
     *
    public function getFormattedSalaryAttribute()
    {
        if (!$this->salary) {
            return 'ไม่ระบุ';
        }
        
        return number_format($this->salary, 2) . ' บาท';
    }
    */

    /**
     * ✅ เพิ่ม: Get status badge color.
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' ? 'success' : 'secondary';
    }

    /**
     * ✅ เพิ่ม: Get status in Thai.
     */
    public function getStatusThaiAttribute()
    {
        return $this->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }

    /**
     * ✅ ข้อ 3: Get email password for display (IT/SystemAdmin only).
     */
    public function getDisplayEmailPasswordAttribute()
    {
        $currentUser = auth()->user();
        
        if (!$currentUser) {
            return '[ซ่อน]';
        }
        
        // Only IT Admin and Super Admin can see email passwords
        if (in_array($currentUser->role, ['super_admin', 'it_admin'])) {
            return $this->email_password ?: '[ไม่มี]';
        }
        
        return '[ซ่อน]';
    }

    /**
     * ✅ ข้อ 3: Get computer password for display (IT/SystemAdmin only).
     */
    public function getDisplayComputerPasswordAttribute()
    {
        $currentUser = auth()->user();
        
        if (!$currentUser) {
            return '[ซ่อน]';
        }
        
        // Only IT Admin and Super Admin can see computer passwords
        if (in_array($currentUser->role, ['super_admin', 'it_admin'])) {
            return $this->computer_password ?: '[ไม่มี]';
        }
        
        return '[ซ่อน]';
    }

    /**
     * Get the employee's display name (prefer Thai, fallback to English).
     */
    public function getDisplayNameAttribute()
    {
        return $this->full_name_th ?: $this->full_name_en;
    }

    /**
     * Get the employee's role display name.
     */
    public function getRoleDisplayAttribute()
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'it_admin' => 'IT Admin',
            'hr' => 'HR',
            'manager' => 'Manager',
            'express' => 'Express',
            'employee' => 'Employee',
        ];

        return $roles[$this->role] ?? 'Employee';
    }

    /**
     * Get the employee's status display name.
     */
    public function getStatusDisplayAttribute()
    {
        return $this->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }

    /**
     * Get the employee's initials.
     */
    public function getInitialsAttribute()
    {
        $firstInitial = $this->first_name_en ? strtoupper(substr($this->first_name_en, 0, 1)) : '';
        $lastInitial = $this->last_name_en ? strtoupper(substr($this->last_name_en, 0, 1)) : '';
        
        return $firstInitial . $lastInitial;
    }

    // ===========================================
    // PERMISSION ACCESSORS (NEW)
    // ===========================================

    /**
     * ✅ NEW: Get VPN access status with icon.
     */
    public function getVpnAccessDisplayAttribute()
    {
        return $this->vpn_access 
            ? '<span class="badge bg-success"><i class="fas fa-shield-alt me-1"></i>อนุญาต</span>'
            : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>ไม่อนุญาต</span>';
    }

    /**
     * ✅ NEW: Get color printing status with icon.
     */
    public function getColorPrintingDisplayAttribute()
    {
        return $this->color_printing
            ? '<span class="badge bg-warning text-dark"><i class="fas fa-palette me-1"></i>อนุญาต</span>'
            : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>ไม่อนุญาต</span>';
    }

    /**
     * ✅ NEW: Get remote work status with icon.
     */
    public function getRemoteWorkDisplayAttribute()
    {
        return $this->remote_work
            ? '<span class="badge bg-info"><i class="fas fa-home me-1"></i>อนุญาต</span>'
            : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>ไม่อนุญาต</span>';
    }

    /**
     * ✅ NEW: Get admin access status with icon.
     */
    public function getAdminAccessDisplayAttribute()
    {
        return $this->admin_access
            ? '<span class="badge bg-danger"><i class="fas fa-user-shield me-1"></i>อนุญาต</span>'
            : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>ไม่อนุญาต</span>';
    }

    // ===========================================
    // METHODS (ไม่ใช่ ACCESSORS)
    // ===========================================

    /**
     * ✅ แก้ไขแล้ว: Check if employee can be managed by current user.
     * ⚠️ นี่เป็น METHOD ไม่ใช่ ACCESSOR - ห้ามใส่ใน $appends!
     */
    public function canBeManaged($user = null)
    {
        $currentUser = $user ?: auth()->user();
        
        if (!$currentUser) {
            return false;
        }
        
        // Super admin can manage everyone
        if ($currentUser->role === 'super_admin') {
            return true;
        }
        
        // IT admin can manage non-super-admin
        if ($currentUser->role === 'it_admin' && $this->role !== 'super_admin') {
            return true;
        }
        
        // HR can manage employee, express
        if ($currentUser->role === 'hr' && in_array($this->role, ['employee', 'express'])) {
            return true;
        }
        
        // Manager can manage same department
        if ($currentUser->role === 'manager' && $currentUser->department_id === $this->department_id) {
            return true;
        }
        
        return false;
    }

    /**
     * ✅ NEW: Check if user has specific permission.
     */
    public function hasPermission($permission)
    {
        switch ($permission) {
            case 'vpn':
                return $this->vpn_access;
            case 'color_printing':
                return $this->color_printing;
            case 'remote_work':
                return $this->remote_work;
            case 'admin_access':
                return $this->admin_access;
            default:
                return false;
        }
    }

    /**
     * ✅ NEW: Grant permission to user.
     */
    public function grantPermission($permission)
    {
        switch ($permission) {
            case 'vpn':
                $this->vpn_access = true;
                break;
            case 'color_printing':
                $this->color_printing = true;
                break;
            case 'remote_work':
                $this->remote_work = true;
                break;
            case 'admin_access':
                $this->admin_access = true;
                break;
        }
        
        return $this->save();
    }

    /**
     * ✅ NEW: Revoke permission from user.
     */
    public function revokePermission($permission)
    {
        switch ($permission) {
            case 'vpn':
                $this->vpn_access = false;
                break;
            case 'color_printing':
                $this->color_printing = false;
                break;
            case 'remote_work':
                $this->remote_work = false;
                break;
            case 'admin_access':
                $this->admin_access = false;
                break;
        }
        
        return $this->save();
    }

    /**
     * ✅ NEW: Get all permissions as array.
     */
    public function getAllPermissions()
    {
        return [
            'vpn_access' => $this->vpn_access,
            'color_printing' => $this->color_printing,
            'remote_work' => $this->remote_work,
            'admin_access' => $this->admin_access,
        ];
    }

    /**
     * Mutator for email - ensure login_email is synced.
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value;
        
        // Auto-sync login_email
        if (!isset($this->attributes['login_email']) || empty($this->attributes['login_email'])) {
            $this->attributes['login_email'] = $value;
        }
    }

    /**
     * Mutator for phone - format phone number.
     */
    public function setPhoneAttribute($value)
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $value);
        
        // Format as xxx-xxx-xxxx if 10 digits
        if (strlen($phone) === 10) {
            $phone = substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }
        
        $this->attributes['phone'] = $phone;
    }

    // ===========================================
    // SCOPES
    // ===========================================

    /**
     * Scope a query to only include active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive employees.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * ✅ NEW: Scope for employees with VPN access.
     */
    public function scopeWithVpnAccess($query)
    {
        return $query->where('vpn_access', true);
    }

    /**
     * ✅ NEW: Scope for employees with color printing access.
     */
    public function scopeWithColorPrinting($query)
    {
        return $query->where('color_printing', true);
    }

    /**
     * ✅ NEW: Scope for employees with remote work access.
     */
    public function scopeWithRemoteWork($query)
    {
        return $query->where('remote_work', true);
    }

    /**
     * ✅ NEW: Scope for employees with admin access.
     */
    public function scopeWithAdminAccess($query)
    {
        return $query->where('admin_access', true);
    }

    /**
     * Scope a query to search employees.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('employee_code', 'LIKE', "%{$search}%")
              ->orWhere('first_name_th', 'LIKE', "%{$search}%")
              ->orWhere('last_name_th', 'LIKE', "%{$search}%")
              ->orWhere('first_name_en', 'LIKE', "%{$search}%")
              ->orWhere('last_name_en', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('username', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to include employees with Express access.
     */
    public function scopeWithExpress($query)
    {
        return $query->whereNotNull('express_username');
    }

    /**
     * Scope a query to order employees by name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('first_name_th', $direction)
                    ->orderBy('last_name_th', $direction);
    }

    // ===========================================
    // METHODS
    // ===========================================

    /**
     * Check if employee has specific role.
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        
        return $this->role === $role;
    }

    /**
     * Check if employee is admin (super_admin or it_admin).
     */
    public function isAdmin()
    {
        return $this->hasRole(['super_admin', 'it_admin']);
    }

    /**
     * Check if employee is super admin.
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if employee can manage other employees.
     */
    public function canManageEmployees()
    {
        return $this->hasRole(['super_admin', 'it_admin', 'hr', 'manager']);
    }

    /**
     * Check if employee has Express access.
     */
    public function hasExpressAccess()
    {
        return !empty($this->express_username);
    }

    /**
     * Check if employee is in accounting department.
     */
    public function isInAccounting()
    {
        return $this->department && $this->department->name === 'บัญชี';
    }

    /**
     * Get employee's full contact information.
     */
    public function getContactInfo()
    {
        return [
            'email' => $this->email,
            'phone' => $this->phone,
            'department' => $this->department->name ?? null,
            'position' => $this->position,
        ];
    }

    /**
     * Get employee's system access information.
     */
    public function getSystemAccess()
    {
        return [
            'employee_code' => $this->employee_code,
            'username' => $this->username,
            'email' => $this->email,
            'keycard_id' => $this->keycard_id,
            'copier_code' => $this->copier_code,
            'express_username' => $this->express_username,
            'role' => $this->role,
            'status' => $this->status,
            'permissions' => $this->getAllPermissions(),
        ];
    }

    /**
     * Generate a new password and return it.
     */
    public function generateNewPassword($length = 10)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        $this->password = bcrypt($password);
        $this->save();
        
        return $password; // Return plain password for initial setup
    }

    /**
     * Send welcome email to new employee.
     */
    public function sendWelcomeEmail()
    {
        // You can implement email sending logic here
        // This is just a placeholder
    }

    /**
     * Deactivate employee account.
     */
    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
        return $this;
    }

    /**
     * Activate employee account.
     */
    public function activate()
    {
        $this->update(['status' => 'active']);
        return $this;
    }

    /**
     * Transfer employee to new department.
     */
    public function transferTo($departmentId, $newPosition = null)
    {
        $updateData = ['department_id' => $departmentId];
        
        if ($newPosition) {
            $updateData['position'] = $newPosition;
        }
        
        $this->update($updateData);
        
        return $this;
    }

    // ===========================================
    // VALIDATION RULES - ✅ FIXED: เอา PHONE UNIQUE ออก + เพิ่ม Permission Fields
    // ===========================================

    /**
     * Get validation rules for creating employee.
     */
    public static function getCreateRules()
    {
        return [
            'employee_code' => 'required|string|max:20|unique:employees,employee_code',
            'keycard_id' => 'required|string|max:20|unique:employees,keycard_id',
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => 'required|string|max:20', // ✅ ลบ unique ออกแล้ว
            'nickname' => 'nullable|string|max:50',
            'username' => 'required|string|max:100|unique:employees,username',
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => 'required|email|max:255|unique:employees,email',
            'email_password' => 'nullable|string|min:6',
            'express_username' => 'nullable|string|max:7',
            'express_password' => 'nullable|string|max:4',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:100',
            'role' => 'required|in:super_admin,it_admin,hr,manager,express,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6',
            'hire_date' => 'nullable|date', // ✅ เพิ่ม hire_date
            // ✅ FIXED: เพิ่ม Permission Validation Rules
            'vpn_access' => 'nullable|boolean',
            'color_printing' => 'nullable|boolean',
            'remote_work' => 'nullable|boolean',
            'admin_access' => 'nullable|boolean',
            // 'salary' => 'nullable|numeric|min:0', // ❌ เอาออกตามที่ขอ - ข้อ 4
        ];
    }

    /**
     * Get validation rules for updating employee.
     */
    public static function getUpdateRules($id)
    {
        return [
            'employee_code' => "required|string|max:20|unique:employees,employee_code,{$id}",
            'keycard_id' => "required|string|max:20|unique:employees,keycard_id,{$id}",
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => 'required|string|max:20', // ✅ ลบ unique ออกแล้ว
            'nickname' => 'nullable|string|max:50',
            'username' => "required|string|max:100|unique:employees,username,{$id}",
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => "required|email|max:255|unique:employees,email,{$id}",
            'email_password' => 'nullable|string|min:6',
            'express_username' => 'nullable|string|max:7',
            'express_password' => 'nullable|string|max:4',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:100',
            'role' => 'required|in:super_admin,it_admin,hr,manager,express,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
            'hire_date' => 'nullable|date', // ✅ เพิ่ม hire_date
            // ✅ FIXED: เพิ่ม Permission Validation Rules
            'vpn_access' => 'nullable|boolean',
            'color_printing' => 'nullable|boolean',
            'remote_work' => 'nullable|boolean',
            'admin_access' => 'nullable|boolean',
            // 'salary' => 'nullable|numeric|min:0', // ❌ เอาออกตามที่ขอ - ข้อ 4
        ];
    }

    /**
     * ✅ เพิ่ม Static method: เอา phone unique validation ออก
     */
    public static function getStatuses()
    {
        return [
            'active' => 'ใช้งาน',
            'inactive' => 'ไม่ใช้งาน'
        ];
    }

    /**
     * ✅ เพิ่ม Static method: Role list
     */
    public static function getRoles()
    {
        return [
            'super_admin' => 'Super Admin',
            'it_admin' => 'IT Admin',
            'hr' => 'HR',
            'manager' => 'Manager',
            'express' => 'Express',
            'employee' => 'Employee'
        ];
    }

    /**
     * ✅ NEW: Get permission list
     */
    public static function getPermissions()
    {
        return [
            'vpn_access' => 'การใช้งาน VPN',
            'color_printing' => 'การปริ้นสี', 
            'remote_work' => 'ทำงานจากที่บ้าน',
            'admin_access' => 'เข้าถึงแผงควบคุม'
        ];
    }

    /**
     * Boot method for model events.
     */
    protected static function booted()
    {
        // Auto-sync login_email when email changes
        static::saving(function ($employee) {
            if ($employee->isDirty('email')) {
                $employee->login_email = $employee->email;
            }
        });
    }
}

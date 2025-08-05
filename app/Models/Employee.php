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
        'branch_id',           // ✅ NEW: เพิ่ม branch_id
        'position',
        'role',
        'status',
        'login_email',
        'password',
        'email_verified_at',
        'remember_token',
        'hire_date',
        // Permission Fields
        'vpn_access',
        'color_printing',
        'remote_work',
        'admin_access',
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
        // Permission Field Casts
        'vpn_access' => 'boolean',
        'color_printing' => 'boolean',
        'remote_work' => 'boolean',
        'admin_access' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name_th',
        'full_name_en',
        'full_name',
        'employee_id',
        'role_display',
        'status_display',
        'status_badge',
        'status_thai',
        'years_of_service',
        'permissions_summary',
        'branch_name',         // ✅ NEW: เพิ่ม branch_name accessor
        'full_location',       // ✅ NEW: เพิ่ม full_location (department + branch)
        'branch_code',         // ✅ NEW: เพิ่ม branch_code accessor
        'location_display',    // ✅ NEW: แสดงที่ตั้งแบบสั้น
    ];

    // ===========================================
    // AUTHENTICATION METHODS (เหมือนเดิม)
    // ===========================================

    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification()
    {
        // You can implement email verification if needed
    }

    // ===========================================
    // ✅ ENHANCED: RELATIONSHIPS
    // ===========================================

    /**
     * Get the department that owns the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * ✅ ENHANCED: Get the branch that owns the employee.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get departments managed by this employee.
     */
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    /**
     * ✅ ENHANCED: Get branches managed by this employee.
     */
    public function managedBranches()
    {
        return $this->hasMany(Branch::class, 'manager_id');
    }

    /**
     * ✅ NEW: Get colleagues in the same branch.
     */
    public function branchColleagues()
    {
        return $this->hasMany(Employee::class, 'branch_id', 'branch_id')
                    ->where('id', '!=', $this->id)
                    ->where('status', 'active');
    }

    /**
     * ✅ NEW: Get colleagues in the same department.
     */
    public function departmentColleagues()
    {
        return $this->hasMany(Employee::class, 'department_id', 'department_id')
                    ->where('id', '!=', $this->id)
                    ->where('status', 'active');
    }

    // ===========================================
    // ✅ ENHANCED: ACCESSORS & MUTATORS (เหมือนเดิม + เพิ่มใหม่)
    // ===========================================

    public function getFullNameThAttribute()
    {
        return trim($this->first_name_th . ' ' . $this->last_name_th);
    }

    public function getFullNameEnAttribute()
    {
        return trim($this->first_name_en . ' ' . $this->last_name_en);
    }

    public function getFullNameAttribute()
    {
        return $this->full_name_th ?: $this->full_name_en;
    }

    public function getEmployeeIdAttribute()
    {
        return $this->employee_code;
    }

    public function getEnglishNameAttribute()
    {
        return $this->full_name_en;
    }

    public function getDepartmentNameAttribute()
    {
        return $this->department ? $this->department->name : 'ไม่ระบุแผนก';
    }

    /**
     * ✅ ENHANCED: Get branch name.
     */
    public function getBranchNameAttribute()
    {
        return $this->branch ? $this->branch->name : 'ไม่ระบุสาขา';
    }

    /**
     * ✅ NEW: Get branch code.
     */
    public function getBranchCodeAttribute()
    {
        return $this->branch ? ($this->branch->code ?? $this->branch->branch_code ?? 'N/A') : 'N/A';
    }

    /**
     * ✅ ENHANCED: Get full location (Department + Branch).
     */
    public function getFullLocationAttribute()
    {
        $department = $this->department_name;
        $branch = $this->branch_name;
        
        if ($department === 'ไม่ระบุแผนก' && $branch === 'ไม่ระบุสาขา') {
            return 'ไม่ระบุ';
        } elseif ($department === 'ไม่ระบุแผนก') {
            return $branch;
        } elseif ($branch === 'ไม่ระบุสาขา') {
            return $department;
        } else {
            return "{$department} - {$branch}";
        }
    }

    /**
     * ✅ NEW: Get location display (short version).
     */
    public function getLocationDisplayAttribute()
    {
        $department = $this->department ? $this->department->name : null;
        $branch = $this->branch ? $this->branch->name : null;
        
        if ($department && $branch) {
            return $branch . ' (' . $department . ')';
        } elseif ($branch) {
            return $branch;
        } elseif ($department) {
            return $department;
        } else {
            return 'ไม่ระบุ';
        }
    }

    /**
     * ✅ ENHANCED: Get branch code with name.
     */
    public function getBranchFullNameAttribute()
    {
        return $this->branch ? $this->branch->full_name : 'ไม่ระบุสาขา';
    }

    public function getIsAccountingDepartmentAttribute()
    {
        $dept = $this->department()->first();
        return $dept && ($dept->name === 'บัญชี' || $dept->express_enabled);
    }

    public function getYearsOfServiceAttribute()
    {
        if (!$this->hire_date) {
            return 0;
        }
        
        return $this->hire_date->diffInYears(now());
    }

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

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' ? 'success' : 'secondary';
    }

    public function getStatusThaiAttribute()
    {
        return $this->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }

    public function getDisplayEmailPasswordAttribute()
    {
        $currentUser = auth()->user();
        
        if (!$currentUser) {
            return '[ซ่อน]';
        }
        
        if (in_array($currentUser->role, ['super_admin', 'it_admin'])) {
            return $this->email_password ?: '[ไม่มี]';
        }
        
        return '[ซ่อน]';
    }

    public function getDisplayComputerPasswordAttribute()
    {
        $currentUser = auth()->user();
        
        if (!$currentUser) {
            return '[ซ่อน]';
        }
        
        if (in_array($currentUser->role, ['super_admin', 'it_admin'])) {
            return $this->computer_password ?: '[ไม่มี]';
        }
        
        return '[ซ่อน]';
    }

    public function getDisplayNameAttribute()
    {
        return $this->full_name_th ?: $this->full_name_en;
    }

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

    public function getStatusDisplayAttribute()
    {
        return $this->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }

    public function getInitialsAttribute()
    {
        $firstInitial = $this->first_name_en ? strtoupper(substr($this->first_name_en, 0, 1)) : '';
        $lastInitial = $this->last_name_en ? strtoupper(substr($this->last_name_en, 0, 1)) : '';
        
        return $firstInitial . $lastInitial;
    }

    // ===========================================
    // PERMISSION ACCESSORS (เหมือนเดิม)
    // ===========================================

    public function getVpnAccessDisplayAttribute()
    {
        return $this->vpn_access 
            ? '<span class="badge bg-success"><i class="fas fa-shield-alt me-1"></i>อนุญาต</span>'
            : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>ไม่อนุญาต</span>';
    }

    public function getColorPrintingDisplayAttribute()
    {
        return $this->color_printing
            ? '<span class="badge bg-warning text-dark"><i class="fas fa-palette me-1"></i>อนุญาต</span>'
            : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>ไม่อนุญาต</span>';
    }

    public function getRemoteWorkDisplayAttribute()
    {
        return $this->remote_work
            ? '<span class="badge bg-info"><i class="fas fa-home me-1"></i>อนุญาต</span>'
            : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>ไม่อนุญาต</span>';
    }

    public function getAdminAccessDisplayAttribute()
    {
        return $this->admin_access
            ? '<span class="badge bg-danger"><i class="fas fa-user-shield me-1"></i>อนุญาต</span>'
            : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>ไม่อนุญาต</span>';
    }

    // ===========================================
    // ✅ ENHANCED: METHODS (เหมือนเดิม + เพิ่มใหม่)
    // ===========================================

    public function canBeManaged($user = null)
    {
        $currentUser = $user ?: auth()->user();
        
        if (!$currentUser) {
            return false;
        }
        
        if ($currentUser->role === 'super_admin') {
            return true;
        }
        
        if ($currentUser->role === 'it_admin' && $this->role !== 'super_admin') {
            return true;
        }
        
        if ($currentUser->role === 'hr' && in_array($this->role, ['employee', 'express'])) {
            return true;
        }
        
        if ($currentUser->role === 'manager' && $currentUser->department_id === $this->department_id) {
            return true;
        }
        
        return false;
    }

    /**
     * ✅ ENHANCED: Check if employee can be managed in the same branch.
     */
    public function canBeManagedInBranch($user = null)
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
        
        // Branch manager can manage same branch
        if ($currentUser->role === 'manager' && 
            $currentUser->branch_id && 
            $currentUser->branch_id === $this->branch_id) {
            return true;
        }
        
        // Department manager can manage same department
        if ($currentUser->role === 'manager' && 
            $currentUser->department_id && 
            $currentUser->department_id === $this->department_id) {
            return true;
        }
        
        return false;
    }

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

    public function getAllPermissions()
    {
        return [
            'vpn_access' => $this->vpn_access,
            'color_printing' => $this->color_printing,
            'remote_work' => $this->remote_work,
            'admin_access' => $this->admin_access,
        ];
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value;
        
        if (!isset($this->attributes['login_email']) || empty($this->attributes['login_email'])) {
            $this->attributes['login_email'] = $value;
        }
    }

    public function setPhoneAttribute($value)
    {
        $phone = preg_replace('/\D/', '', $value);
        
        if (strlen($phone) === 10) {
            $phone = substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }
        
        $this->attributes['phone'] = $phone;
    }

    // ===========================================
    // ✅ ENHANCED: SCOPES (เหมือนเดิม + เพิ่มใหม่)
    // ===========================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * ✅ ENHANCED: Scope by branch.
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * ✅ ENHANCED: Scope by branch and department.
     */
    public function scopeByLocation($query, $branchId = null, $departmentId = null)
    {
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        
        return $query;
    }

    /**
     * ✅ NEW: Scope employees without branch.
     */
    public function scopeWithoutBranch($query)
    {
        return $query->whereNull('branch_id');
    }

    /**
     * ✅ NEW: Scope employees with branch.
     */
    public function scopeWithBranch($query)
    {
        return $query->whereNotNull('branch_id');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeWithVpnAccess($query)
    {
        return $query->where('vpn_access', true);
    }

    public function scopeWithColorPrinting($query)
    {
        return $query->where('color_printing', true);
    }

    public function scopeWithRemoteWork($query)
    {
        return $query->where('remote_work', true);
    }

    public function scopeWithAdminAccess($query)
    {
        return $query->where('admin_access', true);
    }

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

    public function scopeWithExpress($query)
    {
        return $query->whereNotNull('express_username');
    }

    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('first_name_th', $direction)
                    ->orderBy('last_name_th', $direction);
    }

    // ===========================================
    // ✅ ENHANCED: ADDITIONAL METHODS (เหมือนเดิม + เพิ่มใหม่)
    // ===========================================

    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->hasRole(['super_admin', 'it_admin']);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * ✅ ENHANCED: Check if employee is branch manager.
     */
    public function isBranchManager()
    {
        return $this->managedBranches()->exists();
    }

    /**
     * ✅ ENHANCED: Check if employee is manager of specific branch.
     */
    public function isManagerOfBranch($branchId)
    {
        return $this->managedBranches()->where('id', $branchId)->exists();
    }

    public function canManageEmployees()
    {
        return $this->hasRole(['super_admin', 'it_admin', 'hr', 'manager']);
    }

    public function hasExpressAccess()
    {
        return !empty($this->express_username);
    }

    public function isInAccounting()
    {
        return $this->department && $this->department->name === 'บัญชี';
    }

    /**
     * ✅ ENHANCED: Check if employee is in specific branch.
     */
    public function isInBranch($branchId)
    {
        return $this->branch_id == $branchId;
    }

    /**
     * ✅ NEW: Check if employee is in same branch as another employee.
     */
    public function isInSameBranchAs(Employee $otherEmployee)
    {
        return $this->branch_id && $this->branch_id === $otherEmployee->branch_id;
    }

    /**
     * ✅ ENHANCED: Get employee's location information.
     */
    public function getLocationInfo()
    {
        return [
            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
                'code' => $this->branch->branch_code_compat,
                'full_name' => $this->branch->full_name,
                'is_active' => $this->branch->is_active,
            ] : null,
            'department' => $this->department ? [
                'id' => $this->department->id,
                'name' => $this->department->name,
                'express_enabled' => $this->department->express_enabled ?? false,
            ] : null,
            'full_location' => $this->full_location,
            'location_display' => $this->location_display,
        ];
    }

    public function getContactInfo()
    {
        return [
            'email' => $this->email,
            'phone' => $this->phone,
            'department' => $this->department->name ?? null,
            'branch' => $this->branch->name ?? null,          // ✅ ENHANCED: เพิ่ม branch
            'position' => $this->position,
            'location_display' => $this->location_display,    // ✅ NEW: เพิ่ม location_display
        ];
    }

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
            'branch' => $this->branch ? $this->branch->name : null, // ✅ NEW: เพิ่ม branch
        ];
    }

    public function generateNewPassword($length = 10)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        $this->password = bcrypt($password);
        $this->save();
        
        return $password;
    }

    public function sendWelcomeEmail()
    {
        // You can implement email sending logic here
    }

    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
        return $this;
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
        return $this;
    }

    public function transferTo($departmentId, $newPosition = null)
    {
        $updateData = ['department_id' => $departmentId];
        
        if ($newPosition) {
            $updateData['position'] = $newPosition;
        }
        
        $this->update($updateData);
        
        return $this;
    }

    /**
     * ✅ ENHANCED: Transfer employee to new branch.
     */
    public function transferToBranch($branchId, $departmentId = null, $newPosition = null)
    {
        $updateData = ['branch_id' => $branchId];
        
        if ($departmentId) {
            $updateData['department_id'] = $departmentId;
        }
        
        if ($newPosition) {
            $updateData['position'] = $newPosition;
        }
        
        $this->update($updateData);
        
        return $this;
    }

    /**
     * ✅ NEW: Transfer employee with full location change.
     */
    public function transferToLocation($branchId = null, $departmentId = null, $newPosition = null, $reason = null)
    {
        $updateData = [];
        $changes = [];
        
        if ($branchId !== null) {
            $updateData['branch_id'] = $branchId;
            $oldBranch = $this->branch ? $this->branch->name : 'ไม่ระบุ';
            $newBranch = Branch::find($branchId)->name ?? 'ไม่ระบุ';
            $changes[] = "สาขา: {$oldBranch} → {$newBranch}";
        }
        
        if ($departmentId !== null) {
            $updateData['department_id'] = $departmentId;
            $oldDept = $this->department ? $this->department->name : 'ไม่ระบุ';
            $newDept = Department::find($departmentId)->name ?? 'ไม่ระบุ';
            $changes[] = "แผนก: {$oldDept} → {$newDept}";
        }
        
        if ($newPosition) {
            $updateData['position'] = $newPosition;
            $changes[] = "ตำแหน่ง: {$this->position} → {$newPosition}";
        }
        
        if (!empty($updateData)) {
            $this->update($updateData);
            
            \Log::info("Employee location transfer completed", [
                'employee_id' => $this->id,
                'employee_name' => $this->full_name_th,
                'changes' => $changes,
                'reason' => $reason ?? 'ไม่ระบุ'
            ]);
        }
        
        return $this;
    }

    // ===========================================
    // ✅ ENHANCED: VALIDATION RULES (อัพเดตเพิ่ม branch_id)
    // ===========================================

    public static function getCreateRules()
    {
        return [
            'employee_code' => 'required|string|max:20|unique:employees,employee_code',
            'keycard_id' => 'required|string|max:20|unique:employees,keycard_id',
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'nickname' => 'nullable|string|max:50',
            'username' => 'required|string|max:100|unique:employees,username',
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => 'required|email|max:255|unique:employees,email',
            'email_password' => 'nullable|string|min:6',
            'express_username' => 'nullable|string|max:7',
            'express_password' => 'nullable|string|max:4',
            'department_id' => 'required|exists:departments,id',
            'branch_id' => 'nullable|exists:branches,id',           // ✅ ENHANCED: branch validation
            'position' => 'required|string|max:100',
            'role' => 'required|in:super_admin,it_admin,hr,manager,express,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6',
            'hire_date' => 'nullable|date',
            // Permission Fields
            'vpn_access' => 'nullable|boolean',
            'color_printing' => 'nullable|boolean',
            'remote_work' => 'nullable|boolean',
            'admin_access' => 'nullable|boolean',
        ];
    }

    public static function getUpdateRules($id)
    {
        return [
            'employee_code' => "required|string|max:20|unique:employees,employee_code,{$id}",
            'keycard_id' => "required|string|max:20|unique:employees,keycard_id,{$id}",
            'first_name_th' => 'required|string|max:100',
            'last_name_th' => 'required|string|max:100',
            'first_name_en' => 'required|string|max:100',
            'last_name_en' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'nickname' => 'nullable|string|max:50',
            'username' => "required|string|max:100|unique:employees,username,{$id}",
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => "required|email|max:255|unique:employees,email,{$id}",
            'email_password' => 'nullable|string|min:6',
            'express_username' => 'nullable|string|max:7',
            'express_password' => 'nullable|string|max:4',
            'department_id' => 'required|exists:departments,id',
            'branch_id' => 'nullable|exists:branches,id',           // ✅ ENHANCED: branch validation
            'position' => 'required|string|max:100',
            'role' => 'required|in:super_admin,it_admin,hr,manager,express,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
            'hire_date' => 'nullable|date',
            // Permission Fields
            'vpn_access' => 'nullable|boolean',
            'color_printing' => 'nullable|boolean',
            'remote_work' => 'nullable|boolean',
            'admin_access' => 'nullable|boolean',
        ];
    }

    public static function getStatuses()
    {
        return [
            'active' => 'ใช้งาน',
            'inactive' => 'ไม่ใช้งาน'
        ];
    }

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

    public static function getPermissions()
    {
        return [
            'vpn_access' => 'การใช้งาน VPN',
            'color_printing' => 'การปริ้นสี', 
            'remote_work' => 'ทำงานจากที่บ้าน',
            'admin_access' => 'เข้าถึงแผงควบคุม'
        ];
    }

    protected static function booted()
    {
        static::saving(function ($employee) {
            if ($employee->isDirty('email')) {
                $employee->login_email = $employee->email;
            }
        });
    }
}

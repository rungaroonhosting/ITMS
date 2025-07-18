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
        'express_code',
        'department_id',
        'position',
        'role',
        'status',
        'login_email',
        'password',
        'email_verified_at',
        'remember_token',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name_th',
        'full_name_en',
        'role_display',
        'status_display',
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
    // VALIDATION RULES
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
            'phone' => 'required|string|max:20|unique:employees,phone',
            'nickname' => 'nullable|string|max:50',
            'username' => 'required|string|max:100|unique:employees,username',
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => 'required|email|max:255|unique:employees,email',
            'email_password' => 'nullable|string|min:6',
            'express_username' => 'nullable|string|max:7',
            'express_code' => 'nullable|string|max:4',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:100',
            'role' => 'required|in:super_admin,it_admin,hr,manager,express,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6',
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
            'phone' => "required|string|max:20|unique:employees,phone,{$id}",
            'nickname' => 'nullable|string|max:50',
            'username' => "required|string|max:100|unique:employees,username,{$id}",
            'computer_password' => 'nullable|string|min:6',
            'copier_code' => 'nullable|string|max:10',
            'email' => "required|email|max:255|unique:employees,email,{$id}",
            'email_password' => 'nullable|string|min:6',
            'express_username' => 'nullable|string|max:7',
            'express_code' => 'nullable|string|max:4',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:100',
            'role' => 'required|in:super_admin,it_admin,hr,manager,express,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
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
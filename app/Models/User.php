<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'employee_id',
        'is_active',
        'last_login_at',
        'email_verified_at',
        'remember_token',
        'password_reset_token',
        'password_reset_expires_at',
        // ✅ NEW: Branch fields
        'first_name_th',
        'last_name_th',
        'first_name_en',
        'last_name_en',
        'nickname',
        'phone',
        'branch_id',
        'managed_branch_id',
        'department_id',
        'position',
        'hire_date',
        'salary',
        'status',
        'notes',
        'keycard_id',
        'copier_code',
        'express_username',
        'express_password',
        'email_password',
        'computer_password',
        'login_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_reset_token',
        'email_password',
        'computer_password',
        'express_password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'password_reset_expires_at' => 'datetime',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->password)) {
                $user->password = Hash::make('password');
            }
            
            // Auto-generate employee_id if not provided
            if (empty($user->employee_id)) {
                $user->employee_id = 'EMP' . str_pad(
                    User::withoutTrashed()->max('id') + 1, 
                    4, 
                    '0', 
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * ✅ NEW: Branch Relationships
     */
    
    // สาขาที่พนักงานสังกัด
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // สาขาที่พนักงานเป็น Manager
    public function managedBranch(): HasOne
    {
        return $this->hasOne(Branch::class, 'manager_id');
    }

    /**
     * Existing Relationships
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Employee\Models\Employee::class, 'employee_id');
    }

    // แผนกที่สังกัด (ถ้ามี)
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * ✅ NEW: Accessors
     */
    
    // ชื่อเต็มภาษาไทย
    public function getFullNameThAttribute(): string
    {
        if ($this->first_name_th && $this->last_name_th) {
            return $this->first_name_th . ' ' . $this->last_name_th;
        }
        return $this->name ?? '';
    }

    // ชื่อเต็มภาษาอังกฤษ
    public function getFullNameEnAttribute(): string
    {
        if ($this->first_name_en && $this->last_name_en) {
            return $this->first_name_en . ' ' . $this->last_name_en;
        }
        return $this->name ?? '';
    }

    // แสดงชื่อพร้อมรหัสพนักงาน
    public function getDisplayNameAttribute(): string
    {
        return $this->full_name_th . ' (' . $this->employee_id . ')';
    }

    // ตรวจสอบว่าเป็น Manager สาขาหรือไม่
    public function getIsBranchManagerAttribute(): bool
    {
        return !is_null($this->managed_branch_id);
    }

    /**
     * ✅ NEW: Branch Scopes
     */
    
    // พนักงานที่สามารถเป็น Manager ได้
    public function scopeAvailableManagers($query)
    {
        return $query->where('role', '!=', 'super_admin')
                    ->where(function($q) {
                        $q->whereNull('managed_branch_id')
                          ->orWhereDoesntHave('managedBranch');
                    });
    }

    // พนักงานที่เป็น Manager อยู่
    public function scopeBranchManagers($query)
    {
        return $query->whereNotNull('managed_branch_id')
                    ->whereHas('managedBranch');
    }

    // พนักงานในสาขาเฉพาะ
    public function scopeInBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // พนักงานที่ไม่มีสาขา
    public function scopeWithoutBranch($query)
    {
        return $query->whereNull('branch_id');
    }

    // Role Methods
    public function hasAnyRole($roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return in_array($this->role, $roles);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isItAdmin(): bool
    {
        return $this->role === 'it_admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'it_admin', 'admin']);
    }

    // Permission Methods
    public function hasAnyPermission($permissions): bool
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }
        $userPermissions = $this->permissions ?? [];
        return !empty(array_intersect($permissions, $userPermissions));
    }

    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        $userPermissions = $this->permissions ?? [];
        return in_array($permission, $userPermissions);
    }

    public function canAccess(string $resource, string $action = 'view'): bool
    {
        $permission = $resource . '.' . $action;
        return $this->hasPermission($permission);
    }

    /**
     * ✅ Enhanced Branch Methods
     */
    
    // ตรวจสอบสิทธิ์
    public function canManageBranches(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    // ตรวจสอบว่าสามารถดูข้อมูลพนักงานได้หรือไม่
    public function canViewEmployees(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'hr', 'manager']);
    }

    // ดึงข้อมูลสาขาที่จัดการ
    public function getManagedBranchInfo(): ?array
    {
        if ($this->managedBranch) {
            return [
                'id' => $this->managedBranch->id,
                'name' => $this->managedBranch->name,
                'code' => $this->managedBranch->branch_code,
                'employees_count' => $this->managedBranch->employees()->count(),
            ];
        }

        return null;
    }

    // ดึงข้อมูลสาขาที่สังกัด
    public function getBranchInfo(): ?array
    {
        if ($this->branch) {
            return [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
                'code' => $this->branch->branch_code,
                'manager' => $this->branch->manager ? $this->branch->manager->full_name_th : null,
            ];
        }

        return null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['super_admin', 'it_admin', 'admin']);
    }

    // Authentication Methods
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    public function generatePasswordResetToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update([
            'password_reset_token' => $token,
            'password_reset_expires_at' => now()->addHours(24),
        ]);
        return $token;
    }

    public function isPasswordResetTokenValid(string $token): bool
    {
        return $this->password_reset_token === $token 
               && $this->password_reset_expires_at 
               && $this->password_reset_expires_at->isFuture();
    }

    public function resetPassword(string $password): void
    {
        $this->update([
            'password' => Hash::make($password),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);
    }

    // Accessors
    public function getRoleDisplayNameAttribute(): string
    {
        return match($this->role) {
            'super_admin' => 'ผู้ดูแลระบบสูงสุด',
            'admin' => 'ผู้ดูแลระบบ',
            'hr' => 'ฝ่ายบุคคล',
            'manager' => 'ผู้จัดการ',
            'employee' => 'พนักงาน',
            default => ucfirst($this->role)
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->employee && $this->employee->avatar) {
            return asset('storage/avatars/' . $this->employee->avatar);
        }
        
        // Generate Gravatar URL
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=40";
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_reset_token',
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
        });
    }

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Employee\Models\Employee::class, 'employee_id');
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
        return in_array($this->role, ['super_admin', 'it_admin']);
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
        return $query->whereIn('role', ['super_admin', 'it_admin']);
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
            'super_admin' => 'Super Administrator',
            'it_admin' => 'IT Administrator',
            'employee' => 'Employee',
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

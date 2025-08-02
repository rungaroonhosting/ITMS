<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code', // ✅ ใช้ 'code' แทน 'branch_code' ให้ตรงกับ Controller
        'description',
        'address',
        'phone',
        'email',
        'manager_id',
        'is_active', // ✅ ใช้ 'is_active' แทน 'status' ให้ตรงกับ Controller
        'capacity',
        'area_sqm',
        'opening_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean', // ✅ เปลี่ยนจาก status เป็น is_active
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'opening_date' => 'date',
        'area_sqm' => 'decimal:2',
        'capacity' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name',
        'status_display',
        'status_badge',
        'employee_count',
        'capacity_usage',
        'manager_name',
        'years_since_opening',
        'formatted_area',
    ];

    // ===========================================
    // RELATIONSHIPS (แก้ไขให้ใช้ User แทน Employee)
    // ===========================================

    /**
     * Get all employees (users) in this branch.
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'branch_id'); // ✅ ใช้ User แทน Employee
    }

    /**
     * Get active employees in this branch.
     */
    public function activeEmployees()
    {
        return $this->hasMany(User::class, 'branch_id')->where('is_active', true); // ✅ ใช้ User
    }

    /**
     * Get the branch manager.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id'); // ✅ ใช้ User แทน Employee
    }

    /**
     * Get departments in this branch (if you have department-branch relationship).
     */
    public function departments()
    {
        return $this->hasManyThrough(Department::class, User::class, 'branch_id', 'id', 'id', 'department_id')
                    ->distinct();
    }

    // ===========================================
    // ACCESSORS & MUTATORS
    // ===========================================

    /**
     * Get the branch's full name with code.
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} ({$this->code})"; // ✅ ใช้ code แทน branch_code
    }

    /**
     * Get the branch's status display name.
     */
    public function getStatusDisplayAttribute()
    {
        return $this->is_active ? 'เปิดให้บริการ' : 'ปิดชั่วคราว'; // ✅ ใช้ is_active
    }

    /**
     * Get the branch's status badge color.
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active ? 'success' : 'secondary'; // ✅ ใช้ is_active
    }

    /**
     * Get the number of employees in this branch.
     */
    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * Get active employee count in this branch.
     */
    public function getActiveEmployeeCountAttribute()
    {
        return $this->activeEmployees()->count();
    }

    /**
     * Get capacity usage percentage.
     */
    public function getCapacityUsageAttribute()
    {
        if (!$this->capacity || $this->capacity <= 0) {
            return 0;
        }

        $currentCount = $this->getActiveEmployeeCountAttribute();
        return round(($currentCount / $this->capacity) * 100, 1);
    }

    /**
     * Get manager's name.
     */
    public function getManagerNameAttribute()
    {
        return $this->manager ? $this->manager->full_name_th : 'ไม่ระบุ';
    }

    /**
     * Get years since opening.
     */
    public function getYearsSinceOpeningAttribute()
    {
        if (!$this->opening_date) {
            return 0;
        }

        return $this->opening_date->diffInYears(now());
    }

    /**
     * Get formatted area.
     */
    public function getFormattedAreaAttribute()
    {
        if (!$this->area_sqm) {
            return 'ไม่ระบุ';
        }

        return number_format($this->area_sqm, 2) . ' ตร.ม.';
    }

    /**
     * Get capacity status with color.
     */
    public function getCapacityStatusAttribute()
    {
        $usage = $this->capacity_usage;
        
        if ($usage >= 90) {
            return ['status' => 'เต็มกำลัง', 'color' => 'danger'];
        } elseif ($usage >= 70) {
            return ['status' => 'ใกล้เต็ม', 'color' => 'warning'];
        } elseif ($usage >= 30) {
            return ['status' => 'ปกติ', 'color' => 'success'];
        } else {
            return ['status' => 'ว่าง', 'color' => 'info'];
        }
    }

    /**
     * Get formatted phone number.
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) {
            return 'ไม่ระบุ';
        }

        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $this->phone);
        
        // Format as xxx-xxx-xxxx if 10 digits
        if (strlen($phone) === 10) {
            return substr($phone, 0, 2) . '-' . substr($phone, 2, 3) . '-' . substr($phone, 5);
        }
        
        return $this->phone;
    }

    /**
     * Mutator for code - ensure uppercase.
     */
    public function setCodeAttribute($value) // ✅ ใช้ code แทน branch_code
    {
        $this->attributes['code'] = strtoupper($value);
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
            $phone = substr($phone, 0, 2) . '-' . substr($phone, 2, 3) . '-' . substr($phone, 5);
        }
        
        $this->attributes['phone'] = $phone;
    }

    // ===========================================
    // SCOPES
    // ===========================================

    /**
     * Scope a query to only include active branches.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true); // ✅ ใช้ is_active
    }

    /**
     * Scope a query to only include inactive branches.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false); // ✅ ใช้ is_active
    }

    /**
     * Scope a query to search branches.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code', 'LIKE', "%{$search}%") // ✅ ใช้ code
              ->orWhere('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%") // ✅ เพิ่ม description
              ->orWhere('address', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to order branches by name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    /**
     * Scope branches with high capacity usage.
     */
    public function scopeNearCapacity($query, $threshold = 70)
    {
        return $query->whereRaw('
            (SELECT COUNT(*) FROM users 
             WHERE users.branch_id = branches.id 
             AND users.is_active = 1 
             AND users.deleted_at IS NULL) >= (branches.capacity * ? / 100)
        ', [$threshold]); // ✅ ใช้ users table และ is_active
    }

    /**
     * Scope branches with manager.
     */
    public function scopeWithManager($query)
    {
        return $query->whereNotNull('manager_id');
    }

    /**
     * Scope branches without manager.
     */
    public function scopeWithoutManager($query)
    {
        return $query->whereNull('manager_id');
    }

    // ===========================================
    // METHODS
    // ===========================================

    /**
     * Check if branch is active.
     */
    public function isActive()
    {
        return $this->is_active; // ✅ ใช้ is_active
    }

    /**
     * Check if branch is at capacity.
     */
    public function isAtCapacity()
    {
        return $this->capacity_usage >= 100;
    }

    /**
     * Check if branch is near capacity.
     */
    public function isNearCapacity($threshold = 80)
    {
        return $this->capacity_usage >= $threshold;
    }

    /**
     * Get branch capacity status info.
     */
    public function getCapacityInfo()
    {
        return [
            'current' => $this->getActiveEmployeeCountAttribute(),
            'capacity' => $this->capacity ?: 0,
            'usage_percent' => $this->capacity_usage,
            'available' => max(0, ($this->capacity ?: 0) - $this->getActiveEmployeeCountAttribute()),
            'status' => $this->getCapacityStatusAttribute(),
        ];
    }

    /**
     * Assign manager to branch.
     */
    public function assignManager(User $user) // ✅ ใช้ User แทน Employee
    {
        // Ensure user is in this branch
        if ($user->branch_id !== $this->id) {
            $user->update(['branch_id' => $this->id]);
        }

        $this->update(['manager_id' => $user->id]);
        $user->update(['managed_branch_id' => $this->id]); // ✅ เพิ่มการอัพเดต managed_branch_id
        
        return $this;
    }

    /**
     * Remove manager from branch.
     */
    public function removeManager()
    {
        if ($this->manager) {
            $this->manager->update(['managed_branch_id' => null]); // ✅ เพิ่มการลบ managed_branch_id
        }
        
        $this->update(['manager_id' => null]);
        
        return $this;
    }

    /**
     * Transfer employees to another branch.
     */
    public function transferEmployeesTo(Branch $targetBranch, $employeeIds = null)
    {
        $query = $this->employees();
        
        if ($employeeIds) {
            $query->whereIn('id', $employeeIds);
        }
        
        $transferred = $query->update(['branch_id' => $targetBranch->id]);
        
        return $transferred;
    }

    /**
     * Get branch statistics.
     */
    public function getStatistics()
    {
        $employees = $this->employees()->with('department');
        
        return [
            'total_employees' => $employees->count(),
            'active_employees' => $employees->where('is_active', true)->count(), // ✅ ใช้ is_active
            'inactive_employees' => $employees->where('is_active', false)->count(), // ✅ ใช้ is_active
            'departments' => $employees->get()->pluck('department.name')->unique()->values(),
            'capacity_info' => $this->getCapacityInfo(),
            'years_operating' => $this->years_since_opening,
            'manager' => $this->manager ? [
                'id' => $this->manager->id,
                'name' => $this->manager->full_name_th,
                'email' => $this->manager->email,
            ] : null,
        ];
    }

    /**
     * Activate branch.
     */
    public function activate()
    {
        $this->update(['is_active' => true]); // ✅ ใช้ is_active
        return $this;
    }

    /**
     * Deactivate branch.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]); // ✅ ใช้ is_active
        return $this;
    }

    /**
     * Toggle status
     */
    public function toggleStatus()
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }

    // ===========================================
    // VALIDATION RULES
    // ===========================================

    /**
     * Get validation rules for creating branch.
     */
    public static function getCreateRules()
    {
        return [
            'code' => 'required|string|max:10|unique:branches,code', // ✅ ใช้ code
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id', // ✅ ใช้ users table
            'is_active' => 'boolean', // ✅ ใช้ is_active
            'capacity' => 'nullable|integer|min:1|max:1000',
            'area_sqm' => 'nullable|numeric|min:0|max:99999.99',
            'opening_date' => 'nullable|date|before_or_equal:today',
        ];
    }

    /**
     * Get validation rules for updating branch.
     */
    public static function getUpdateRules($id)
    {
        return [
            'code' => "required|string|max:10|unique:branches,code,{$id}", // ✅ ใช้ code
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id', // ✅ ใช้ users table
            'is_active' => 'boolean', // ✅ ใช้ is_active
            'capacity' => 'nullable|integer|min:1|max:1000',
            'area_sqm' => 'nullable|numeric|min:0|max:99999.99',
            'opening_date' => 'nullable|date|before_or_equal:today',
        ];
    }

    /**
     * Get status options.
     */
    public static function getStatuses()
    {
        return [
            true => 'เปิดให้บริการ',
            false => 'ปิดชั่วคราว'
        ]; // ✅ ใช้ boolean แทน string
    }

    /**
     * Boot method for model events.
     */
    protected static function booted()
    {
        // Auto uppercase branch code
        static::saving(function ($branch) {
            if ($branch->isDirty('code')) { // ✅ ใช้ code
                $branch->code = strtoupper($branch->code);
            }
        });
        
        // Clean up when deleting
        static::deleting(function ($branch) {
            // Remove manager assignment
            if ($branch->manager) {
                $branch->manager->update(['managed_branch_id' => null]);
            }

            // Remove branch assignment from employees
            $branch->employees()->update(['branch_id' => null]);
        });
    }
}

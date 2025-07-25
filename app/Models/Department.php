<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'express_enabled', // ✅ เพิ่ม field สำหรับ Express v2.0
        'status',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'express_enabled' => 'boolean', // ✅ Cast เป็น boolean
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
        'status_display',
        'employee_count',
        'express_users_count',
    ];

    // ===========================================
    // RELATIONSHIPS
    // ===========================================

    /**
     * Get the employees for the department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the manager of the department.
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get active employees only.
     */
    public function activeEmployees()
    {
        return $this->hasMany(Employee::class)->where('status', 'active');
    }

    /**
     * Get employees with Express access.
     */
    public function expressEmployees()
    {
        return $this->hasMany(Employee::class)->whereNotNull('express_username');
    }

    // ===========================================
    // ACCESSORS & MUTATORS
    // ===========================================

    /**
     * Get the department's status display name.
     */
    public function getStatusDisplayAttribute()
    {
        return $this->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }

    /**
     * Get the department's employee count.
     */
    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * Get the department's Express users count.
     */
    public function getExpressUsersCountAttribute()
    {
        return $this->expressEmployees()->count();
    }

    /**
     * ✅ Get Express status display
     */
    public function getExpressStatusDisplayAttribute()
    {
        return $this->express_enabled ? 'เปิดใช้งาน Express' : 'ปิดใช้งาน Express';
    }

    // ===========================================
    // SCOPES
    // ===========================================

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive departments.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * ✅ Scope for Express-enabled departments
     */
    public function scopeExpressEnabled($query)
    {
        return $query->where('express_enabled', true);
    }

    /**
     * ✅ Scope for Express-disabled departments
     */
    public function scopeExpressDisabled($query)
    {
        return $query->where('express_enabled', false);
    }

    /**
     * Scope to order departments by name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    /**
     * Scope to search departments.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    // ===========================================
    // METHODS
    // ===========================================

    /**
     * ✅ Check if department has Express enabled
     */
    public function hasExpressAccess()
    {
        return (bool) $this->express_enabled;
    }

    /**
     * ✅ Enable Express for this department
     */
    public function enableExpress()
    {
        $this->update(['express_enabled' => true]);
        
        // Log the change
        \Log::info("Express enabled for department: {$this->name}");
        
        return $this;
    }

    /**
     * ✅ Disable Express for this department
     */
    public function disableExpress()
    {
        $this->update(['express_enabled' => false]);
        
        // Clear Express credentials from employees in this department
        $this->employees()->update([
            'express_username' => null,
            'express_password' => null
        ]);
        
        // Log the change
        \Log::info("Express disabled for department: {$this->name} (cleared {$this->employees()->count()} employee credentials)");
        
        return $this;
    }

    /**
     * ✅ Toggle Express status
     */
    public function toggleExpress()
    {
        if ($this->express_enabled) {
            return $this->disableExpress();
        } else {
            return $this->enableExpress();
        }
    }

    /**
     * Get department statistics.
     */
    public function getStats()
    {
        return [
            'total_employees' => $this->employees()->count(),
            'active_employees' => $this->activeEmployees()->count(),
            'express_users' => $this->expressEmployees()->count(),
            'express_enabled' => $this->express_enabled,
            'express_percentage' => $this->employees()->count() > 0 ? 
                round(($this->expressEmployees()->count() / $this->employees()->count()) * 100, 2) : 0,
        ];
    }

    /**
     * Check if department can be deleted.
     */
    public function canDelete()
    {
        // Cannot delete if has employees
        return $this->employees()->count() === 0;
    }

    /**
     * Activate department.
     */
    public function activate()
    {
        $this->update(['status' => 'active']);
        return $this;
    }

    /**
     * Deactivate department.
     */
    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
        return $this;
    }

    /**
     * Assign manager to department.
     */
    public function assignManager($employeeId)
    {
        $this->update(['manager_id' => $employeeId]);
        return $this;
    }

    /**
     * Remove manager from department.
     */
    public function removeManager()
    {
        $this->update(['manager_id' => null]);
        return $this;
    }

    // ===========================================
    // STATIC METHODS
    // ===========================================

    /**
     * ✅ Get all Express-enabled departments
     */
    public static function getExpressEnabledDepartments()
    {
        return static::expressEnabled()->active()->orderByName()->get();
    }

    /**
     * ✅ Get Express statistics for all departments
     */
    public static function getExpressStats()
    {
        $totalDepartments = static::count();
        $expressEnabled = static::expressEnabled()->count();
        $totalEmployees = Employee::count();
        $expressUsers = Employee::whereNotNull('express_username')->count();

        return [
            'total_departments' => $totalDepartments,
            'express_enabled_departments' => $expressEnabled,
            'express_disabled_departments' => $totalDepartments - $expressEnabled,
            'express_department_percentage' => $totalDepartments > 0 ? 
                round(($expressEnabled / $totalDepartments) * 100, 2) : 0,
            'total_employees' => $totalEmployees,
            'express_users' => $expressUsers,
            'express_user_percentage' => $totalEmployees > 0 ? 
                round(($expressUsers / $totalEmployees) * 100, 2) : 0,
        ];
    }

    /**
     * Get department statuses.
     */
    public static function getStatuses()
    {
        return [
            'active' => 'ใช้งาน',
            'inactive' => 'ไม่ใช้งาน',
        ];
    }

    /**
     * Get validation rules for creating department.
     */
    public static function getCreateRules()
    {
        return [
            'name' => 'required|string|max:100|unique:departments,name',
            'description' => 'nullable|string|max:500',
            'manager_id' => 'nullable|exists:employees,id',
            'express_enabled' => 'boolean',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get validation rules for updating department.
     */
    public static function getUpdateRules($id)
    {
        return [
            'name' => "required|string|max:100|unique:departments,name,{$id}",
            'description' => 'nullable|string|max:500',
            'manager_id' => 'nullable|exists:employees,id',
            'express_enabled' => 'boolean',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    // ===========================================
    // MODEL EVENTS
    // ===========================================

    /**
     * Boot method for model events.
     */
    protected static function booted()
    {
        // When department is deleted, handle employees
        static::deleting(function ($department) {
            if ($department->employees()->count() > 0) {
                throw new \Exception('ไม่สามารถลบแผนกที่มีพนักงานได้');
            }
        });

        // When Express is disabled, clear employee credentials
        static::updating(function ($department) {
            if ($department->isDirty('express_enabled') && !$department->express_enabled) {
                $department->employees()->update([
                    'express_username' => null,
                    'express_password' => null
                ]);
            }
        });

        // Set default sort order
        static::creating(function ($department) {
            if (empty($department->sort_order)) {
                $department->sort_order = static::max('sort_order') + 1;
            }
            
            // Set default Express status to false
            if (!isset($department->express_enabled)) {
                $department->express_enabled = false;
            }
        });
    }

    // ===========================================
    // EXPRESS v2.0 SPECIFIC METHODS 🚀
    // ===========================================

    /**
     * ✅ Bulk enable Express for multiple departments
     */
    public static function bulkEnableExpress(array $departmentIds)
    {
        $count = static::whereIn('id', $departmentIds)->update(['express_enabled' => true]);
        
        \Log::info("Bulk enabled Express for {$count} departments");
        
        return $count;
    }

    /**
     * ✅ Bulk disable Express for multiple departments
     */
    public static function bulkDisableExpress(array $departmentIds)
    {
        // Get departments to disable
        $departments = static::whereIn('id', $departmentIds)->get();
        
        // Clear Express credentials from all employees in these departments
        $employeeCount = 0;
        foreach ($departments as $department) {
            $employeeCount += $department->employees()->count();
            $department->employees()->update([
                'express_username' => null,
                'express_password' => null
            ]);
        }
        
        // Disable Express for departments
        $count = static::whereIn('id', $departmentIds)->update(['express_enabled' => false]);
        
        \Log::info("Bulk disabled Express for {$count} departments, cleared {$employeeCount} employee credentials");
        
        return $count;
    }

    /**
     * ✅ Get departments that need Express setup
     */
    public static function getDepartmentsNeedingExpressSetup()
    {
        return static::expressEnabled()
            ->whereDoesntHave('employees', function($query) {
                $query->whereNotNull('express_username');
            })
            ->get();
    }

    /**
     * ✅ Generate Express report for department
     */
    public function generateExpressReport()
    {
        $employees = $this->employees()->with('department')->get();
        $expressUsers = $employees->filter(function($employee) {
            return !empty($employee->express_username);
        });

        return [
            'department_id' => $this->id,
            'department_name' => $this->name,
            'express_enabled' => $this->express_enabled,
            'total_employees' => $employees->count(),
            'express_users' => $expressUsers->count(),
            'non_express_users' => $employees->count() - $expressUsers->count(),
            'express_percentage' => $employees->count() > 0 ? 
                round(($expressUsers->count() / $employees->count()) * 100, 2) : 0,
            'employees_without_express' => $employees->filter(function($employee) {
                return empty($employee->express_username);
            })->values()->toArray(),
            'manager' => $this->manager ? [
                'name' => $this->manager->full_name_th,
                'email' => $this->manager->email,
                'has_express' => !empty($this->manager->express_username)
            ] : null,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'code',
        'manager_id',
        'parent_id',
        'status',
        'budget',
        'location',
        'phone',
        'email',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
        'budget' => 'decimal:2',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'active_employees_count',
        'total_employees_count',
        'status_display',
        'full_name',
    ];

    // ===========================================
    // RELATIONSHIPS
    // ===========================================

    /**
     * Get the employees for the department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    /**
     * Get the manager of the department.
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get the parent department.
     */
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * Get the child departments.
     */
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    /**
     * Get all descendants recursively.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors recursively.
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    // ===========================================
    // ACCESSORS & MUTATORS
    // ===========================================

    /**
     * Get the department's active employees count.
     */
    public function getActiveEmployeesCountAttribute()
    {
        return $this->employees()->where('status', 'active')->count();
    }

    /**
     * Get the department's total employees count.
     */
    public function getTotalEmployeesCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * Get the department's status display name.
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'active' => 'ใช้งาน',
            'inactive' => 'ไม่ใช้งาน',
            'suspended' => 'ระงับ',
            'archived' => 'เก็บถาวร',
        ];

        return $statuses[$this->status] ?? 'ไม่ระบุ';
    }

    /**
     * Get the department's full name (with parent hierarchy).
     */
    public function getFullNameAttribute()
    {
        $names = collect([$this->name]);
        $parent = $this->parent;

        while ($parent) {
            $names->prepend($parent->name);
            $parent = $parent->parent;
        }

        return $names->implode(' > ');
    }

    /**
     * Get the department hierarchy level.
     */
    public function getLevelAttribute()
    {
        $level = 0;
        $parent = $this->parent;

        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }

        return $level;
    }

    /**
     * Get the department's budget formatted.
     */
    public function getBudgetFormattedAttribute()
    {
        if ($this->budget) {
            return number_format($this->budget, 2) . ' บาท';
        }
        return 'ไม่ระบุ';
    }

    /**
     * Get employees by role in this department.
     */
    public function getEmployeesByRole($role)
    {
        return $this->employees()->where('role', $role)->get();
    }

    /**
     * Mutator for name - always capitalize first letter.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(trim($value));
    }

    /**
     * Mutator for code - always uppercase.
     */
    public function setCodeAttribute($value)
    {
        if ($value) {
            $this->attributes['code'] = strtoupper(trim($value));
        }
    }

    // ===========================================
    // SCOPES
    // ===========================================

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive departments.
     */
    public function scopeInactive(Builder $query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include top-level departments (no parent).
     */
    public function scopeTopLevel(Builder $query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to include departments with employees.
     */
    public function scopeWithEmployees(Builder $query)
    {
        return $query->has('employees');
    }

    /**
     * Scope a query to search departments by name or code.
     */
    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to order by hierarchy and sort order.
     */
    public function scopeOrdered(Builder $query)
    {
        return $query->orderBy('parent_id', 'asc')
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('name', 'asc');
    }

    /**
     * Scope a query to include department statistics.
     */
    public function scopeWithStats(Builder $query)
    {
        return $query->withCount([
            'employees',
            'employees as active_employees_count' => function($q) {
                $q->where('status', 'active');
            },
            'employees as inactive_employees_count' => function($q) {
                $q->where('status', 'inactive');
            }
        ]);
    }

    // ===========================================
    // METHODS
    // ===========================================

    /**
     * Check if department has any employees.
     */
    public function hasEmployees()
    {
        return $this->employees()->exists();
    }

    /**
     * Check if department has any active employees.
     */
    public function hasActiveEmployees()
    {
        return $this->employees()->where('status', 'active')->exists();
    }

    /**
     * Check if department can be deleted.
     */
    public function canBeDeleted()
    {
        return !$this->hasEmployees() && !$this->children()->exists();
    }

    /**
     * Get all employees including from child departments.
     */
    public function getAllEmployees()
    {
        $employeeIds = collect([$this->id]);
        
        // Get all descendant department IDs
        $this->addDescendantIds($employeeIds);
        
        return Employee::whereIn('department_id', $employeeIds)->get();
    }

    /**
     * Recursively add descendant department IDs.
     */
    private function addDescendantIds($collection)
    {
        $children = $this->children;
        
        foreach ($children as $child) {
            $collection->push($child->id);
            $child->addDescendantIds($collection);
        }
    }

    /**
     * Get department tree structure.
     */
    public static function getTree($parentId = null, $level = 0)
    {
        $departments = static::where('parent_id', $parentId)
                            ->where('status', 'active')
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->get();

        $tree = [];
        
        foreach ($departments as $department) {
            $department->level = $level;
            $tree[] = $department;
            
            // Get children recursively
            $children = static::getTree($department->id, $level + 1);
            $tree = array_merge($tree, $children);
        }

        return $tree;
    }

    /**
     * Get department options for select dropdown.
     */
    public static function getSelectOptions($includeInactive = false)
    {
        $query = static::query();
        
        if (!$includeInactive) {
            $query->where('status', 'active');
        }
        
        return $query->ordered()->get()->mapWithKeys(function ($department) {
            $prefix = str_repeat('— ', $department->level);
            return [$department->id => $prefix . $department->name];
        });
    }

    /**
     * Generate unique department code.
     */
    public static function generateCode($name)
    {
        // Get first 3 characters of name (Thai or English)
        $code = strtoupper(substr($name, 0, 3));
        
        // If code exists, add number suffix
        $originalCode = $code;
        $counter = 1;
        
        while (static::where('code', $code)->exists()) {
            $code = $originalCode . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $code;
    }

    /**
     * Move department to new parent.
     */
    public function moveTo($newParentId = null)
    {
        // Prevent circular reference
        if ($newParentId && $this->isDescendantOf($newParentId)) {
            throw new \InvalidArgumentException('Cannot move department to its own descendant.');
        }
        
        $this->update(['parent_id' => $newParentId]);
        
        return $this;
    }

    /**
     * Check if this department is descendant of given department.
     */
    public function isDescendantOf($departmentId)
    {
        $parent = $this->parent;
        
        while ($parent) {
            if ($parent->id == $departmentId) {
                return true;
            }
            $parent = $parent->parent;
        }
        
        return false;
    }

    /**
     * Get department statistics.
     */
    public function getStatistics()
    {
        return [
            'total_employees' => $this->employees()->count(),
            'active_employees' => $this->employees()->where('status', 'active')->count(),
            'inactive_employees' => $this->employees()->where('status', 'inactive')->count(),
            'roles' => $this->employees()
                          ->selectRaw('role, COUNT(*) as count')
                          ->groupBy('role')
                          ->pluck('count', 'role'),
            'budget' => $this->budget,
            'child_departments' => $this->children()->count(),
        ];
    }

    /**
     * Archive department (soft delete with status change).
     */
    public function archive()
    {
        $this->update(['status' => 'archived']);
        $this->delete(); // Soft delete
        
        return $this;
    }

    /**
     * Restore archived department.
     */
    public function restore()
    {
        parent::restore();
        $this->update(['status' => 'active']);
        
        return $this;
    }

    // ===========================================
    // BOOT METHODS
    // ===========================================

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Auto-generate code if not provided
        static::creating(function ($department) {
            if (empty($department->code)) {
                $department->code = static::generateCode($department->name);
            }
            
            if (empty($department->sort_order)) {
                $maxOrder = static::where('parent_id', $department->parent_id)
                                 ->max('sort_order') ?? 0;
                $department->sort_order = $maxOrder + 1;
            }
        });

        // Prevent deletion if has employees or children
        static::deleting(function ($department) {
            if (!$department->canBeDeleted()) {
                throw new \Exception('ไม่สามารถลบแผนกที่มีพนักงานหรือแผนกย่อยได้');
            }
        });
    }

    // ===========================================
    // VALIDATION RULES
    // ===========================================

    /**
     * Get validation rules for creating department.
     */
    public static function getCreateRules()
    {
        return [
            'name' => 'required|string|max:100|unique:departments,name',
            'code' => 'nullable|string|max:10|unique:departments,code',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,inactive,suspended,archived',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
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
            'code' => "nullable|string|max:10|unique:departments,code,{$id}",
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,inactive,suspended,archived',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get validation messages.
     */
    public static function getValidationMessages()
    {
        return [
            'name.required' => 'ชื่อแผนกจำเป็นต้องกรอก',
            'name.unique' => 'ชื่อแผนกนี้มีอยู่แล้วในระบบ',
            'code.unique' => 'รหัสแผนกนี้มีอยู่แล้วในระบบ',
            'parent_id.exists' => 'แผนกหลักที่เลือกไม่มีในระบบ',
            'manager_id.exists' => 'ผู้จัดการที่เลือกไม่มีในระบบ',
            'status.required' => 'สถานะจำเป็นต้องเลือก',
            'status.in' => 'สถานะที่เลือกไม่ถูกต้อง',
            'budget.numeric' => 'งบประมาณต้องเป็นตัวเลข',
            'budget.min' => 'งบประมาณต้องไม่น้อยกว่า 0',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'sort_order.integer' => 'ลำดับการแสดงต้องเป็นตัวเลข',
            'sort_order.min' => 'ลำดับการแสดงต้องไม่น้อยกว่า 0',
        ];
    }
}
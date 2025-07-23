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
        'code',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
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
        'employees_count',
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
     * Get active employees for the department.
     */
    public function activeEmployees()
    {
        return $this->hasMany(Employee::class)->where('status', 'active');
    }

    /**
     * Get the manager for the department.
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    // ===========================================
    // ACCESSORS & MUTATORS
    // ===========================================

    /**
     * Get the department's status display name.
     */
    public function getStatusDisplayAttribute()
    {
        return $this->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }

    /**
     * Get the employees count for the department.
     */
    public function getEmployeesCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * Get the active employees count for the department.
     */
    public function getActiveEmployeesCountAttribute()
    {
        return $this->activeEmployees()->count();
    }

    /**
     * Mutator for name - trim and title case.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    /**
     * Mutator for code - uppercase and trim.
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }

    // ===========================================
    // SCOPES
    // ===========================================

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive departments.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to search departments.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to order departments by name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    // ===========================================
    // METHODS
    // ===========================================

    /**
     * Check if department is accounting department.
     */
    public function isAccounting()
    {
        return in_array($this->name, ['บัญชี', 'แผนกบัญชี', 'แผนกบัญชีและการเงิน']) 
               || $this->code === 'ACC';
    }

    /**
     * Check if department has employees.
     */
    public function hasEmployees()
    {
        return $this->employees()->count() > 0;
    }

    /**
     * Get department statistics.
     */
    public function getStatistics()
    {
        return [
            'total_employees' => $this->employees()->count(),
            'active_employees' => $this->activeEmployees()->count(),
            'inactive_employees' => $this->employees()->where('status', 'inactive')->count(),
            'managers' => $this->employees()->where('role', 'manager')->count(),
            'admins' => $this->employees()->whereIn('role', ['super_admin', 'it_admin'])->count(),
        ];
    }

    /**
     * Activate department.
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    /**
     * Deactivate department.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    /**
     * Transfer all employees to another department.
     */
    public function transferEmployeesTo($newDepartmentId)
    {
        return $this->employees()->update(['department_id' => $newDepartmentId]);
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
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:10|alpha_dash|unique:departments,code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Get validation rules for updating department.
     */
    public static function getUpdateRules($id)
    {
        return [
            'name' => "required|string|max:255|unique:departments,name,{$id}",
            'code' => "required|string|max:10|alpha_dash|unique:departments,code,{$id}",
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Boot method for model events.
     */
    protected static function booted()
    {
        // Auto-generate code from name if not provided
        static::creating(function ($department) {
            if (empty($department->code)) {
                $department->code = static::generateCodeFromName($department->name);
            }
        });
    }

    /**
     * Generate department code from name.
     */
    public static function generateCodeFromName($name)
    {
        // Extract first letters from each word
        $words = explode(' ', $name);
        $code = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // If code is too short, pad with first characters of name
        if (strlen($code) < 3) {
            $code = strtoupper(substr(str_replace(' ', '', $name), 0, 3));
        }
        
        // Check if code already exists and modify if needed
        $originalCode = $code;
        $counter = 1;
        
        while (static::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }

    /**
     * Get departments for select dropdown.
     */
    public static function getSelectOptions($activeOnly = true)
    {
        $query = static::select('id', 'name', 'code')->orderBy('name');
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->get()->mapWithKeys(function ($department) {
            return [$department->id => $department->name];
        });
    }

    /**
     * Get default departments for seeding.
     */
    public static function getDefaultDepartments()
    {
        return [
            [
                'name' => 'แผนกเทคโนโลยีสารสนเทศ',
                'code' => 'IT',
                'description' => 'แผนกที่ดูแลระบบคอมพิวเตอร์และเทคโนโลยี',
                'is_active' => true,
            ],
            [
                'name' => 'แผนกบัญชี',
                'code' => 'ACC',
                'description' => 'แผนกการเงินและบัญชี',
                'is_active' => true,
            ],
            [
                'name' => 'แผนกทรัพยากรบุคคล',
                'code' => 'HR',
                'description' => 'แผนกทรัพยากรบุคคล',
                'is_active' => true,
            ],
            [
                'name' => 'แผนกขาย',
                'code' => 'SALES',
                'description' => 'แผนกการขายและการตลาด',
                'is_active' => true,
            ],
            [
                'name' => 'แผนกการตลาด',
                'code' => 'MKT',
                'description' => 'แผนกการตลาดและประชาสัมพันธ์',
                'is_active' => true,
            ],
            [
                'name' => 'แผนกผลิต',
                'code' => 'PROD',
                'description' => 'แผนกการผลิต',
                'is_active' => true,
            ],
            [
                'name' => 'แผนกคลังสินค้า',
                'code' => 'WH',
                'description' => 'แผนกคลังสินค้า',
                'is_active' => true,
            ],
            [
                'name' => 'แผนกบริหาร',
                'code' => 'ADMIN',
                'description' => 'แผนกบริหารงานทั่วไป',
                'is_active' => true,
            ],
        ];
    }
}
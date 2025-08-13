<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\Authorizable as AuthorizableTrait;

class Employee extends Model implements Authenticatable, Authorizable
{
    use HasFactory, SoftDeletes, AuthenticatableTrait, AuthorizableTrait;

    protected $fillable = [
        'employee_code',
        'keycard_id',
        'first_name_th',
        'last_name_th',
        'first_name_en',
        'last_name_en',
        'nickname',
        'email',
        'login_email',
        'phone',
        'username',
        'password',
        'computer_password',
        'email_password',
        'login_password',
        'copier_code',
        'express_username',
        'express_password',
        'department_id',
        'branch_id',
        'position',
        'role',
        'status',
        'hire_date',
        'vpn_access',
        'color_printing',
        'remote_work',
        'admin_access',
        'photo',
        'photo_original_name',
        'photo_mime_type',
        'photo_size',
        'photo_uploaded_at',
        'remember_token',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'computer_password',
        'email_password',
        'login_password',
        'express_password',
        'remember_token',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'vpn_access' => 'boolean',
        'color_printing' => 'boolean',
        'remote_work' => 'boolean',
        'admin_access' => 'boolean',
        'email_verified_at' => 'datetime',
        'photo_uploaded_at' => 'datetime',
        'password' => 'hashed',
    ];

    // =====================================================
    // AUTHENTICATION INTERFACE METHODS
    // =====================================================

    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
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

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // =====================================================
    // 🔥 FIXED: ENHANCED PHOTO SYSTEM
    // =====================================================

    /**
     * 🔥 CRITICAL FIX: Get photo URL with enhanced detection
     */
    public function getPhotoUrlAttribute()
    {
        try {
            // 🔥 Priority 1: Check if photo exists and file is accessible
            if ($this->photo && $this->photoFileExists()) {
                // ✅ Use asset() instead of Storage::url() for better compatibility
                $photoPath = 'storage/' . str_replace('employees/photos/', 'employees/photos/', $this->photo);
                return asset($photoPath);
            }

            // 🔥 Priority 2: Try to find photo by multiple patterns
            $foundPath = $this->findExistingPhoto();
            if ($foundPath) {
                // Update database with found path
                $this->updateQuietly(['photo' => $foundPath]);
                $photoPath = 'storage/' . str_replace('employees/photos/', 'employees/photos/', $foundPath);
                return asset($photoPath);
            }

            // Priority 3: Generate consistent avatar
            return $this->generateAvatarUrl();

        } catch (\Exception $e) {
            Log::error("Photo URL generation failed for employee {$this->id}: " . $e->getMessage());
            return $this->generateAvatarUrl();
        }
    }

    /**
     * 🔥 ENHANCED: Check if photo file actually exists with multiple checks
     */
    public function photoFileExists(): bool
    {
        if (!$this->photo) return false;

        try {
            // Method 1: Storage disk check
            if (Storage::disk('public')->exists($this->photo)) {
                return true;
            }

            // Method 2: Direct file path check
            $fullPath = storage_path('app/public/' . $this->photo);
            if (file_exists($fullPath)) {
                return true;
            }

            // Method 3: Alternative path check
            $altPath = public_path('storage/' . str_replace('employees/photos/', 'employees/photos/', $this->photo));
            if (file_exists($altPath)) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::warning("Photo existence check failed for employee {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔥 NEW: Find existing photo with comprehensive search
     */
    private function findExistingPhoto(): ?string
    {
        try {
            $photoDir = 'employees/photos';
            
            // Ensure directory exists
            if (!Storage::disk('public')->exists($photoDir)) {
                Storage::disk('public')->makeDirectory($photoDir, 0755, true);
                return null;
            }

            // Search patterns based on employee data
            $searchPatterns = [
                // Current ID-based patterns
                "emp_{$this->id}",
                "employee_{$this->id}",
                
                // Employee code patterns
                $this->employee_code,
                strtolower($this->employee_code),
                
                // Name-based patterns (if available)
                ($this->first_name_en && $this->last_name_en) 
                    ? strtolower($this->first_name_en . '_' . $this->last_name_en) 
                    : null,
                
                // Simple ID
                (string)$this->id,
            ];

            // Remove null patterns
            $searchPatterns = array_filter($searchPatterns);

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Search through all files in photos directory
            $files = Storage::disk('public')->files($photoDir);
            
            foreach ($files as $file) {
                $filename = basename($file);
                $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                // Check if extension is allowed
                if (!in_array($extension, $allowedExtensions)) {
                    continue;
                }
                
                // Check against all patterns
                foreach ($searchPatterns as $pattern) {
                    if (str_contains($nameWithoutExt, $pattern) || str_starts_with($nameWithoutExt, $pattern)) {
                        Log::info("Found photo for employee {$this->id}: {$file} (pattern: {$pattern})");
                        return $file;
                    }
                }
            }

            Log::info("No photo found for employee {$this->id} after comprehensive search");
            return null;

        } catch (\Exception $e) {
            Log::error("Photo search failed for employee {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔥 ENHANCED: Generate avatar with consistent colors
     */
    private function generateAvatarUrl(): string
    {
        $initials = $this->getInitials();
        
        // Use employee ID for consistent color
        $colors = ['B54544', 'E6952A', '0d6efd', '198754', '6f42c1', 'dc3545', 'fd7e14', '20c997'];
        $colorIndex = abs(crc32((string)$this->id)) % count($colors);
        $bgColor = $colors[$colorIndex];

        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . 
               "&background=" . $bgColor . "&color=ffffff&size=400&font-size=0.33&bold=true&format=png";
    }

    /**
     * 🔥 ENHANCED: Check if employee has valid photo
     */
    public function getHasPhotoAttribute()
    {
        return !empty($this->photo) && $this->photoFileExists();
    }

    /**
     * 🔥 ENHANCED: Upload photo with better error handling
     */
    public function uploadPhoto($file)
    {
        try {
            if (!$file || !$file->isValid()) {
                throw new \Exception('Invalid file provided');
            }

            // Validate file
            $this->validatePhotoFile($file);

            // Ensure directories exist
            $this->ensurePhotoDirectoryExists();

            // Generate filename
            $filename = $this->generatePhotoFilename($file);
            $directory = 'employees/photos';
            $relativePath = $directory . '/' . $filename;

            // Delete old photo before uploading new one
            $this->deletePhotoFile();

            // Store the file
            $storedPath = $file->storeAs($directory, $filename, 'public');
            
            if (!$storedPath) {
                throw new \Exception('Failed to store photo file');
            }

            // Verify file was actually stored
            if (!Storage::disk('public')->exists($storedPath)) {
                throw new \Exception('Photo was not saved properly');
            }

            // Update database
            $this->update([
                'photo' => $storedPath,
                'photo_original_name' => $file->getClientOriginalName(),
                'photo_mime_type' => $file->getMimeType(),
                'photo_size' => $file->getSize(),
                'photo_uploaded_at' => now(),
            ]);

            Log::info("Photo uploaded successfully for employee {$this->id}: {$storedPath}");
            
            return $storedPath;

        } catch (\Exception $e) {
            Log::error("Photo upload failed for employee {$this->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 🔥 ENHANCED: Delete photo with better cleanup
     */
    public function deletePhoto()
    {
        try {
            if (!$this->photo) {
                return true;
            }

            $photoPath = $this->photo;
            
            // Delete file from storage (multiple attempts)
            if (Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            // Also try direct file deletion
            $fullPath = storage_path('app/public/' . $photoPath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Update database
            $this->update([
                'photo' => null,
                'photo_original_name' => null,
                'photo_mime_type' => null,
                'photo_size' => null,
                'photo_uploaded_at' => null,
            ]);

            Log::info("Photo deleted successfully for employee {$this->id}: {$photoPath}");
            
            return true;

        } catch (\Exception $e) {
            Log::error("Photo deletion failed for employee {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔥 ENHANCED: Get comprehensive photo information
     */
    public function getPhotoInfo()
    {
        $info = [
            'exists' => $this->has_photo,
            'photo_url' => $this->photo_url,
            'default_photo_url' => $this->generateAvatarUrl(),
            'photo_path' => $this->photo,
            'file_size' => $this->photo_size,
            'file_size_human' => $this->photo_size ? $this->formatBytes($this->photo_size) : '0 B',
            'mime_type' => $this->photo_mime_type,
            'original_name' => $this->photo_original_name,
            'uploaded_at' => $this->photo_uploaded_at,
            'file_exists' => $this->photoFileExists(),
        ];

        // Add file system info if photo exists
        if ($this->photo) {
            $fullPath = storage_path('app/public/' . $this->photo);
            $publicPath = public_path('storage/' . str_replace('employees/photos/', 'employees/photos/', $this->photo));
            
            $info['storage_path'] = $fullPath;
            $info['public_path'] = $publicPath;
            $info['storage_exists'] = file_exists($fullPath);
            $info['public_exists'] = file_exists($publicPath);
            
            if (file_exists($fullPath)) {
                $info['real_file_size'] = filesize($fullPath);
                $info['last_modified'] = date('d/m/Y H:i:s', filemtime($fullPath));
                
                // Get image dimensions
                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo !== false) {
                    $info['dimensions'] = [
                        'width' => $imageInfo[0],
                        'height' => $imageInfo[1]
                    ];
                    $info['width'] = $imageInfo[0];
                    $info['height'] = $imageInfo[1];
                    $info['real_mime_type'] = $imageInfo['mime'];
                }
            }
        }

        return $info;
    }

    /**
     * 🔥 CRITICAL: Ensure storage setup is correct
     */
    private function ensurePhotoDirectoryExists()
    {
        try {
            $directory = 'employees/photos';
            
            // Create directory in storage/app/public
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
                Log::info("Created photo directory: {$directory}");
            }

            // Ensure symlink exists
            $this->ensureStorageSymlink();

            // Test write permission
            $testFile = $directory . '/.test_write_' . time();
            Storage::disk('public')->put($testFile, 'test');
            
            if (Storage::disk('public')->exists($testFile)) {
                Storage::disk('public')->delete($testFile);
                Log::info("Photo directory write test passed");
            } else {
                throw new \Exception("Cannot write to photo directory");
            }

        } catch (\Exception $e) {
            Log::error("Photo directory setup failed: " . $e->getMessage());
            throw new \Exception("Photo storage setup failed: " . $e->getMessage());
        }
    }

    /**
     * 🔥 CRITICAL: Ensure storage symlink exists
     */
    private function ensureStorageSymlink()
    {
        $publicPath = public_path('storage');
        $storagePath = storage_path('app/public');
        
        if (!file_exists($publicPath)) {
            try {
                if (function_exists('symlink')) {
                    symlink($storagePath, $publicPath);
                    Log::info("Created storage symlink: {$publicPath} -> {$storagePath}");
                } else {
                    Log::warning("Symlink function not available. Please run: php artisan storage:link");
                    throw new \Exception("Storage symlink missing. Please run: php artisan storage:link");
                }
            } catch (\Exception $e) {
                Log::error("Failed to create symlink: " . $e->getMessage());
                throw new \Exception("Storage symlink creation failed. Please run: php artisan storage:link");
            }
        }
    }

    /**
     * 🔥 ENHANCED: Generate unique photo filename
     */
    private function generatePhotoFilename($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        return "emp_{$this->id}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Validate photo file
     */
    private function validatePhotoFile($file)
    {
        $maxSize = 2 * 1024 * 1024; // 2MB
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if ($file->getSize() > $maxSize) {
            throw new \Exception('File size exceeds 2MB limit');
        }

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, and GIF are allowed');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Invalid file extension');
        }

        $imageInfo = @getimagesize($file->getPathname());
        if ($imageInfo === false) {
            throw new \Exception('File is not a valid image');
        }

        if ($imageInfo[0] < 50 || $imageInfo[1] < 50) {
            throw new \Exception('Image too small. Minimum size is 50x50 pixels');
        }

        if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
            throw new \Exception('Image too large. Maximum size is 2000x2000 pixels');
        }
    }

    /**
     * Delete photo file only
     */
    private function deletePhotoFile()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            Storage::disk('public')->delete($this->photo);
            Log::info("Deleted old photo: {$this->photo}");
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // =====================================================
    // MODEL EVENTS
    // =====================================================

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($employee) {
            if ($employee->isForceDeleting()) {
                $employee->deletePhotoFile();
                Log::info("Photo deleted on force delete for employee: {$employee->id}");
            }
        });

        static::creating(function ($employee) {
            if (!Storage::disk('public')->exists('employees/photos')) {
                Storage::disk('public')->makeDirectory('employees/photos', 0755, true);
            }
        });
    }

    // =====================================================
    // ACCESSORS & MUTATORS
    // =====================================================

    public function getFullNameThAttribute()
    {
        return trim($this->first_name_th . ' ' . $this->last_name_th);
    }

    public function getFullNameEnAttribute()
    {
        return trim($this->first_name_en . ' ' . $this->last_name_en);
    }

    public function getInitialsAttribute()
    {
        if ($this->first_name_th && $this->last_name_th) {
            return strtoupper(
                mb_substr($this->first_name_th, 0, 1) . 
                mb_substr($this->last_name_th, 0, 1)
            );
        }
        
        if ($this->first_name_en && $this->last_name_en) {
            return strtoupper(
                substr($this->first_name_en, 0, 1) . 
                substr($this->last_name_en, 0, 1)
            );
        }
        
        if ($this->employee_code) {
            return strtoupper(substr($this->employee_code, 0, 2));
        }
        
        return 'NN';
    }

    public function getInitials()
    {
        return $this->initials;
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

        return $roles[$this->role] ?? $this->role;
    }

    public function getStatusDisplayAttribute()
    {
        return $this->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }

    public function getNameAttribute()
    {
        return $this->full_name_th ?: $this->full_name_en ?: $this->email;
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithPhoto($query)
    {
        return $query->whereNotNull('photo');
    }

    public function scopeWithoutPhoto($query)
    {
        return $query->whereNull('photo');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeExpressUsers($query)
    {
        return $query->whereNotNull('express_username');
    }

    // =====================================================
    // HELPER METHODS
    // =====================================================

    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }

        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return false;
    }

    public function canEdit($employee)
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        if ($this->role === 'it_admin' && $employee->role !== 'super_admin') {
            return true;
        }

        if ($this->role === 'hr' && in_array($employee->role, ['employee', 'express'])) {
            return true;
        }

        return $this->id === $employee->id;
    }

    public function canAccess($employee)
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        if ($this->role === 'it_admin' && $employee->role !== 'super_admin') {
            return true;
        }

        if ($this->role === 'hr' && in_array($employee->role, ['employee', 'express'])) {
            return true;
        }

        return $this->id === $employee->id;
    }

    /**
     * 🔥 NEW: Debug photo system
     */
    public function debugPhotoSystem()
    {
        $debug = [
            'employee_id' => $this->id,
            'employee_code' => $this->employee_code,
            'photo_in_db' => $this->photo,
            'has_photo_attribute' => $this->has_photo,
            'photo_url_attribute' => $this->photo_url,
        ];

        if ($this->photo) {
            $debug['storage_exists'] = Storage::disk('public')->exists($this->photo);
            $debug['file_path'] = storage_path('app/public/' . $this->photo);
            $debug['file_exists'] = file_exists(storage_path('app/public/' . $this->photo));
            $debug['public_path'] = public_path('storage/' . str_replace('employees/photos/', 'employees/photos/', $this->photo));
            $debug['public_exists'] = file_exists(public_path('storage/' . str_replace('employees/photos/', 'employees/photos/', $this->photo)));
        }

        $debug['search_result'] = $this->findExistingPhoto();
        $debug['avatar_url'] = $this->generateAvatarUrl();

        return $debug;
    }

    /**
     * 🔥 NEW: Fix missing photos for all employees
     */
    public static function fixAllMissingPhotos()
    {
        $employees = self::whereNotNull('photo')->get();
        $fixed = 0;
        $errors = 0;

        foreach ($employees as $employee) {
            try {
                if (!$employee->photoFileExists()) {
                    $found = $employee->findExistingPhoto();
                    if ($found) {
                        $employee->update(['photo' => $found]);
                        $fixed++;
                        Log::info("Fixed photo for employee {$employee->id}: {$found}");
                    } else {
                        // Clear invalid photo reference
                        $employee->update(['photo' => null]);
                        Log::info("Cleared invalid photo for employee {$employee->id}");
                    }
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error("Failed to fix photo for employee {$employee->id}: " . $e->getMessage());
            }
        }

        return [
            'processed' => $employees->count(),
            'fixed' => $fixed,
            'errors' => $errors
        ];
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DebugPhotoSystem extends Command
{
    protected $signature = 'photo:debug {employee_id?}';
    protected $description = 'Debug photo system issues';

    public function handle()
    {
        $employeeId = $this->argument('employee_id');
        
        if ($employeeId) {
            $employee = Employee::find($employeeId);
            if (!$employee) {
                $this->error("Employee not found");
                return;
            }
            
            $debug = $employee->debugPhotoSystem();
            $this->info("Debug info for Employee {$employeeId}:");
            $this->table(['Property', 'Value'], collect($debug)->map(fn($value, $key) => [$key, $value]));
        } else {
            $this->info("Checking photo system...");
            
            // Check storage symlink
            $this->info("Storage symlink: " . (file_exists(public_path('storage')) ? '✅' : '❌'));
            
            // Check photos directory
            $this->info("Photos directory: " . (Storage::disk('public')->exists('employees/photos') ? '✅' : '❌'));
            
            // Check employees with missing photos
            $missing = Employee::whereNotNull('photo')->get()->filter(fn($emp) => !$emp->photoFileExists());
            $this->info("Employees with missing photo files: " . $missing->count());
            
            if ($missing->count() > 0) {
                $this->info("Attempting to fix missing photos...");
                $result = Employee::fixAllMissingPhotos();
                $this->info("Fixed: {$result['fixed']}, Errors: {$result['errors']}");
            }
        }
    }
}

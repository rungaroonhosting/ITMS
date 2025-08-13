<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\Employee;

class PhotoTroubleshoot extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'photo:troubleshoot {--fix : Automatically fix detected issues}';

    /**
     * The console command description.
     */
    protected $description = 'Troubleshoot and fix photo system issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Laravel Employee Photo System Troubleshoot');
        $this->newLine();
        
        $issues = [];
        $fixes = [];
        
        // Check 1: Storage directories
        $this->info('📂 Checking storage directories...');
        $storageIssues = $this->checkStorageDirectories();
        if (!empty($storageIssues)) {
            $issues = array_merge($issues, $storageIssues);
        }
        
        // Check 2: Storage symlink
        $this->info('🔗 Checking storage symlink...');
        $symlinkIssues = $this->checkStorageSymlink();
        if (!empty($symlinkIssues)) {
            $issues = array_merge($issues, $symlinkIssues);
        }
        
        // Check 3: File permissions
        $this->info('🔐 Checking file permissions...');
        $permissionIssues = $this->checkPermissions();
        if (!empty($permissionIssues)) {
            $issues = array_merge($issues, $permissionIssues);
        }
        
        // Check 4: Photo files integrity
        $this->info('🖼️ Checking photo files integrity...');
        $photoIssues = $this->checkPhotoFiles();
        if (!empty($photoIssues)) {
            $issues = array_merge($issues, $photoIssues);
        }
        
        // Check 5: Configuration
        $this->info('⚙️ Checking configuration...');
        $configIssues = $this->checkConfiguration();
        if (!empty($configIssues)) {
            $issues = array_merge($issues, $configIssues);
        }
        
        $this->newLine();
        
        if (empty($issues)) {
            $this->info('✅ No issues found! Photo system is working correctly.');
            $this->showSystemInfo();
            return 0;
        }
        
        // Show issues
        $this->error('❌ Found ' . count($issues) . ' issue(s):');
        foreach ($issues as $i => $issue) {
            $this->line('   ' . ($i + 1) . '. ' . $issue['description']);
            if (isset($issue['details'])) {
                $this->line('      Details: ' . $issue['details']);
            }
        }
        
        $this->newLine();
        
        // Auto-fix if requested
        if ($this->option('fix')) {
            $this->info('🔧 Attempting to fix issues...');
            $this->newLine();
            
            $fixed = 0;
            foreach ($issues as $issue) {
                if (isset($issue['fix']) && is_callable($issue['fix'])) {
                    try {
                        $result = call_user_func($issue['fix']);
                        if ($result) {
                            $this->line('   ✅ Fixed: ' . $issue['description']);
                            $fixed++;
                        } else {
                            $this->line('   ❌ Failed to fix: ' . $issue['description']);
                        }
                    } catch (\Exception $e) {
                        $this->line('   ❌ Error fixing: ' . $issue['description'] . ' - ' . $e->getMessage());
                    }
                } else {
                    $this->line('   ⚠️ Manual fix required: ' . $issue['description']);
                }
            }
            
            $this->newLine();
            $this->info("🎉 Fixed {$fixed} out of " . count($issues) . " issues.");
            
            if ($fixed > 0) {
                $this->info('🔄 Re-running checks...');
                $this->newLine();
                return $this->handle(); // Re-run to verify fixes
            }
        } else {
            $this->info('💡 Run with --fix flag to automatically fix issues:');
            $this->line('   php artisan photo:troubleshoot --fix');
        }
        
        $this->newLine();
        $this->showManualFixes($issues);
        
        return count($issues) > 0 ? 1 : 0;
    }
    
    /**
     * Check storage directories
     */
    private function checkStorageDirectories()
    {
        $issues = [];
        $directories = [
            'employees',
            'employees/photos',
        ];
        
        foreach ($directories as $dir) {
            if (!Storage::disk('public')->exists($dir)) {
                $issues[] = [
                    'description' => "Missing directory: storage/app/public/{$dir}",
                    'fix' => function() use ($dir) {
                        return Storage::disk('public')->makeDirectory($dir, 0755, true);
                    }
                ];
            } else {
                $this->line("   ✓ Directory exists: {$dir}");
            }
        }
        
        return $issues;
    }
    
    /**
     * Check storage symlink
     */
    private function checkStorageSymlink()
    {
        $issues = [];
        $publicPath = public_path('storage');
        $storagePath = storage_path('app/public');
        
        if (!file_exists($publicPath)) {
            $issues[] = [
                'description' => 'Storage symlink missing: public/storage',
                'fix' => function() use ($publicPath, $storagePath) {
                    try {
                        Artisan::call('storage:link');
                        return file_exists($publicPath);
                    } catch (\Exception $e) {
                        if (function_exists('symlink')) {
                            symlink($storagePath, $publicPath);
                            return file_exists($publicPath);
                        }
                        return false;
                    }
                }
            ];
        } elseif (is_link($publicPath)) {
            $target = readlink($publicPath);
            if ($target !== $storagePath) {
                $issues[] = [
                    'description' => "Storage symlink points to wrong location: {$target}",
                    'details' => "Should point to: {$storagePath}",
                    'fix' => function() use ($publicPath, $storagePath) {
                        unlink($publicPath);
                        symlink($storagePath, $publicPath);
                        return readlink($publicPath) === $storagePath;
                    }
                ];
            } else {
                $this->line('   ✓ Storage symlink correct');
            }
        } elseif (is_dir($publicPath)) {
            $issues[] = [
                'description' => 'public/storage is a directory instead of symlink',
                'fix' => function() use ($publicPath, $storagePath) {
                    File::deleteDirectory($publicPath);
                    symlink($storagePath, $publicPath);
                    return is_link($publicPath);
                }
            ];
        }
        
        return $issues;
    }
    
    /**
     * Check file permissions
     */
    private function checkPermissions()
    {
        $issues = [];
        $paths = [
            storage_path('app/public') => 0755,
            storage_path('app/public/employees') => 0755,
            storage_path('app/public/employees/photos') => 0755,
        ];
        
        foreach ($paths as $path => $expectedPerm) {
            if (file_exists($path)) {
                $currentPerm = fileperms($path) & 0777;
                if ($currentPerm !== $expectedPerm) {
                    $issues[] = [
                        'description' => "Incorrect permissions for: " . basename($path),
                        'details' => sprintf('Current: %o, Expected: %o', $currentPerm, $expectedPerm),
                        'fix' => function() use ($path, $expectedPerm) {
                            return chmod($path, $expectedPerm);
                        }
                    ];
                } else {
                    $this->line('   ✓ Permissions correct: ' . basename($path));
                }
            }
        }
        
        // Check if writable
        if (!is_writable(storage_path('app/public'))) {
            $issues[] = [
                'description' => 'Storage directory is not writable',
                'details' => 'Web server cannot write to storage/app/public'
            ];
        } else {
            $this->line('   ✓ Storage is writable');
        }
        
        return $issues;
    }
    
    /**
     * Check photo files integrity
     */
    private function checkPhotoFiles()
    {
        $issues = [];
        $employees = Employee::whereNotNull('photo')->get();
        $missingFiles = 0;
        $corruptFiles = 0;
        
        foreach ($employees as $employee) {
            if (!Storage::disk('public')->exists($employee->photo)) {
                $missingFiles++;
            } else {
                // Check if file is a valid image
                $filePath = Storage::disk('public')->path($employee->photo);
                $imageInfo = @getimagesize($filePath);
                if ($imageInfo === false) {
                    $corruptFiles++;
                }
            }
        }
        
        if ($missingFiles > 0) {
            $issues[] = [
                'description' => "{$missingFiles} photo files are missing from storage",
                'details' => 'Database references exist but files are missing',
                'fix' => function() use ($employees) {
                    $fixed = 0;
                    foreach ($employees as $employee) {
                        if (!Storage::disk('public')->exists($employee->photo)) {
                            $employee->update(['photo' => null]);
                            $fixed++;
                        }
                    }
                    return $fixed > 0;
                }
            ];
        }
        
        if ($corruptFiles > 0) {
            $issues[] = [
                'description' => "{$corruptFiles} photo files are corrupted",
                'details' => 'Files exist but are not valid images'
            ];
        }
        
        if ($missingFiles === 0 && $corruptFiles === 0) {
            $this->line("   ✓ All {$employees->count()} photo files are valid");
        }
        
        return $issues;
    }
    
    /**
     * Check configuration
     */
    private function checkConfiguration()
    {
        $issues = [];
        
        // Check filesystem configuration
        $defaultDisk = config('filesystems.default');
        $publicDisk = config('filesystems.disks.public');
        
        if (!$publicDisk) {
            $issues[] = [
                'description' => 'Public disk not configured in filesystems.php'
            ];
        } else {
            $this->line('   ✓ Public disk configured');
        }
        
        // Check if public disk root is correct
        $expectedRoot = storage_path('app/public');
        if ($publicDisk && $publicDisk['root'] !== $expectedRoot) {
            $issues[] = [
                'description' => 'Public disk root path is incorrect',
                'details' => "Current: {$publicDisk['root']}, Expected: {$expectedRoot}"
            ];
        }
        
        // Check APP_URL
        $appUrl = config('app.url');
        if (empty($appUrl) || $appUrl === 'http://localhost') {
            $issues[] = [
                'description' => 'APP_URL not properly configured',
                'details' => 'This may cause incorrect photo URLs'
            ];
        } else {
            $this->line('   ✓ APP_URL configured');
        }
        
        return $issues;
    }
    
    /**
     * Show system information
     */
    private function showSystemInfo()
    {
        $this->newLine();
        $this->info('📋 System Information:');
        
        $stats = Employee::getPhotoStatistics();
        
        $info = [
            'Total Employees' => $stats['total_employees'],
            'With Photos' => $stats['with_photo'],
            'Without Photos' => $stats['without_photo'],
            'Photo Coverage' => $stats['coverage_percentage'] . '%',
            'Storage Used' => $stats['storage_used_mb'] . ' MB',
            'Valid Photos' => $stats['valid_photos'],
            'Storage Path' => storage_path('app/public/employees/photos'),
            'Public URL' => Storage::disk('public')->url('employees/photos/'),
            'PHP Upload Max' => ini_get('upload_max_filesize'),
            'PHP Post Max' => ini_get('post_max_size'),
        ];
        
        foreach ($info as $label => $value) {
            $this->line("   {$label}: {$value}");
        }
    }
    
    /**
     * Show manual fixes for issues that can't be auto-fixed
     */
    private function showManualFixes($issues)
    {
        $manualIssues = array_filter($issues, function($issue) {
            return !isset($issue['fix']);
        });
        
        if (!empty($manualIssues)) {
            $this->info('🛠️ Manual fixes required:');
            $this->newLine();
            
            foreach ($manualIssues as $i => $issue) {
                $this->line('   ' . ($i + 1) . '. ' . $issue['description']);
                
                // Provide specific manual fix instructions
                if (strpos($issue['description'], 'APP_URL') !== false) {
                    $this->line('      Fix: Set APP_URL in .env file to your domain');
                    $this->line('      Example: APP_URL=https://yourdomain.com');
                }
                
                if (strpos($issue['description'], 'not writable') !== false) {
                    $this->line('      Fix: Set proper ownership and permissions');
                    $this->line('      Commands:');
                    $this->line('        sudo chown -R www-data:www-data storage/');
                    $this->line('        sudo chmod -R 755 storage/');
                }
                
                if (strpos($issue['description'], 'corrupted') !== false) {
                    $this->line('      Fix: Remove corrupted files and ask users to re-upload');
                }
                
                $this->newLine();
            }
        }
        
        // General troubleshooting tips
        $this->info('💡 Additional troubleshooting tips:');
        $this->line('   • Run: php artisan storage:link');
        $this->line('   • Run: php artisan photo:setup --force');
        $this->line('   • Check web server error logs');
        $this->line('   • Verify .env configuration');
        $this->line('   • Test with: php artisan tinker > Storage::disk("public")->exists("employees/photos")');
    }
}

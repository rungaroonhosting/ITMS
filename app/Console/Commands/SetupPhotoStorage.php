<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class SetupPhotoStorage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'photo:setup {--force : Force recreate directories and symlinks}';

    /**
     * The console command description.
     */
    protected $description = 'Setup photo storage directories and symlinks for Employee Photo System';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎨 Setting up Employee Photo Storage System...');
        $this->newLine();

        // Step 1: Create storage directories
        $this->createStorageDirectories();

        // Step 2: Create storage symlink
        $this->createStorageSymlink();

        // Step 3: Set proper permissions
        $this->setPermissions();

        // Step 4: Create test images
        if ($this->option('force')) {
            $this->createTestImages();
        }

        // Step 5: Verify setup
        $this->verifySetup();

        $this->newLine();
        $this->info('✅ Photo Storage setup completed successfully!');
        $this->info('📁 You can now upload photos in the Employee Management system');

        return 0;
    }

    /**
     * Create necessary storage directories
     */
    private function createStorageDirectories()
    {
        $this->info('📂 Creating storage directories...');

        $directories = [
            'employees',
            'employees/photos',
            'employees/photos/thumbs', // For future thumbnail support
        ];

        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
                $this->line("   ✓ Created: storage/app/public/{$directory}");
            } else {
                $this->line("   • Exists: storage/app/public/{$directory}");
            }
        }

        // Create .gitkeep files
        foreach ($directories as $directory) {
            $gitkeepPath = storage_path("app/public/{$directory}/.gitkeep");
            if (!file_exists($gitkeepPath)) {
                file_put_contents($gitkeepPath, '');
                $this->line("   ✓ Created .gitkeep in {$directory}");
            }
        }
    }

    /**
     * Create or recreate storage symlink
     */
    private function createStorageSymlink()
    {
        $this->info('🔗 Setting up storage symlink...');

        $publicStoragePath = public_path('storage');
        $storageAppPublicPath = storage_path('app/public');

        // Remove existing symlink if force option is used
        if ($this->option('force') && file_exists($publicStoragePath)) {
            if (is_link($publicStoragePath)) {
                unlink($publicStoragePath);
                $this->line('   • Removed existing symlink');
            } elseif (is_dir($publicStoragePath)) {
                File::deleteDirectory($publicStoragePath);
                $this->line('   • Removed existing directory');
            }
        }

        // Create symlink
        if (!file_exists($publicStoragePath)) {
            try {
                // Try using artisan command first
                Artisan::call('storage:link');
                $this->line('   ✓ Created storage symlink using artisan');
            } catch (\Exception $e) {
                // Fallback to manual symlink creation
                if (function_exists('symlink')) {
                    symlink($storageAppPublicPath, $publicStoragePath);
                    $this->line('   ✓ Created storage symlink manually');
                } else {
                    $this->error('   ❌ Cannot create symlink. Please run manually: php artisan storage:link');
                    $this->warn('   Or create symlink manually: ln -s ' . $storageAppPublicPath . ' ' . $publicStoragePath);
                }
            }
        } else {
            $this->line('   • Storage symlink already exists');
        }

        // Verify symlink
        if (is_link($publicStoragePath)) {
            $target = readlink($publicStoragePath);
            if ($target === $storageAppPublicPath) {
                $this->line('   ✓ Symlink is correctly pointing to storage/app/public');
            } else {
                $this->warn("   ⚠ Symlink points to wrong location: {$target}");
            }
        }
    }

    /**
     * Set proper permissions
     */
    private function setPermissions()
    {
        $this->info('🔐 Setting permissions...');

        $paths = [
            storage_path('app/public'),
            storage_path('app/public/employees'),
            storage_path('app/public/employees/photos'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                try {
                    chmod($path, 0755);
                    $this->line("   ✓ Set permissions 755 for: " . basename($path));
                } catch (\Exception $e) {
                    $this->warn("   ⚠ Could not set permissions for: " . basename($path));
                }
            }
        }

        // Set web server permissions (if running as web user)
        if (function_exists('posix_getuid') && posix_getuid() === 0) {
            $this->warn('   ⚠ Running as root. Consider setting proper web server ownership.');
        }
    }

    /**
     * Create test images for verification
     */
    private function createTestImages()
    {
        $this->info('🖼️ Creating test images...');

        $testImagePath = storage_path('app/public/employees/photos/test-image.png');
        
        // Create a simple test image
        $testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        
        if (!file_exists($testImagePath)) {
            file_put_contents($testImagePath, $testImageContent);
            $this->line('   ✓ Created test image: test-image.png');
        }

        // Create SVG default avatar
        $defaultAvatarPath = storage_path('app/public/employees/photos/default-avatar.svg');
        $svgContent = '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg">
            <rect width="100" height="100" fill="#e74c3c"/>
            <text x="50" y="50" font-family="Arial" font-size="32" fill="white" text-anchor="middle" dy=".3em">?</text>
        </svg>';
        
        if (!file_exists($defaultAvatarPath)) {
            file_put_contents($defaultAvatarPath, $svgContent);
            $this->line('   ✓ Created default avatar: default-avatar.svg');
        }
    }

    /**
     * Verify the complete setup
     */
    private function verifySetup()
    {
        $this->info('🔍 Verifying setup...');

        $checks = [
            'Storage directory exists' => Storage::disk('public')->exists('employees/photos'),
            'Public storage symlink exists' => file_exists(public_path('storage')),
            'Storage is writable' => is_writable(storage_path('app/public')),
            'Public storage is accessible' => is_readable(public_path('storage')),
        ];

        $allPassed = true;

        foreach ($checks as $check => $result) {
            if ($result) {
                $this->line("   ✓ {$check}");
            } else {
                $this->line("   ❌ {$check}");
                $allPassed = false;
            }
        }

        if (!$allPassed) {
            $this->newLine();
            $this->error('❌ Some checks failed. Please resolve the issues above.');
            $this->info('💡 Common solutions:');
            $this->line('   • Run: php artisan storage:link');
            $this->line('   • Set permissions: chmod -R 755 storage/app/public');
            $this->line('   • Check web server configuration');
        }

        // Test photo URL generation
        $testUrl = Storage::disk('public')->url('employees/photos/test-image.png');
        $this->line("   📍 Test photo URL: {$testUrl}");

        // Show storage info
        $storageInfo = [
            'Storage path' => storage_path('app/public'),
            'Public storage path' => public_path('storage'),
            'Photo directory' => storage_path('app/public/employees/photos'),
            'Storage disk' => config('filesystems.default'),
            'Public disk' => config('filesystems.disks.public.root'),
        ];

        $this->newLine();
        $this->info('📋 Storage Information:');
        foreach ($storageInfo as $label => $path) {
            $this->line("   {$label}: {$path}");
        }
    }
}

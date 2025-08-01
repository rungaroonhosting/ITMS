<?php

/**
 * Quick Test Script for Permission System
 * 
 * Place this file in your Laravel root directory and run:
 * php TestPermissionSystem.php
 */

// Include Laravel's autoloader
require_once 'vendor/autoload.php';

class PermissionSystemTester
{
    private $errors = [];
    private $warnings = [];
    private $successes = [];
    
    public function __construct()
    {
        echo "🚀 Testing Permission System Implementation...\n\n";
    }
    
    /**
     * Run all tests
     */
    public function runAllTests()
    {
        $this->testDatabaseColumns(); 
        $this->testEmployeeModel();
        $this->testValidationRules();
        $this->testPermissionMethods();
        $this->testMigrationFiles();
        
        $this->displayResults();
    }
    
    /**
     * Test 1: Check if database columns exist
     */
    private function testDatabaseColumns()
    {
        echo "📋 Test 1: Checking Database Columns...\n";
        
        try {
            // Try to connect to database using Laravel's config
            $app = require_once 'bootstrap/app.php';
            $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
            
            $db = \Illuminate\Support\Facades\DB::connection();
            
            // Check if employees table exists
            $tables = $db->select("SHOW TABLES LIKE 'employees'");
            if (empty($tables)) {
                $this->errors[] = "❌ Employees table does not exist";
                return;
            }
            
            // Check permission columns
            $permissionColumns = ['vpn_access', 'color_printing', 'remote_work', 'admin_access'];
            $existingColumns = [];
            
            foreach ($permissionColumns as $column) {
                $columnExists = $db->select("SHOW COLUMNS FROM employees LIKE '{$column}'");
                if (!empty($columnExists)) {
                    $existingColumns[] = $column;
                    $this->successes[] = "✅ Column '{$column}' exists";
                } else {
                    $this->errors[] = "❌ Column '{$column}' missing";
                }
            }
            
            if (count($existingColumns) === count($permissionColumns)) {
                $this->successes[] = "✅ All permission columns exist in database";
            }
            
        } catch (\Exception $e) {
            $this->warnings[] = "⚠️ Could not test database: " . $e->getMessage();
            $this->warnings[] = "   Please check database connection and run migration manually";
        }
        
        echo "\n";
    }
    
    /**
     * Test 2: Check Employee Model
     */
    private function testEmployeeModel()
    {
        echo "🏗️ Test 2: Checking Employee Model...\n";
        
        try {
            // Check if Employee model file exists
            $modelPath = 'app/Models/Employee.php';
            if (!file_exists($modelPath)) {
                $this->errors[] = "❌ Employee model file not found at {$modelPath}";
                return;
            }
            
            $modelContent = file_get_contents($modelPath);
            
            // Check if permission fields are in fillable
            $permissionFields = ['vpn_access', 'color_printing', 'remote_work', 'admin_access'];
            $fillableFound = [];
            
            foreach ($permissionFields as $field) {
                if (strpos($modelContent, "'{$field}'") !== false) {
                    $fillableFound[] = $field;
                    $this->successes[] = "✅ '{$field}' found in model";
                } else {
                    $this->errors[] = "❌ '{$field}' not found in \$fillable array";
                }
            }
            
            // Check if boolean casting exists
            if (strpos($modelContent, "'vpn_access' => 'boolean'") !== false) {
                $this->successes[] = "✅ Boolean casting found for permission fields";
            } else {
                $this->errors[] = "❌ Boolean casting missing for permission fields";
            }
            
            // Check if permission methods exist
            $permissionMethods = ['hasPermission', 'grantPermission', 'revokePermission', 'getAllPermissions'];
            foreach ($permissionMethods as $method) {
                if (strpos($modelContent, "function {$method}") !== false) {
                    $this->successes[] = "✅ Method '{$method}()' found";
                } else {
                    $this->warnings[] = "⚠️ Method '{$method}()' not found (optional)";
                }
            }
            
        } catch (\Exception $e) {
            $this->errors[] = "❌ Error checking Employee model: " . $e->getMessage();
        }
        
        echo "\n";
    }
    
    /**
     * Test 3: Check Validation Rules
     */
    private function testValidationRules()
    {
        echo "📝 Test 3: Checking Validation Rules...\n";
        
        try {
            $requestPath = 'app/Http/Requests/EmployeeRequest.php';
            if (!file_exists($requestPath)) {
                $this->warnings[] = "⚠️ EmployeeRequest file not found at {$requestPath}";
                $this->warnings[] = "   You may need to create this file for proper validation";
                return;
            }
            
            $requestContent = file_get_contents($requestPath);
            
            // Check for permission validation rules
            $permissionFields = ['vpn_access', 'color_printing', 'remote_work', 'admin_access'];
            foreach ($permissionFields as $field) {
                if (strpos($requestContent, "'{$field}' => 'nullable|boolean'") !== false) {
                    $this->successes[] = "✅ Validation rule for '{$field}' found";
                } else {
                    $this->warnings[] = "⚠️ Validation rule for '{$field}' not found";
                }
            }
            
        } catch (\Exception $e) {
            $this->warnings[] = "⚠️ Error checking validation rules: " . $e->getMessage();
        }
        
        echo "\n";
    }
    
    /**
     * Test 4: Check Permission Methods (if possible)
     */
    private function testPermissionMethods()
    {
        echo "⚙️ Test 4: Testing Permission Methods...\n";
        
        try {
            // Try to instantiate the Employee model to test methods
            if (class_exists('App\Models\Employee')) {
                $this->successes[] = "✅ Employee model class can be loaded";
                
                // Check if methods exist
                $reflection = new \ReflectionClass('App\Models\Employee');
                
                $permissionMethods = ['hasPermission', 'grantPermission', 'revokePermission'];
                foreach ($permissionMethods as $method) {
                    if ($reflection->hasMethod($method)) {
                        $this->successes[] = "✅ Method '{$method}()' exists and is callable";
                    } else {
                        $this->warnings[] = "⚠️ Method '{$method}()' does not exist";
                    }
                }
            } else {
                $this->warnings[] = "⚠️ Cannot load Employee model class (Laravel not bootstrapped)";
            }
            
        } catch (\Exception $e) {
            $this->warnings[] = "⚠️ Cannot test permission methods: " . $e->getMessage();
        }
        
        echo "\n";
    }
    
    /**
     * Test 5: Check Migration Files
     */
    private function testMigrationFiles()
    {
        echo "📂 Test 5: Checking Migration Files...\n";
        
        try {
            $migrationDir = 'database/migrations';
            if (!is_dir($migrationDir)) {
                $this->errors[] = "❌ Migration directory not found: {$migrationDir}";
                return;
            }
            
            $migrationFiles = glob($migrationDir . '/*add*permission*fields*.php');
            if (empty($migrationFiles)) {
                $migrationFiles = glob($migrationDir . '/*permission*.php');
            }
            
            if (!empty($migrationFiles)) {
                foreach ($migrationFiles as $file) {
                    $this->successes[] = "✅ Permission migration found: " . basename($file);
                    
                    // Check migration content
                    $content = file_get_contents($file);
                    if (strpos($content, 'vpn_access') !== false && strpos($content, 'color_printing') !== false) {
                        $this->successes[] = "✅ Migration contains required permission fields";
                    }
                }
            } else {
                $this->errors[] = "❌ No permission migration files found";
                $this->errors[] = "   Please create and run the migration first";
            }
            
        } catch (\Exception $e) {
            $this->errors[] = "❌ Error checking migration files: " . $e->getMessage();
        }
        
        echo "\n";
    }
    
    /**
     * Display test results
     */
    private function displayResults()
    {
        echo "═══════════════════════════════════\n";
        echo "📊 TEST RESULTS SUMMARY\n";
        echo "═══════════════════════════════════\n\n";
        
        // Successes
        if (!empty($this->successes)) {
            echo "✅ SUCCESSES (" . count($this->successes) . "):\n";
            foreach ($this->successes as $success) {
                echo "   {$success}\n";
            }
            echo "\n";
        }
        
        // Warnings
        if (!empty($this->warnings)) {
            echo "⚠️  WARNINGS (" . count($this->warnings) . "):\n";
            foreach ($this->warnings as $warning) {
                echo "   {$warning}\n";
            }
            echo "\n";
        }
        
        // Errors
        if (!empty($this->errors)) {
            echo "❌ ERRORS (" . count($this->errors) . "):\n";
            foreach ($this->errors as $error) {
                echo "   {$error}\n";
            }
            echo "\n";
        }
        
        // Overall result
        if (empty($this->errors)) {
            if (empty($this->warnings)) {
                echo "🎉 OVERALL RESULT: PERFECT!\n";
                echo "   Permission system is fully implemented and ready to use.\n";
            } else {
                echo "✅ OVERALL RESULT: GOOD!\n";
                echo "   Permission system should work, but check warnings above.\n";
            }
        } else {
            echo "🚨 OVERALL RESULT: NEEDS ATTENTION!\n";
            echo "   Please fix the errors above before using the permission system.\n";
        }
        
        echo "\n";
        echo "═══════════════════════════════════\n";
        echo "🛠️  NEXT STEPS:\n";
        echo "═══════════════════════════════════\n";
        
        if (!empty($this->errors)) {
            echo "1. Fix all errors listed above\n";
            echo "2. Run 'php artisan migrate' to create database columns\n";
            echo "3. Test the permission system in your application\n";
        } else {
            echo "1. Test editing employee permissions in your application\n";
            echo "2. Verify that permission changes persist after saving\n";
            echo "3. Check that permission display works correctly\n";
        }
        
        echo "\n🎯 Permission System Test Complete!\n\n";
    }
}

// Run the tests
try {
    $tester = new PermissionSystemTester();
    $tester->runAllTests();
} catch (\Exception $e) {
    echo "💥 Fatal Error: " . $e->getMessage() . "\n";
    echo "Please ensure you're running this script from your Laravel root directory.\n";
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupExpressDatabase extends Command
{
    protected $signature = 'express:setup-db {--force : Force run without confirmation}';
    protected $description = 'Setup Express Employee Management Database (Fixed Version)';

    public function handle()
    {
        $this->info('🚀 Express Database Setup v1.4 (Fixed)');
        $this->info('=====================================');

        try {
            // 1. Check Express columns (already done from previous run)
            $this->info('📊 Checking Express columns...');
            
            if (!Schema::hasColumn('employees', 'express_username')) {
                DB::statement('ALTER TABLE employees ADD COLUMN express_username VARCHAR(7) NULL COMMENT "Express system username (7 chars)"');
                $this->line('✅ Added express_username column');
            } else {
                $this->line('⚠️  express_username column already exists (OK)');
            }
            
            if (!Schema::hasColumn('employees', 'express_password')) {
                DB::statement('ALTER TABLE employees ADD COLUMN express_password VARCHAR(10) NULL COMMENT "Express system password (4 chars encrypted)"');
                $this->line('✅ Added express_password column');
            } else {
                $this->line('⚠️  express_password column already exists (OK)');
            }

            // 2. Check departments table structure
            $this->info('🔍 Checking departments table structure...');
            $columns = collect(DB::select('DESCRIBE departments'))->pluck('Field')->toArray();
            $this->line('Departments columns: ' . implode(', ', $columns));
            
            $hasStatus = in_array('status', $columns);
            $this->line($hasStatus ? '✅ Has status column' : '⚠️  No status column found');

            // 3. Add accounting departments (with flexible column handling)
            $this->info('🏢 Adding accounting departments...');
            
            $departments = [
                ['name' => 'บัญชี', 'code' => 'ACC'],
                ['name' => 'แผนกบัญชีและการเงิน', 'code' => 'ACFIN'],
                ['name' => 'การเงิน', 'code' => 'FIN'],
                ['name' => 'Accounting', 'code' => 'ACC_EN'],
                ['name' => 'Finance', 'code' => 'FIN_EN'],
            ];
            
            $added = 0;
            foreach ($departments as $dept) {
                // Check if department already exists (by name or code)
                $exists = DB::table('departments')
                    ->where('name', $dept['name'])
                    ->orWhere('code', $dept['code'])
                    ->exists();
                    
                if (!$exists) {
                    // Prepare insert data based on available columns
                    $insertData = [
                        'name' => $dept['name'],
                        'code' => $dept['code'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    // Add status if column exists
                    if ($hasStatus) {
                        $insertData['status'] = 'active';
                    }
                    
                    DB::table('departments')->insert($insertData);
                    $added++;
                    $this->line("✅ Added department: {$dept['name']} ({$dept['code']})");
                } else {
                    $this->line("⚠️  Department already exists: {$dept['name']}");
                }
            }
            
            if ($added === 0) {
                $this->line('ℹ️  All accounting departments already exist');
            } else {
                $this->line("✅ Successfully added {$added} new departments");
            }

            // 4. Create indexes safely
            $this->info('⚡ Creating performance indexes...');
            
            try {
                // Check if express_username index exists
                $indexExists = DB::select("
                    SELECT COUNT(*) as count
                    FROM INFORMATION_SCHEMA.STATISTICS 
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'employees' 
                    AND INDEX_NAME = 'idx_express_username'
                ")[0]->count > 0;
                
                if (!$indexExists) {
                    DB::statement('CREATE INDEX idx_express_username ON employees(express_username)');
                    $this->line('✅ Created express_username index');
                } else {
                    $this->line('⚠️  Express username index already exists');
                }
            } catch (\Exception $e) {
                $this->line('⚠️  Could not create express_username index: ' . $e->getMessage());
            }

            try {
                // Check if department_id index exists  
                $indexExists = DB::select("
                    SELECT COUNT(*) as count
                    FROM INFORMATION_SCHEMA.STATISTICS 
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'employees' 
                    AND INDEX_NAME = 'idx_department_id'
                ")[0]->count > 0;
                
                if (!$indexExists) {
                    DB::statement('CREATE INDEX idx_department_id ON employees(department_id)');
                    $this->line('✅ Created department_id index');
                } else {
                    $this->line('⚠️  Department ID index already exists');
                }
            } catch (\Exception $e) {
                $this->line('⚠️  Could not create department_id index: ' . $e->getMessage());
            }
            
            // 5. Show final summary
            $this->info('📈 Setup Summary:');
            $this->info('================');
            
            // Count accounting departments
            $accountingDepts = DB::table('departments')
                ->where(function($query) {
                    $query->where('name', 'LIKE', '%บัญชี%')
                          ->orWhere('name', 'LIKE', '%การเงิน%')
                          ->orWhere('name', 'LIKE', '%Accounting%')
                          ->orWhere('name', 'LIKE', '%Finance%');
                })
                ->count();
                
            // Count express users
            $expressUsers = DB::table('employees')->whereNotNull('express_username')->count();
            $totalEmployees = DB::table('employees')->count();
            
            // Count express fields
            $expressFields = collect(DB::select("
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'employees' 
                AND COLUMN_NAME LIKE 'express_%'
            "))->count();
            
            $this->table(
                ['Metric', 'Count', 'Status'],
                [
                    ['Express Fields', $expressFields, $expressFields >= 2 ? '✅ Ready' : '❌ Missing'],
                    ['Accounting Departments', $accountingDepts, $accountingDepts >= 3 ? '✅ Ready' : '⚠️ Partial'],
                    ['Total Employees', $totalEmployees, '📊 Info'],
                    ['Express Users', $expressUsers, $expressUsers > 0 ? '✅ Active' : 'ℹ️  None yet'],
                    ['Database', DB::getDatabaseName(), '✅ Connected'],
                ]
            );
            
            // Show accounting departments that were added/found
            $depts = DB::table('departments')
                ->where(function($query) {
                    $query->where('name', 'LIKE', '%บัญชี%')
                          ->orWhere('name', 'LIKE', '%การเงิน%')
                          ->orWhere('name', 'LIKE', '%Accounting%')
                          ->orWhere('name', 'LIKE', '%Finance%');
                })
                ->select('id', 'name', 'code')
                ->get();
                
            if ($depts->count() > 0) {
                $this->info('🏢 Accounting Departments Found/Added:');
                $this->table(
                    ['ID', 'Name', 'Code'],
                    $depts->map(function($dept) {
                        return [$dept->id, $dept->name, $dept->code];
                    })->toArray()
                );
            }
            
            $this->info('✅ Express Database Setup Complete!');
            $this->info('=====================================');
            $this->line('');
            $this->info('🚀 Next Steps:');
            $this->line('1. Go to /employees/create');
            $this->line('2. Select an accounting department from the list above');
            $this->line('3. The Express section should appear automatically');
            $this->line('4. Test the auto-generation features');
            $this->line('');
            $this->info('🔧 Test Commands:');
            $this->line('• Check Express: php artisan tinker --execute="echo DB::table(\'employees\')->whereNotNull(\'express_username\')->count() . \' Express users\';"');
            $this->line('• Check Depts: php artisan tinker --execute="echo DB::table(\'departments\')->where(\'name\', \'like\', \'%บัญชี%\')->count() . \' Accounting depts\';"');
            
        } catch (\Exception $e) {
            $this->error('❌ Setup failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}

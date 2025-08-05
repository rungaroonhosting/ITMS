<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * ✅ CRITICAL FIX: Fix Branch System Database Issues
     * Run this migration to resolve conflicts and ensure proper branch functionality
     */
    public function up(): void
    {
        try {
            // ✅ STEP 1: Check and fix branches table
            if (!Schema::hasTable('branches')) {
                $this->createBranchesTable();
            } else {
                $this->fixBranchesTableStructure();
            }
            
            // ✅ STEP 2: Check and fix employees table branch_id column
            if (!Schema::hasColumn('employees', 'branch_id')) {
                $this->addBranchIdToEmployees();
            } else {
                $this->fixEmployeesBranchColumn();
            }
            
            // ✅ STEP 3: Fix foreign key constraints
            $this->fixForeignKeyConstraints();
            
            // ✅ STEP 4: Insert sample branches if table is empty
            $this->insertSampleBranches();
            
            echo "✅ Branch System Migration completed successfully!\n";
            
        } catch (\Exception $e) {
            echo "❌ Migration failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    private function createBranchesTable()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('branch_code', 10)->nullable()->unique(); // ✅ Compatibility
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('capacity')->nullable();
            $table->decimal('area_sqm', 10, 2)->nullable();
            $table->date('opening_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index for better performance
            $table->index(['is_active', 'name']);
            $table->index('manager_id');
        });
        
        echo "✅ Created branches table\n";
    }

    private function fixBranchesTableStructure()
    {
        Schema::table('branches', function (Blueprint $table) {
            // ✅ Ensure all required columns exist
            if (!Schema::hasColumn('branches', 'code')) {
                $table->string('code', 10)->unique()->after('name');
            }
            
            if (!Schema::hasColumn('branches', 'branch_code')) {
                $table->string('branch_code', 10)->nullable()->unique()->after('code');
            }
            
            if (!Schema::hasColumn('branches', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('manager_id');
            }
            
            if (!Schema::hasColumn('branches', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('email');
            }
        });
        
        echo "✅ Fixed branches table structure\n";
    }

    private function addBranchIdToEmployees()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('department_id');
            $table->index('branch_id');
        });
        
        echo "✅ Added branch_id to employees table\n";
    }

    private function fixEmployeesBranchColumn()
    {
        // ตรวจสอบว่า column มี index หรือยัง
        $hasIndex = collect(DB::select("SHOW INDEX FROM employees WHERE Column_name = 'branch_id'"))->isNotEmpty();
        
        if (!$hasIndex) {
            Schema::table('employees', function (Blueprint $table) {
                $table->index('branch_id');
            });
            echo "✅ Added index to employees.branch_id\n";
        }
    }

    private function fixForeignKeyConstraints()
    {
        // ✅ Drop existing conflicting foreign keys first
        try {
            // ลบ foreign key เก่าที่อาจจะขัดแย้ง
            $existingForeignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'employees' 
                AND COLUMN_NAME = 'branch_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($existingForeignKeys as $fk) {
                DB::statement("ALTER TABLE employees DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                echo "✅ Dropped existing foreign key: {$fk->CONSTRAINT_NAME}\n";
            }
            
        } catch (\Exception $e) {
            // ไม่เป็นไร ถ้าไม่มี foreign key อยู่แล้ว
        }
        
        // ✅ Create proper foreign key constraints
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('set null')
                  ->name('fk_employees_branch_id');
        });
        
        // ✅ Create manager foreign key for branches (if employees table exists)
        if (Schema::hasTable('employees')) {
            try {
                Schema::table('branches', function (Blueprint $table) {
                    $table->foreign('manager_id')
                          ->references('id')
                          ->on('employees')
                          ->onDelete('set null')
                          ->name('fk_branches_manager_id');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
                echo "⚠️ Manager foreign key may already exist\n";
            }
        }
        
        echo "✅ Fixed foreign key constraints\n";
    }

    private function insertSampleBranches()
    {
        // ✅ Insert sample branches only if table is empty
        $branchCount = DB::table('branches')->count();
        
        if ($branchCount === 0) {
            $sampleBranches = [
                [
                    'name' => 'สำนักงานใหญ่',
                    'code' => 'HQ001',
                    'branch_code' => 'HQ001',
                    'description' => 'สำนักงานใหญ่ กรุงเทพมหานคร',
                    'address' => 'กรุงเทพมหานคร',
                    'phone' => '02-123-4567',
                    'email' => 'hq@bettersystem.co.th',
                    'is_active' => true,
                    'capacity' => 100,
                    'opening_date' => '2020-01-01',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'สาขาลาดพร้าว',
                    'code' => 'LP001',
                    'branch_code' => 'LP001', 
                    'description' => 'สาขาลาดพร้าว กรุงเทพมหานคร',
                    'address' => 'ลาดพร้าว กรุงเทพมหานคร',
                    'phone' => '02-234-5678',
                    'email' => 'ladprao@bettersystem.co.th',
                    'is_active' => true,
                    'capacity' => 50,
                    'opening_date' => '2021-06-01',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'สาขาสีลม',
                    'code' => 'SL001',
                    'branch_code' => 'SL001',
                    'description' => 'สาขาสีลม กรุงเทพมหานคร',
                    'address' => 'สีลม กรุงเทพมหานคร',
                    'phone' => '02-345-6789',
                    'email' => 'silom@bettersystem.co.th',
                    'is_active' => true,
                    'capacity' => 30,
                    'opening_date' => '2022-03-01',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];
            
            DB::table('branches')->insert($sampleBranches);
            echo "✅ Inserted " . count($sampleBranches) . " sample branches\n";
        } else {
            echo "✅ Branches table already has data ({$branchCount} records)\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ✅ Drop foreign keys first
        try {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeign('fk_employees_branch_id');
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }
        
        try {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropForeign('fk_branches_manager_id');
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }
        
        // ✅ Drop columns
        if (Schema::hasColumn('employees', 'branch_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
        
        // ⚠️ WARNING: Uncomment next line only if you want to drop branches table completely
        // Schema::dropIfExists('branches');
        
        echo "✅ Migration rolled back\n";
    }
};

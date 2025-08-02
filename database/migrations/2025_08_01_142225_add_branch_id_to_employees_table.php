<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "🔗 Adding branch_id to employees table...\n";
        
        try {
            // ตรวจสอบว่าตาราง employees และ branches มีอยู่
            if (!Schema::hasTable('employees')) {
                echo "❌ Table 'employees' not found!\n";
                return;
            }
            
            if (!Schema::hasTable('branches')) {
                echo "❌ Table 'branches' not found! Please run branches migration first.\n";
                return;
            }
            
            Schema::table('employees', function (Blueprint $table) {
                // เพิ่ม branch_id column
                if (!Schema::hasColumn('employees', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('department_id')
                          ->comment('สาขาที่พนักงานสังกัด');
                    echo "✅ Added branch_id column\n";
                } else {
                    echo "ℹ️ branch_id column already exists\n";
                }
            });
            
            // เพิ่ม foreign key constraint
            $this->addForeignKeyConstraint();
            
            // เพิ่ม index
            $this->addBranchIndex();
            
            // กำหนดสาขาเริ่มต้นให้พนักงานที่มีอยู่
            $this->assignDefaultBranches();
            
            echo "🎉 Branch ID added to employees successfully!\n";
            
        } catch (\Exception $e) {
            echo "❌ Migration failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Removing branch_id from employees table...\n";
        
        try {
            if (Schema::hasTable('employees')) {
                Schema::table('employees', function (Blueprint $table) {
                    // ลบ foreign key constraint ก่อน
                    try {
                        $table->dropForeign(['branch_id']);
                        echo "🗑️ Dropped foreign key constraint\n";
                    } catch (\Exception $e) {
                        echo "⚠️ Could not drop foreign key: " . $e->getMessage() . "\n";
                    }
                    
                    // ลบ index
                    try {
                        $table->dropIndex('idx_employees_branch_id');
                        echo "🗑️ Dropped branch_id index\n";
                    } catch (\Exception $e) {
                        echo "⚠️ Could not drop index: " . $e->getMessage() . "\n";
                    }
                    
                    // ลบ column
                    if (Schema::hasColumn('employees', 'branch_id')) {
                        $table->dropColumn('branch_id');
                        echo "🗑️ Removed branch_id column\n";
                    }
                });
            }
            
            echo "✅ Branch ID removal completed\n";
            
        } catch (\Exception $e) {
            echo "⚠️ Rollback warning: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * เพิ่ม foreign key constraint
     */
    private function addForeignKeyConstraint(): void
    {
        try {
            echo "🔗 Adding foreign key constraint...\n";
            
            Schema::table('employees', function (Blueprint $table) {
                $table->foreign('branch_id')
                      ->references('id')
                      ->on('branches')
                      ->onDelete('set null')
                      ->name('fk_employees_branch_id');
            });
            
            echo "✅ Foreign key constraint added\n";
            
        } catch (\Exception $e) {
            echo "⚠️ Could not add foreign key constraint: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * เพิ่ม index สำหรับ branch_id
     */
    private function addBranchIndex(): void
    {
        try {
            echo "🔍 Adding branch_id index...\n";
            
            // ตรวจสอบว่า index มีอยู่แล้วหรือไม่
            $indexes = DB::select("SHOW INDEX FROM employees WHERE Key_name = 'idx_employees_branch_id'");
            
            if (empty($indexes)) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->index('branch_id', 'idx_employees_branch_id');
                });
                echo "✅ Branch ID index added\n";
            } else {
                echo "ℹ️ Branch ID index already exists\n";
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Could not add index: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * กำหนดสาขาเริ่มต้นให้พนักงานที่มีอยู่
     */
    private function assignDefaultBranches(): void
    {
        try {
            echo "🏢 Assigning default branches to existing employees...\n";
            
            // ค้นหาสาขาสำนักงานใหญ่
            $headquarters = DB::table('branches')
                ->where('branch_code', 'HQ001')
                ->orWhere('name', 'like', '%สำนักงานใหญ่%')
                ->orWhere('name', 'like', '%headquarters%')
                ->first();
            
            if (!$headquarters) {
                // ถ้าไม่มีสำนักงานใหญ่ ใช้สาขาแรกที่มี status active
                $headquarters = DB::table('branches')
                    ->where('status', 'active')
                    ->orderBy('id')
                    ->first();
            }
            
            if ($headquarters) {
                // อัปเดตพนักงานที่ยังไม่มีสาขา
                $updated = DB::table('employees')
                    ->whereNull('branch_id')
                    ->whereNull('deleted_at')
                    ->update([
                        'branch_id' => $headquarters->id,
                        'updated_at' => now()
                    ]);
                
                if ($updated > 0) {
                    echo "✅ Assigned {$updated} employees to branch: {$headquarters->name}\n";
                } else {
                    echo "ℹ️ No employees need branch assignment\n";
                }
            } else {
                echo "⚠️ No active branch found to assign employees\n";
            }
            
            // แสดงสถิติการกระจายพนักงานตามสาขา
            $this->showBranchDistribution();
            
        } catch (\Exception $e) {
            echo "⚠️ Default branch assignment warning: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * แสดงสถิติการกระจายพนักงานตามสาขา
     */
    private function showBranchDistribution(): void
    {
        try {
            echo "📊 Employee distribution by branch:\n";
            
            $distribution = DB::table('employees')
                ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
                ->whereNull('employees.deleted_at')
                ->select(
                    'branches.name as branch_name',
                    'branches.branch_code',
                    DB::raw('COUNT(employees.id) as employee_count')
                )
                ->groupBy('branches.id', 'branches.name', 'branches.branch_code')
                ->orderBy('employee_count', 'desc')
                ->get();
            
            foreach ($distribution as $branch) {
                $branchName = $branch->branch_name ?: 'ไม่ระบุสาขา';
                $branchCode = $branch->branch_code ? "({$branch->branch_code})" : '';
                echo "   🏢 {$branchName} {$branchCode}: {$branch->employee_count} คน\n";
            }
            
            // นับพนักงานที่ไม่มีสาขา
            $noBreachCount = DB::table('employees')
                ->whereNull('branch_id')
                ->whereNull('deleted_at')
                ->count();
            
            if ($noBreachCount > 0) {
                echo "   ⚠️ ไม่ระบุสาขา: {$noBreachCount} คน\n";
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Could not show distribution: " . $e->getMessage() . "\n";
        }
    }
};

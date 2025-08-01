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
        echo "🚀 Adding Permission Fields to Employees Table...\n";
        
        try {
            // ตรวจสอบว่าตาราง employees มีอยู่
            if (!Schema::hasTable('employees')) {
                echo "❌ Table 'employees' not found!\n";
                return;
            }
            
            Schema::table('employees', function (Blueprint $table) {
                // เพิ่ม VPN Access Permission
                if (!Schema::hasColumn('employees', 'vpn_access')) {
                    $table->boolean('vpn_access')->default(false)->after('status')
                          ->comment('อนุญาตใช้งาน VPN');
                    echo "✅ Added vpn_access column\n";
                } else {
                    echo "ℹ️ vpn_access column already exists\n";
                }
                
                // เพิ่ม Color Printing Permission
                if (!Schema::hasColumn('employees', 'color_printing')) {
                    $table->boolean('color_printing')->default(false)->after('vpn_access')
                          ->comment('อนุญาตใช้งานเครื่องพิมพ์สี');
                    echo "✅ Added color_printing column\n";
                } else {
                    echo "ℹ️ color_printing column already exists\n";
                }
                
                // เพิ่ม Remote Work Permission (เพิ่มเติม)
                if (!Schema::hasColumn('employees', 'remote_work')) {
                    $table->boolean('remote_work')->default(false)->after('color_printing')
                          ->comment('อนุญาตทำงานจากที่บ้าน');
                    echo "✅ Added remote_work column\n";
                } else {
                    echo "ℹ️ remote_work column already exists\n";
                }
                
                // เพิ่ม Admin Panel Access (เพิ่มเติม)
                if (!Schema::hasColumn('employees', 'admin_access')) {
                    $table->boolean('admin_access')->default(false)->after('remote_work')
                          ->comment('อนุญาตเข้าถึงแผงควบคุมผู้ดูแลระบบ');
                    echo "✅ Added admin_access column\n";
                } else {
                    echo "ℹ️ admin_access column already exists\n";
                }
            });
            
            // เพิ่ม Indexes สำหรับประสิทธิภาพ
            $this->addPermissionIndexes();
            
            // ตั้งค่าเริ่มต้นสำหรับ Admin และ IT
            $this->setDefaultPermissions();
            
            echo "🎉 Permission fields added successfully!\n";
            echo "📋 Added fields: vpn_access, color_printing, remote_work, admin_access\n";
            
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
        echo "🔄 Rolling back permission fields...\n";
        
        try {
            if (Schema::hasTable('employees')) {
                Schema::table('employees', function (Blueprint $table) {
                    // ลบ indexes ก่อน
                    $this->dropPermissionIndexes($table);
                    
                    // ลบ columns
                    $columnsToRemove = ['vpn_access', 'color_printing', 'remote_work', 'admin_access'];
                    
                    foreach ($columnsToRemove as $column) {
                        if (Schema::hasColumn('employees', $column)) {
                            $table->dropColumn($column);
                            echo "🗑️ Removed {$column} column\n";
                        }
                    }
                });
            }
            
            echo "✅ Permission fields rollback completed\n";
            
        } catch (\Exception $e) {
            echo "⚠️ Rollback warning: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * เพิ่ม indexes สำหรับ permission fields
     */
    private function addPermissionIndexes(): void
    {
        try {
            echo "🔍 Adding permission indexes...\n";
            
            $indexesToAdd = [
                'vpn_access' => 'idx_employees_vpn_access',
                'color_printing' => 'idx_employees_color_printing', 
                'remote_work' => 'idx_employees_remote_work',
                'admin_access' => 'idx_employees_admin_access'
            ];
            
            foreach ($indexesToAdd as $column => $indexName) {
                if (Schema::hasColumn('employees', $column)) {
                    try {
                        // ตรวจสอบว่า index มีอยู่แล้วหรือไม่
                        $indexes = DB::select("SHOW INDEX FROM employees WHERE Key_name = ?", [$indexName]);
                        
                        if (empty($indexes)) {
                            Schema::table('employees', function (Blueprint $table) use ($column, $indexName) {
                                $table->index($column, $indexName);
                            });
                            echo "✅ Index {$indexName} added\n";
                        } else {
                            echo "ℹ️ Index {$indexName} already exists\n";
                        }
                    } catch (\Exception $e) {
                        echo "⚠️ Could not add index {$indexName}: " . $e->getMessage() . "\n";
                    }
                }
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Index creation warning: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * ลบ permission indexes
     */
    private function dropPermissionIndexes($table): void
    {
        $indexesToDrop = [
            'idx_employees_vpn_access',
            'idx_employees_color_printing',
            'idx_employees_remote_work', 
            'idx_employees_admin_access'
        ];
        
        foreach ($indexesToDrop as $indexName) {
            try {
                $table->dropIndex($indexName);
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
        }
    }
    
    /**
     * ตั้งค่าสิทธิ์เริ่มต้นสำหรับ Admin และ IT
     */
    private function setDefaultPermissions(): void
    {
        try {
            echo "🔐 Setting default permissions...\n";
            
            // ตั้งค่าสิทธิ์สำหรับ Super Admin และ IT Admin
            $adminUpdated = DB::table('employees')
                ->whereIn('role', ['super_admin', 'it_admin'])
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->update([
                    'vpn_access' => true,
                    'color_printing' => true, 
                    'remote_work' => true,
                    'admin_access' => true,
                    'updated_at' => now()
                ]);
                
            if ($adminUpdated > 0) {
                echo "✅ Updated permissions for {$adminUpdated} admin users\n";
            }
            
            // ตั้งค่าสิทธิ์สำหรับ HR และ Manager (บางสิทธิ์)
            $managerUpdated = DB::table('employees')
                ->whereIn('role', ['hr', 'manager'])
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->update([
                    'color_printing' => true,
                    'remote_work' => true,
                    'updated_at' => now()
                ]);
                
            if ($managerUpdated > 0) {
                echo "✅ Updated permissions for {$managerUpdated} manager/hr users\n";
            }
            
            // ตั้งค่าสิทธิ์พื้นฐานสำหรับ Express และ Employee
            $employeeUpdated = DB::table('employees')
                ->whereIn('role', ['express', 'employee'])
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->update([
                    'vpn_access' => false,
                    'color_printing' => false,
                    'remote_work' => false, 
                    'admin_access' => false,
                    'updated_at' => now()
                ]);
                
            if ($employeeUpdated > 0) {
                echo "✅ Updated permissions for {$employeeUpdated} regular employees\n";
            }
            
            echo "🎯 Default permissions set successfully!\n";
            
        } catch (\Exception $e) {
            echo "⚠️ Default permissions warning: " . $e->getMessage() . "\n";
        }
    }
};

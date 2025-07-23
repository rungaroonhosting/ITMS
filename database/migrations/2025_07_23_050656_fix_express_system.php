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
        // ===============================================
        // 1. แก้ไขตาราง employees (ถ้าจำเป็น)
        // ===============================================
        Schema::table('employees', function (Blueprint $table) {
            // เช็คและเพิ่ม columns ที่ยังไม่มี
            if (!Schema::hasColumn('employees', 'express_username')) {
                $table->string('express_username', 7)->nullable()->after('email_password');
            }
            
            if (!Schema::hasColumn('employees', 'express_password')) {
                $table->string('express_password', 4)->nullable()->after('express_username');
            }
        });

        // ===============================================
        // 2. เพิ่ม indexes แบบปลอดภัย
        // ===============================================
        try {
            // ตรวจสอบว่า index มีอยู่แล้วไหม
            $indexes = DB::select("SHOW INDEX FROM employees WHERE Key_name = 'employees_express_username_index'");
            
            if (empty($indexes)) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->index('express_username', 'employees_express_username_index');
                });
                echo "✅ สร้าง express_username index สำเร็จ\n";
            } else {
                echo "ℹ️ express_username index มีอยู่แล้ว\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ ไม่สามารถสร้าง index: " . $e->getMessage() . "\n";
        }

        // ===============================================
        // 3. เพิ่ม Express support ให้ตาราง departments
        // ===============================================
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'express_enabled')) {
                $table->boolean('express_enabled')->default(false)->after('name')
                      ->comment('เปิดใช้งาน Express สำหรับแผนกนี้');
            }
            
            if (!Schema::hasColumn('departments', 'express_auto_detect')) {
                $table->boolean('express_auto_detect')->default(true)->after('express_enabled')
                      ->comment('ตรวจจับแผนกบัญชีอัตโนมัติ');
            }
        });

        // ===============================================
        // 4. อัพเดตข้อมูลแผนกบัญชีอัตโนมัติ
        // ===============================================
        try {
            $accountingKeywords = ['บัญชี', 'การเงิน', 'accounting', 'finance'];
            
            foreach ($accountingKeywords as $keyword) {
                $updated = DB::table('departments')
                    ->where('name', 'like', "%{$keyword}%")
                    ->where('express_enabled', false) // อัพเดตเฉพาะที่ยังไม่เปิด
                    ->update(['express_enabled' => true]);
                
                if ($updated > 0) {
                    echo "✅ เปิด Express สำหรับแผนก '{$keyword}': {$updated} แผนก\n";
                }
            }
        } catch (\Exception $e) {
            echo "⚠️ ไม่สามารถอัพเดตแผนกอัตโนมัติ: " . $e->getMessage() . "\n";
        }

        // ===============================================
        // 5. สร้างข้อมูลตัวอย่าง (ถ้าต้องการ)
        // ===============================================
        try {
            $this->seedSampleData();
        } catch (\Exception $e) {
            echo "⚠️ ไม่สามารถสร้างข้อมูลตัวอย่าง: " . $e->getMessage() . "\n";
        }

        echo "🎉 Express System v2.0 ติดตั้งเรียบร้อยแล้ว!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ลบ Express features
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'express_enabled')) {
                $table->dropColumn('express_enabled');
            }
            if (Schema::hasColumn('departments', 'express_auto_detect')) {
                $table->dropColumn('express_auto_detect');
            }
        });

        // ลบ index (ถ้ามี)
        try {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropIndex('employees_express_username_index');
            });
        } catch (\Exception $e) {
            // Ignore if index doesn't exist
        }

        // เคลียร์ Express data
        DB::table('employees')->update([
            'express_username' => null,
            'express_password' => null
        ]);

        echo "🔄 Rollback Express System เรียบร้อยแล้ว\n";
    }

    /**
     * สร้างข้อมูลตัวอย่างสำหรับทดสอบ
     */
    private function seedSampleData()
    {
        // ตรวจสอบว่ามีแผนกบัญชีไหม ถ้าไม่มีให้สร้าง
        $accountingDept = DB::table('departments')->where('name', 'like', '%บัญชี%')->first();
        
        if (!$accountingDept) {
            DB::table('departments')->insert([
                'name' => 'แผนกบัญชี',
                'express_enabled' => true,
                'express_auto_detect' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ สร้างแผนกบัญชีตัวอย่าง\n";
        }

        // สร้างแผนกอื่นๆ ที่ไม่เปิด Express
        $otherDepts = [
            'แผนกไอที' => false,
            'แผนกขาย' => false,
            'แผนกการตลาด' => false,
            'แผนกทรัพยากรบุคคล' => false
        ];

        foreach ($otherDepts as $deptName => $expressEnabled) {
            $exists = DB::table('departments')->where('name', $deptName)->exists();
            
            if (!$exists) {
                DB::table('departments')->insert([
                    'name' => $deptName,
                    'express_enabled' => $expressEnabled,
                    'express_auto_detect' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        echo "✅ สร้างแผนกตัวอย่างเรียบร้อยแล้ว\n";
    }
};

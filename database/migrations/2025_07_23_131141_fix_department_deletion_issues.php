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
        // 1. เพิ่ม soft delete ถ้ายังไม่มี
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 2. เพิ่ม columns ที่จำเป็น
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'code')) {
                $table->string('code', 10)->after('name')->nullable();
            }
            
            if (!Schema::hasColumn('departments', 'description')) {
                $table->text('description')->nullable()->after('code');
            }
            
            if (!Schema::hasColumn('departments', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
            
            if (!Schema::hasColumn('departments', 'express_enabled')) {
                $table->boolean('express_enabled')->default(false)->after('is_active');
            }
            
            if (!Schema::hasColumn('departments', 'express_enabled_at')) {
                $table->timestamp('express_enabled_at')->nullable()->after('express_enabled');
            }
        });

        // 3. ล้างข้อมูล duplicate และปัญหา
        $this->cleanupDepartmentData();

        // 4. เพิ่ม unique constraints ที่รองรับ soft delete
        $this->addUniqueConstraints();

        // 5. สร้างข้อมูล code สำหรับแผนกที่ยังไม่มี
        $this->generateMissingCodes();

        echo "✅ Department deletion fix migration completed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ลบ unique constraints
        Schema::table('departments', function (Blueprint $table) {
            try {
                $table->dropUnique(['name', 'deleted_at']);
                $table->dropUnique(['code', 'deleted_at']);
            } catch (\Exception $e) {
                // Ignore if constraints don't exist
            }
        });

        // ไม่ลบ columns เพราะอาจมีข้อมูลสำคัญ
        echo "⚠️ Migration rollback completed (columns kept for data safety)\n";
    }

    /**
     * ล้างข้อมูลที่ duplicate และแก้ไขปัญหา
     */
    private function cleanupDepartmentData(): void
    {
        try {
            echo "🧹 Cleaning up department data...\n";

            // ล้างข้อมูล null values
            DB::table('departments')->whereNull('name')->delete();
            DB::update("UPDATE departments SET code = UPPER(LEFT(name, 3)) WHERE code IS NULL OR code = ''");
            DB::update("UPDATE departments SET is_active = 1 WHERE is_active IS NULL");
            DB::update("UPDATE departments SET express_enabled = 0 WHERE express_enabled IS NULL");

            // ค้นหาแผนกที่ชื่อซ้ำ
            $duplicateNames = DB::table('departments')
                ->select('name', DB::raw('COUNT(*) as count'))
                ->whereNull('deleted_at')
                ->groupBy('name')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicateNames as $duplicate) {
                echo "🔍 Found duplicate name: {$duplicate->name} ({$duplicate->count} records)\n";
                
                $departments = DB::table('departments')
                    ->where('name', $duplicate->name)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // เก็บแผนกแรก (ใหม่ที่สุด) ลบที่เหลือ
                $keep = $departments->first();
                $toDelete = $departments->skip(1);

                foreach ($toDelete as $dept) {
                    // ตรวจสอบว่ามีพนักงานไหม
                    $hasEmployees = DB::table('employees')
                        ->where('department_id', $dept->id)
                        ->exists();

                    if ($hasEmployees) {
                        // ย้ายพนักงานไปแผนกที่เก็บไว้
                        DB::table('employees')
                            ->where('department_id', $dept->id)
                            ->update(['department_id' => $keep->id]);
                        
                        echo "👥 Moved employees from duplicate department ID {$dept->id} to {$keep->id}\n";
                    }

                    // Soft delete แผนกที่ซ้ำ
                    DB::table('departments')
                        ->where('id', $dept->id)
                        ->update(['deleted_at' => now()]);
                    
                    echo "🗑️ Soft deleted duplicate department: {$dept->name} (ID: {$dept->id})\n";
                }
            }

            // แก้ไขรหัสแผนกที่ซ้ำ
            $duplicateCodes = DB::table('departments')
                ->select('code', DB::raw('COUNT(*) as count'))
                ->whereNull('deleted_at')
                ->whereNotNull('code')
                ->groupBy('code')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicateCodes as $duplicate) {
                echo "🔍 Found duplicate code: {$duplicate->code} ({$duplicate->count} records)\n";
                
                $departments = DB::table('departments')
                    ->where('code', $duplicate->code)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'asc')
                    ->get();

                // เก็บแผนกแรก ปรับรหัสของที่เหลือ
                $counter = 1;
                foreach ($departments->skip(1) as $dept) {
                    $newCode = $duplicate->code . $counter;
                    
                    // ตรวจสอบว่ารหัสใหม่ไม่ซ้ำ
                    while (DB::table('departments')
                           ->where('code', $newCode)
                           ->whereNull('deleted_at')
                           ->exists()) {
                        $counter++;
                        $newCode = $duplicate->code . $counter;
                    }
                    
                    DB::table('departments')
                        ->where('id', $dept->id)
                        ->update(['code' => $newCode]);
                    
                    echo "🔄 Updated duplicate code for department ID {$dept->id}: {$duplicate->code} → {$newCode}\n";
                    $counter++;
                }
            }

            echo "✅ Department data cleanup completed\n";

        } catch (\Exception $e) {
            echo "⚠️ Cleanup warning: " . $e->getMessage() . "\n";
            \Log::warning('Department cleanup error: ' . $e->getMessage());
        }
    }

    /**
     * เพิ่ม unique constraints ที่รองรับ soft delete
     */
    private function addUniqueConstraints(): void
    {
        try {
            echo "🔒 Adding unique constraints...\n";

            // ลบ unique constraints เดิมก่อน (ถ้ามี)
            Schema::table('departments', function (Blueprint $table) {
                try {
                    $table->dropUnique(['name']);
                } catch (\Exception $e) {
                    // Ignore if doesn't exist
                }
                
                try {
                    $table->dropUnique(['code']);
                } catch (\Exception $e) {
                    // Ignore if doesn't exist
                }
            });

            // เพิ่ม composite unique constraints ที่รองรับ soft delete
            DB::statement('CREATE UNIQUE INDEX departments_name_unique_not_deleted ON departments (name) WHERE deleted_at IS NULL');
            DB::statement('CREATE UNIQUE INDEX departments_code_unique_not_deleted ON departments (code) WHERE deleted_at IS NULL');

            echo "✅ Unique constraints added\n";

        } catch (\Exception $e) {
            echo "⚠️ Constraint warning: " . $e->getMessage() . "\n";
            \Log::warning('Failed to add unique constraints: ' . $e->getMessage());
        }
    }

    /**
     * สร้างรหัสแผนกสำหรับแผนกที่ไม่มี
     */
    private function generateMissingCodes(): void
    {
        try {
            echo "🏷️ Generating missing department codes...\n";

            $departmentsWithoutCode = DB::table('departments')
                ->whereNull('deleted_at')
                ->where(function($query) {
                    $query->whereNull('code')
                          ->orWhere('code', '');
                })
                ->get();

            foreach ($departmentsWithoutCode as $dept) {
                $name = $dept->name;
                
                // สร้างรหัสจากชื่อแผนก
                $code = strtoupper(substr($name, 0, 3));
                $code = preg_replace('/[^A-Z0-9]/', '', $code);
                
                // ตรวจสอบความซ้ำ
                $originalCode = $code;
                $counter = 1;
                
                while (DB::table('departments')
                       ->where('code', $code)
                       ->whereNull('deleted_at')
                       ->where('id', '!=', $dept->id)
                       ->exists()) {
                    $code = $originalCode . $counter;
                    $counter++;
                }

                DB::table('departments')
                    ->where('id', $dept->id)
                    ->update(['code' => $code]);

                echo "🏷️ Generated code for '{$name}': {$code}\n";
            }

            echo "✅ Missing codes generated\n";

        } catch (\Exception $e) {
            echo "⚠️ Code generation warning: " . $e->getMessage() . "\n";
            \Log::warning('Failed to generate codes: ' . $e->getMessage());
        }
    }
};

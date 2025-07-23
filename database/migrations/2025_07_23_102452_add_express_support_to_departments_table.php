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
        // ตรวจสอบและเพิ่ม columns ที่จำเป็น
        Schema::table('departments', function (Blueprint $table) {
            // เพิ่ม express_enabled ถ้ายังไม่มี
            if (!Schema::hasColumn('departments', 'express_enabled')) {
                $table->boolean('express_enabled')->default(false)->after('description');
            }
            
            // เพิ่ม express_enabled_at ถ้ายังไม่มี
            if (!Schema::hasColumn('departments', 'express_enabled_at')) {
                $table->timestamp('express_enabled_at')->nullable()->after('express_enabled');
            }
            
            // เพิ่ม is_active ถ้ายังไม่มี
            if (!Schema::hasColumn('departments', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        // ทำความสะอาดข้อมูลอย่างปลอดภัย
        try {
            // แก้ไขข้อมูล express_enabled ที่ไม่ใช่ boolean
            DB::statement("UPDATE departments SET express_enabled = 0 WHERE express_enabled IS NULL OR express_enabled = '' OR express_enabled NOT IN (0, 1)");
            
            // แก้ไขข้อมูล is_active ที่ไม่ใช่ boolean
            DB::statement("UPDATE departments SET is_active = 1 WHERE is_active IS NULL OR is_active = '' OR is_active NOT IN (0, 1)");
            
            // ตั้งค่า express_enabled_at สำหรับแผนกที่เปิด Express
            DB::statement("UPDATE departments SET express_enabled_at = NOW() WHERE express_enabled = 1 AND express_enabled_at IS NULL");
            
        } catch (\Exception $e) {
            // หากเกิดข้อผิดพลาดในการอัพเดตข้อมูล ให้แสดง warning แต่ไม่หยุด migration
            \Log::warning('Migration data cleanup warning: ' . $e->getMessage());
        }

        // ตรวจสอบว่ามี column ที่ถูกต้องแล้ว
        $columns = Schema::getColumnListing('departments');
        \Log::info('Departments table columns after migration: ' . implode(', ', $columns));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // ลบ columns ที่เพิ่มไป (ระวัง: อาจทำให้ข้อมูลหายไป)
            if (Schema::hasColumn('departments', 'express_enabled_at')) {
                $table->dropColumn('express_enabled_at');
            }
            
            // ไม่ลบ express_enabled และ is_active เพราะอาจมีข้อมูลสำคัญ
            // $table->dropColumn(['express_enabled']);
        });
    }
};

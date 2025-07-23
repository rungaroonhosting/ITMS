<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // ตรวจสอบก่อนเพิ่ม column
            if (!Schema::hasColumn('departments', 'express_enabled')) {
                $table->boolean('express_enabled')->default(false)->after('name');
            }
            
            if (!Schema::hasColumn('departments', 'express_auto_detect')) {
                $table->boolean('express_auto_detect')->default(true)->after('express_enabled');
            }
        });

        // ✅ อัพเดตแผนกบัญชีให้เปิด Express อัตโนมัติ
        try {
            DB::table('departments')
                ->where('name', 'like', '%บัญชี%')
                ->orWhere('name', 'like', '%การเงิน%')
                ->orWhere('name', 'like', '%accounting%')
                ->orWhere('name', 'like', '%finance%')
                ->update(['express_enabled' => true]);
                
            \Log::info('Auto-enabled Express for accounting departments');
        } catch (\Exception $e) {
            \Log::warning('Could not auto-enable Express: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'express_enabled')) {
                $table->dropColumn('express_enabled');
            }
            
            if (Schema::hasColumn('departments', 'express_auto_detect')) {
                $table->dropColumn('express_auto_detect');
            }
        });
    }
};

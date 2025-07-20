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
        // เพิ่ม field ให้ตาราง departments (ถ้ายังไม่มี)
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'code')) {
                $table->string('code', 10)->unique()->after('name')->comment('รหัสแผนก');
            }
            if (!Schema::hasColumn('departments', 'color')) {
                $table->string('color', 20)->default('primary')->after('code')->comment('สีของแผนก');
            }
            if (!Schema::hasColumn('departments', 'description')) {
                $table->text('description')->nullable()->after('color')->comment('คำอธิบายแผนก');
            }
            if (!Schema::hasColumn('departments', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description')->comment('สถานะใช้งาน');
            }
        });

        // เพิ่ม field ให้ตาราง employees สำหรับ Express
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'express_username')) {
                $table->string('express_username', 7)->nullable()->after('email_password')->comment('Username Express (แผนกบัญชี)');
            }
            if (!Schema::hasColumn('employees', 'express_password')) {
                $table->string('express_password', 4)->nullable()->after('express_username')->comment('Password Express (แผนกบัญชี)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'code')) {
                $table->dropColumn('code');
            }
            if (Schema::hasColumn('departments', 'color')) {
                $table->dropColumn('color');
            }
            if (Schema::hasColumn('departments', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('departments', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'express_username')) {
                $table->dropColumn('express_username');
            }
            if (Schema::hasColumn('employees', 'express_password')) {
                $table->dropColumn('express_password');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // เพิ่ม columns ใหม่สำหรับ authentication
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'password_reset_token')) {
                $table->string('password_reset_token')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'password_reset_expires_at')) {
                $table->timestamp('password_reset_expires_at')->nullable()->after('password_reset_token');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
            
            // อัปเดต role enum ถ้ายังไม่ได้ตั้งค่า
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'it_admin', 'employee'])->default('employee')->after('password');
            }
            
            // เพิ่ม permissions column ถ้ายังไม่มี
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('role');
            }
            
            // เพิ่ม employee_id column ถ้ายังไม่มี
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('permissions');
            }
            
            // เพิ่ม is_active column ถ้ายังไม่มี
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('employee_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'password_reset_token', 
                'password_reset_expires_at',
                'permissions',
                'employee_id',
                'is_active'
            ]);
            $table->dropSoftDeletes();
        });
    }
};

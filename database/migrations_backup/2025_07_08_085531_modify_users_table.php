<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('email');
            $table->json('permissions')->nullable()->after('role');
            $table->unsignedBigInteger('employee_id')->nullable()->after('permissions');
            $table->boolean('is_active')->default(true)->after('employee_id');
            
            // ไม่ต้องเพิ่ม foreign key constraint ที่นี่
            // จะเพิ่มใน migration add_foreign_keys แทน
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'permissions',
                'employee_id',
                'is_active'
            ]);
        });
    }
};

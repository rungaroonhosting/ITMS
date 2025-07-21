<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // เพิ่มเฉพาะ Express fields
            if (!Schema::hasColumn('employees', 'express_username')) {
                $table->string('express_username', 7)->nullable();
            }
            
            if (!Schema::hasColumn('employees', 'express_password')) {
                $table->string('express_password', 10)->nullable();
            }
            
            // indexes เฉพาะที่มี column จริง
            try {
                $table->index('express_username');
                $table->index('status');
                $table->index('department_id'); // ใช้ department_id
            } catch (\Exception $e) {
                // ignore if exists
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['express_username', 'express_password']);
        });
    }
};

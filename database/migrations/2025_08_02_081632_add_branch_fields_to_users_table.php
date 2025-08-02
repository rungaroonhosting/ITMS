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
        Schema::table('users', function (Blueprint $table) {
            // เพิ่มฟิลด์สำหรับสาขา
            $table->unsignedBigInteger('branch_id')->nullable()->after('email')->comment('สาขาที่สังกัด');
            $table->unsignedBigInteger('managed_branch_id')->nullable()->after('branch_id')->comment('สาขาที่เป็นผู้จัดการ');

            // เพิ่ม indexes
            $table->index('branch_id');
            $table->index('managed_branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['managed_branch_id']);
            $table->dropColumn(['branch_id', 'managed_branch_id']);
        });
    }
};

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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code', 10)->unique()->comment('รหัสสาขา');
            $table->string('name', 100)->comment('ชื่อสาขา');
            $table->text('address')->nullable()->comment('ที่อยู่');
            $table->string('phone', 20)->nullable()->comment('เบอร์โทรศัพท์');
            $table->string('email', 100)->nullable()->comment('อีเมล');
            $table->unsignedBigInteger('manager_id')->nullable()->comment('ผู้จัดการสาขา');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('สถานะ');
            $table->integer('capacity')->nullable()->comment('กำลังการผลิต (จำนวนพนักงานสูงสุด)');
            $table->decimal('area_sqm', 8, 2)->nullable()->comment('พื้นที่ (ตารางเมตร)');
            $table->date('opening_date')->nullable()->comment('วันที่เปิดสาขา');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['status'], 'idx_branches_status');
            $table->index(['branch_code'], 'idx_branches_code');
            $table->index(['name'], 'idx_branches_name');
            $table->index(['manager_id'], 'idx_branches_manager');
            $table->index(['created_at'], 'idx_branches_created');

            // Note: Foreign key constraint for manager_id will be added after employees table exists
            // in a separate migration file
        });

        // Add comment to table
        DB::statement("ALTER TABLE `branches` COMMENT = 'ตารางข้อมูลสาขาต่างๆ ของบริษัท'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};

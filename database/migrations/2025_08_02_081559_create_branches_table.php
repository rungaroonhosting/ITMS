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
            $table->string('name')->comment('ชื่อสาขา');
            $table->string('code', 10)->unique()->comment('รหัสสาขา');
            $table->text('description')->nullable()->comment('คำอธิบาย');
            $table->text('address')->nullable()->comment('ที่อยู่');
            $table->string('phone', 20)->nullable()->comment('เบอร์โทร');
            $table->string('email')->nullable()->comment('อีเมล');
            $table->unsignedBigInteger('manager_id')->nullable()->comment('ผู้จัดการสาขา');
            $table->boolean('is_active')->default(true)->comment('สถานะการใช้งาน');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_active');
            $table->index('manager_id');
            $table->index(['name', 'code']);

            // Foreign key constraint (will be added after users table exists)
            // $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};

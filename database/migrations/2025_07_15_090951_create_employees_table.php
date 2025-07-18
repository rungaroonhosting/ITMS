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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('employee_code', 20)->unique();
            $table->string('keycard_id', 20)->unique();
            $table->string('first_name_th', 100);
            $table->string('last_name_th', 100);
            $table->string('first_name_en', 100);
            $table->string('last_name_en', 100);
            $table->string('phone', 20)->unique();
            $table->string('nickname', 50)->nullable();
            
            // Computer System
            $table->string('username', 100)->unique();
            $table->string('computer_password')->nullable();
            $table->string('copier_code', 10)->nullable();
            
            // Email System
            $table->string('email')->unique();
            $table->string('login_email')->unique();
            $table->string('email_password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            
            // Express Program (Conditional)
            $table->string('express_username', 7)->nullable();
            $table->string('express_code', 4)->nullable();
            
            // Department and Role
            $table->unsignedBigInteger('department_id');
            $table->string('position', 100);
            $table->enum('role', ['super_admin', 'it_admin', 'hr', 'manager', 'express', 'employee'])->default('employee');
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            // Authentication
            $table->string('password');
            $table->rememberToken();
            
            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            
            // Indexes
            $table->index('employee_code');
            $table->index('email');
            $table->index('login_email');
            $table->index('status');
            $table->index('role');
            $table->index('department_id');
            $table->index(['first_name_th', 'last_name_th']);
            $table->index(['first_name_en', 'last_name_en']);
            $table->index('username');
            $table->index('keycard_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

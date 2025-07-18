<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('assigned_by');
            $table->date('assigned_date');
            $table->date('returned_date')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->text('assignment_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->enum('condition_on_assignment', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->enum('condition_on_return', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->timestamps();

            $table->index(['asset_id', 'assigned_date']);
            $table->index(['employee_id', 'assigned_date']);
            $table->index(['assigned_date']);
            $table->index(['returned_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};

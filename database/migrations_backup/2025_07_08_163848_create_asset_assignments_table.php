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
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('employees')->onDelete('restrict');
            $table->date('assigned_date');
            $table->date('returned_date')->nullable();
            $table->foreignId('returned_by')->nullable()->constrained('employees')->onDelete('set null');
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

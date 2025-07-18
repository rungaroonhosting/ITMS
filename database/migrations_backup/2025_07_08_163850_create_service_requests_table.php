<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 50)->unique();
            $table->string('title');
            $table->longText('description');
            $table->foreignId('type_id')->constrained('service_request_types')->onDelete('restrict');
            $table->foreignId('requested_by')->constrained('employees')->onDelete('restrict');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->longText('approval_notes')->nullable();
            $table->longText('completion_notes')->nullable();
            $table->longText('rejection_reason')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['request_number']);
            $table->index(['type_id', 'status']);
            $table->index(['requested_by']);
            $table->index(['assigned_to']);
            $table->index(['approved_by']);
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['requested_at']);
            $table->index(['due_date']);
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number', 50)->unique();
            $table->string('title');
            $table->longText('description');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('priority_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('reported_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->timestamp('reported_at');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->longText('resolution_notes')->nullable();
            $table->enum('impact', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('location')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('downtime_minutes')->nullable();
            $table->timestamps();

            $table->index(['incident_number']);
            $table->index(['type_id', 'status_id']);
            $table->index(['priority_id', 'status_id']);
            $table->index(['reported_by']);
            $table->index(['assigned_to']);
            $table->index(['asset_id']);
            $table->index(['reported_at']);
            $table->index(['due_date']);
            $table->index(['resolved_at']);
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};

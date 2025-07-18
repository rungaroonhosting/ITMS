<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('employees')->onDelete('restrict');
            $table->enum('action_type', ['created', 'updated', 'approved', 'rejected', 'assigned', 'started', 'completed', 'cancelled', 'commented']);
            $table->longText('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();

            $table->index(['service_request_id', 'created_at']);
            $table->index(['user_id']);
            $table->index(['action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_logs');
    }
};

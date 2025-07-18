<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('action_type', ['created', 'updated', 'assigned', 'status_changed', 'priority_changed', 'commented', 'resolved', 'closed']);
            $table->longText('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();

            $table->index(['incident_id', 'created_at']);
            $table->index(['user_id']);
            $table->index(['action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_logs');
    }
};

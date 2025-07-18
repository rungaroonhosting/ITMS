<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_request_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6c757d');
            $table->integer('sla_hours')->default(72);
            $table->boolean('requires_approval')->default(false);
            $table->unsignedBigInteger('default_assignee')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['requires_approval']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_types');
    }
};

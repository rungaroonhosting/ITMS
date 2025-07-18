<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('asset_categories')->onDelete('restrict');
            $table->foreignId('status_id')->constrained('asset_statuses')->onDelete('restrict');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_start_date')->nullable();
            $table->date('warranty_end_date')->nullable();
            $table->string('supplier')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('assigned_date')->nullable();
            $table->json('specifications')->nullable(); // JSON field for flexible specs
            $table->text('notes')->nullable();
            $table->string('qr_code')->nullable();
            $table->decimal('current_value', 12, 2)->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();

            $table->index(['asset_tag']);
            $table->index(['category_id', 'status_id']);
            $table->index(['assigned_to']);
            $table->index(['purchase_date']);
            $table->index(['warranty_end_date']);
            $table->index(['next_maintenance_date']);
            $table->fullText(['name', 'description', 'brand', 'model', 'serial_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

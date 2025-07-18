<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agreement_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_id')->constrained()->onDelete('cascade');
            $table->date('old_end_date');
            $table->date('new_end_date');
            $table->decimal('old_value', 12, 2)->nullable();
            $table->decimal('new_value', 12, 2)->nullable();
            $table->foreignId('renewed_by')->constrained('employees')->onDelete('restrict');
            $table->timestamp('renewed_at');
            $table->text('renewal_notes')->nullable();
            $table->timestamps();

            $table->index(['agreement_id', 'renewed_at']);
            $table->index(['renewed_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agreement_renewals');
    }
};

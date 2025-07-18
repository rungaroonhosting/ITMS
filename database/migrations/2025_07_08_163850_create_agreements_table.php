<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->string('agreement_number', 50)->unique();
            $table->string('title');
            $table->longText('description');
            $table->unsignedBigInteger('type_id');
            $table->string('vendor_name');
            $table->string('vendor_contact_person')->nullable();
            $table->string('vendor_email')->nullable();
            $table->string('vendor_phone', 20)->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('value', 12, 2)->nullable();
            $table->string('currency', 3)->default('THB');
            $table->enum('payment_terms', ['monthly', 'quarterly', 'semi_annually', 'annually', 'one_time'])->nullable();
            $table->enum('status', ['draft', 'active', 'expired', 'terminated', 'renewed'])->default('draft');
            $table->boolean('auto_renewal')->default(false);
            $table->integer('renewal_notice_days')->default(30);
            $table->longText('terms_and_conditions')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            $table->index(['agreement_number']);
            $table->index(['type_id', 'status']);
            $table->index(['owner_id']);
            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['end_date']);
            $table->fullText(['title', 'description', 'vendor_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};

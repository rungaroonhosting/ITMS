<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('employees', 'express_username')) {
                $table->string('express_username', 7)->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('employees', 'express_password')) {
                $table->string('express_password', 10)->nullable()->after('express_username');
            }
            
            if (!Schema::hasColumn('employees', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
            
            // Add indexes for better performance
            if (!Schema::hasColumn('employees', 'express_username')) {
                $table->index('express_username');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['express_username', 'express_password']);
            $table->dropSoftDeletes();
        });
    }
};

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
        // Add foreign key constraint for branches.manager_id
        Schema::table('branches', function (Blueprint $table) {
            $table->foreign('manager_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null')
                  ->name('fk_branches_manager_id');
        });

        // Add foreign key constraints for users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('set null')
                  ->name('fk_users_branch_id');

            $table->foreign('managed_branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('set null')
                  ->name('fk_users_managed_branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_users_branch_id');
            $table->dropForeign('fk_users_managed_branch_id');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign('fk_branches_manager_id');
        });
    }
};

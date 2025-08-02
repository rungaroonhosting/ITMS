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
            // Check if branch_id column doesn't exist before adding
            if (!Schema::hasColumn('employees', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('department_id')->comment('สาขาที่สังกัด');
                
                // Add index for better performance
                $table->index(['branch_id'], 'idx_employees_branch');
            }
        });

        // Add foreign key constraints after both tables exist
        if (Schema::hasTable('branches') && Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                // Add foreign key constraint for branch_id if it doesn't exist
                if (!$this->foreignKeyExists('employees', 'employees_branch_id_foreign')) {
                    $table->foreign('branch_id', 'employees_branch_id_foreign')
                          ->references('id')
                          ->on('branches')
                          ->onDelete('set null')
                          ->onUpdate('cascade');
                }
            });

            // Add foreign key constraint for manager_id in branches table if it doesn't exist
            Schema::table('branches', function (Blueprint $table) {
                if (!$this->foreignKeyExists('branches', 'branches_manager_id_foreign')) {
                    $table->foreign('manager_id', 'branches_manager_id_foreign')
                          ->references('id')
                          ->on('employees')
                          ->onDelete('set null')
                          ->onUpdate('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop foreign key first if it exists
            if ($this->foreignKeyExists('employees', 'employees_branch_id_foreign')) {
                $table->dropForeign('employees_branch_id_foreign');
            }
            
            // Drop the column if it exists
            if (Schema::hasColumn('employees', 'branch_id')) {
                $table->dropColumn('branch_id');
            }
        });

        // Drop foreign key from branches table
        Schema::table('branches', function (Blueprint $table) {
            if ($this->foreignKeyExists('branches', 'branches_manager_id_foreign')) {
                $table->dropForeign('branches_manager_id_foreign');
            }
        });
    }

    /**
     * Helper method to check if foreign key exists
     */
    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        $schema = Schema::getConnection()->getDoctrineSchemaManager();
        $foreignKeys = $schema->listTableForeignKeys($table);
        
        foreach ($foreignKeys as $key) {
            if ($key->getName() === $foreignKey) {
                return true;
            }
        }
        
        return false;
    }
};

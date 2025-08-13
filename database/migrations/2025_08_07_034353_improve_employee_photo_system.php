<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add new photo columns for better management
            // ✅ FIXED: Use 'photo' column (existing) instead of 'photo_path'
            if (!Schema::hasColumn('employees', 'photo_original_name')) {
                $table->string('photo_original_name')->nullable()->after('photo');
            }
            
            if (!Schema::hasColumn('employees', 'photo_mime_type')) {
                $table->string('photo_mime_type')->nullable()->after('photo_original_name');
            }
            
            if (!Schema::hasColumn('employees', 'photo_size')) {
                $table->integer('photo_size')->nullable()->after('photo_mime_type');
            }
            
            if (!Schema::hasColumn('employees', 'photo_uploaded_at')) {
                $table->timestamp('photo_uploaded_at')->nullable()->after('photo_size');
            }
            
            // Add login_password column if not exists
            if (!Schema::hasColumn('employees', 'login_password')) {
                $table->string('login_password')->nullable()->after('email_password');
            }
        });

        // ✅ FIXED: Add indexes safely (check if they exist first)
        $this->addIndexSafely('employees', 'photo', 'employees_photo_index');
        $this->addIndexSafely('employees', 'employee_code', 'employees_employee_code_index');
        $this->addIndexSafely('employees', ['status', 'role'], 'employees_status_role_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop columns if they exist
            $columnsToRemove = [
                'photo_original_name',
                'photo_mime_type', 
                'photo_size',
                'photo_uploaded_at'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Drop login_password if we added it
            if (Schema::hasColumn('employees', 'login_password')) {
                $table->dropColumn('login_password');
            }
        });
        
        // Drop indexes safely
        $this->dropIndexSafely('employees', 'employees_photo_index');
        $this->dropIndexSafely('employees', 'employees_employee_code_index');
        $this->dropIndexSafely('employees', 'employees_status_role_index');
    }

    /**
     * ✅ Helper function to add index safely
     */
    private function addIndexSafely($table, $columns, $indexName)
    {
        try {
            $indexExists = collect(DB::select("SHOW INDEX FROM {$table}"))->contains('Key_name', $indexName);
            
            if (!$indexExists) {
                if (is_array($columns)) {
                    DB::statement("ALTER TABLE {$table} ADD INDEX {$indexName} (" . implode(',', $columns) . ")");
                } else {
                    DB::statement("ALTER TABLE {$table} ADD INDEX {$indexName} ({$columns})");
                }
                echo "✅ Created index: {$indexName}\n";
            } else {
                echo "ℹ️  Index already exists: {$indexName}\n";
            }
        } catch (\Exception $e) {
            echo "⚠️  Could not create index {$indexName}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * ✅ Helper function to drop index safely
     */
    private function dropIndexSafely($table, $indexName)
    {
        try {
            $indexExists = collect(DB::select("SHOW INDEX FROM {$table}"))->contains('Key_name', $indexName);
            
            if ($indexExists) {
                DB::statement("ALTER TABLE {$table} DROP INDEX {$indexName}");
                echo "✅ Dropped index: {$indexName}\n";
            }
        } catch (\Exception $e) {
            echo "⚠️  Could not drop index {$indexName}: " . $e->getMessage() . "\n";
        }
    }
};

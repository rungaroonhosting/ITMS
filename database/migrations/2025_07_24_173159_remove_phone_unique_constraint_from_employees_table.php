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
        try {
            // ตรวจสอบว่ามี unique constraint อยู่หรือไม่
            $indexExists = false;
            
            // วิธีตรวจสอบ unique constraint
            if (Schema::hasTable('employees')) {
                $indexes = DB::select("SHOW INDEX FROM employees WHERE Key_name LIKE '%phone%' AND Non_unique = 0");
                $indexExists = !empty($indexes);
            }
            
            if ($indexExists) {
                echo "🔍 Found phone unique constraint, removing...\n";
                
                // ลบ unique constraint จาก phone field
                Schema::table('employees', function (Blueprint $table) {
                    try {
                        // ลองลบ unique constraint หลายชื่อที่เป็นไปได้
                        $table->dropUnique(['phone']);
                    } catch (\Exception $e) {
                        // ถ้าไม่สามารถลบได้ด้วยชื่อ array ให้ลองใช้ชื่อ index
                        try {
                            $table->dropIndex('employees_phone_unique');
                        } catch (\Exception $e2) {
                            // ลองชื่ออื่นๆ ที่เป็นไปได้
                            try {
                                $table->dropIndex('phone_unique');
                            } catch (\Exception $e3) {
                                echo "⚠️ Warning: Could not drop phone unique constraint: " . $e3->getMessage() . "\n";
                            }
                        }
                    }
                });
                
                echo "✅ Phone unique constraint removed successfully!\n";
            } else {
                echo "ℹ️ No phone unique constraint found, skipping...\n";
            }
            
            // ตรวจสอบผลลัพธ์
            $remainingIndexes = DB::select("SHOW INDEX FROM employees WHERE Key_name LIKE '%phone%' AND Non_unique = 0");
            
            if (empty($remainingIndexes)) {
                echo "✅ Phone field is now NOT UNIQUE - duplicate phone numbers are allowed!\n";
                \Log::info('Phone unique constraint removed successfully - duplicate phone numbers now allowed');
            } else {
                echo "⚠️ Some phone constraints may still exist\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ Migration error: " . $e->getMessage() . "\n";
            \Log::error('Phone unique constraint removal failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            // เพิ่ม unique constraint กลับคืน (ถ้าต้องการ rollback)
            Schema::table('employees', function (Blueprint $table) {
                // ตรวจสอบว่าไม่มี duplicate ก่อนเพิ่ม unique
                $duplicates = DB::table('employees')
                    ->select('phone', DB::raw('COUNT(*) as count'))
                    ->whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->groupBy('phone')
                    ->havingRaw('COUNT(*) > 1')
                    ->get();
                
                if ($duplicates->isEmpty()) {
                    $table->unique('phone');
                    echo "🔄 Phone unique constraint restored\n";
                } else {
                    echo "⚠️ Cannot restore unique constraint - duplicate phone numbers exist:\n";
                    foreach ($duplicates as $dup) {
                        echo "   Phone: {$dup->phone} (Count: {$dup->count})\n";
                    }
                    echo "💡 Please resolve duplicates before rolling back this migration\n";
                }
            });
            
        } catch (\Exception $e) {
            echo "❌ Rollback error: " . $e->getMessage() . "\n";
            \Log::error('Phone unique constraint rollback failed: ' . $e->getMessage());
        }
    }
};

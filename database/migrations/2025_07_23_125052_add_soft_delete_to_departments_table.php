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
        Schema::table('departments', function (Blueprint $table) {
            // เพิ่ม soft delete ถ้ายังไม่มี
            if (!Schema::hasColumn('departments', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // ล้างข้อมูล duplicate ที่อาจเหลืออยู่
        $this->cleanDuplicateData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * ล้างข้อมูลที่ duplicate อยู่
     */
    private function cleanDuplicateData(): void
    {
        try {
            // ค้นหา duplicate names
            $duplicateNames = \DB::table('departments')
                ->select('name')
                ->groupBy('name')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('name');

            foreach ($duplicateNames as $name) {
                $departments = \DB::table('departments')
                    ->where('name', $name)
                    ->orderBy('created_at', 'desc')
                    ->get();

                // เก็บแผนกล่าสุด ลบที่เหลือ
                $keep = $departments->first();
                $toDelete = $departments->skip(1);

                foreach ($toDelete as $dept) {
                    // ตรวจสอบว่ามีพนักงานหรือไม่
                    $hasEmployees = \DB::table('employees')
                        ->where('department_id', $dept->id)
                        ->exists();

                    if (!$hasEmployees) {
                        \DB::table('departments')
                            ->where('id', $dept->id)
                            ->delete();
                        
                        \Log::info("Deleted duplicate department: {$dept->name} (ID: {$dept->id})");
                    } else {
                        // ถ้ามีพนักงาน ให้ย้ายไปแผนกที่เก็บไว้
                        \DB::table('employees')
                            ->where('department_id', $dept->id)
                            ->update(['department_id' => $keep->id]);
                        
                        \DB::table('departments')
                            ->where('id', $dept->id)
                            ->delete();
                        
                        \Log::info("Merged employees and deleted duplicate department: {$dept->name} (ID: {$dept->id})");
                    }
                }
            }

            // ค้นหา duplicate codes
            $duplicateCodes = \DB::table('departments')
                ->select('code')
                ->groupBy('code')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('code');

            foreach ($duplicateCodes as $code) {
                $departments = \DB::table('departments')
                    ->where('code', $code)
                    ->orderBy('created_at', 'desc')
                    ->get();

                // เก็บแผนกล่าสุด ลบที่เหลือ
                $keep = $departments->first();
                $toDelete = $departments->skip(1);

                foreach ($toDelete as $dept) {
                    // ตรวจสอบว่ามีพนักงานหรือไม่
                    $hasEmployees = \DB::table('employees')
                        ->where('department_id', $dept->id)
                        ->exists();

                    if (!$hasEmployees) {
                        \DB::table('departments')
                            ->where('id', $dept->id)
                            ->delete();
                        
                        \Log::info("Deleted duplicate department code: {$dept->code} (ID: {$dept->id})");
                    } else {
                        // ถ้ามีพนักงาน ให้ย้ายไปแผนกที่เก็บไว้
                        \DB::table('employees')
                            ->where('department_id', $dept->id)
                            ->update(['department_id' => $keep->id]);
                        
                        \DB::table('departments')
                            ->where('id', $dept->id)
                            ->delete();
                        
                        \Log::info("Merged employees and deleted duplicate department code: {$dept->code} (ID: {$dept->id})");
                    }
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to clean duplicate departments: ' . $e->getMessage());
        }
    }
};

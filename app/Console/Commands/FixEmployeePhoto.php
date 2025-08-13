<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FixEmployeePhoto extends Command
{
    protected $signature = 'photo:fix {employee_id?} {--all}';
    protected $description = 'Fix employee photo issues';

    public function handle()
    {
        if ($this->option('all')) {
            return $this->fixAllEmployeePhotos();
        }

        $employeeId = $this->argument('employee_id');
        if (!$employeeId) {
            $this->error('Please provide employee ID or use --all flag');
            return;
        }

        return $this->fixSingleEmployeePhoto($employeeId);
    }

    private function fixSingleEmployeePhoto($employeeId)
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            $this->error("Employee {$employeeId} not found");
            return;
        }

        $this->info("🔍 Checking Employee {$employeeId}: {$employee->full_name_th}");
        
        // ข้อมูลปัจจุบัน
        $this->info("📊 Current Data:");
        $this->info("  Photo in DB: " . ($employee->photo ?? 'NULL'));
        $this->info("  Has Photo: " . ($employee->has_photo ? 'YES' : 'NO'));
        $this->info("  File Exists: " . ($employee->photoFileExists() ? 'YES' : 'NO'));

        // ลองหาไฟล์รูปภาพ
        $this->info("🔎 Searching for photo files...");
        $foundFiles = $this->findEmployeePhotos($employeeId);
        
        if (empty($foundFiles)) {
            $this->warn("❌ No photo files found for employee {$employeeId}");
            
            // ลบข้อมูลรูปภาพในฐานข้อมูลถ้าไฟล์ไม่มี
            if ($employee->photo) {
                $employee->update([
                    'photo' => null,
                    'photo_original_name' => null,
                    'photo_mime_type' => null,
                    'photo_size' => null,
                    'photo_uploaded_at' => null,
                ]);
                $this->info("🧹 Cleared invalid photo data from database");
            }
            return;
        }

        $this->info("✅ Found " . count($foundFiles) . " photo file(s):");
        foreach ($foundFiles as $file) {
            $this->info("  📁 {$file}");
        }

        // ใช้ไฟล์แรกที่พบ
        $selectedFile = $foundFiles[0];
        $this->info("📌 Using: {$selectedFile}");

        // อัพเดตฐานข้อมูล
        try {
            $fullPath = storage_path('app/public/' . $selectedFile);
            $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
            $mimeType = file_exists($fullPath) ? mime_content_type($fullPath) : null;

            $employee->update([
                'photo' => $selectedFile,
                'photo_original_name' => basename($selectedFile),
                'photo_mime_type' => $mimeType,
                'photo_size' => $fileSize,
                'photo_uploaded_at' => now(),
            ]);

            $this->info("✅ Successfully updated employee {$employeeId} photo data");
            $this->info("📸 Photo URL: " . $employee->fresh()->photo_url);

        } catch (\Exception $e) {
            $this->error("❌ Failed to update employee {$employeeId}: " . $e->getMessage());
        }
    }

    private function fixAllEmployeePhotos()
    {
        $this->info("🚀 Fixing all employee photos...");
        
        $employees = Employee::all();
        $fixed = 0;
        $errors = 0;

        foreach ($employees as $employee) {
            try {
                $foundFiles = $this->findEmployeePhotos($employee->id);
                
                if (!empty($foundFiles)) {
                    $selectedFile = $foundFiles[0];
                    $fullPath = storage_path('app/public/' . $selectedFile);
                    $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                    $mimeType = file_exists($fullPath) ? mime_content_type($fullPath) : null;

                    $employee->update([
                        'photo' => $selectedFile,
                        'photo_original_name' => basename($selectedFile),
                        'photo_mime_type' => $mimeType,
                        'photo_size' => $fileSize,
                        'photo_uploaded_at' => now(),
                    ]);

                    $this->info("✅ Fixed Employee {$employee->id}: {$selectedFile}");
                    $fixed++;
                } else {
                    // ลบข้อมูลรูปภาพที่ไม่มีไฟล์
                    if ($employee->photo) {
                        $employee->update([
                            'photo' => null,
                            'photo_original_name' => null,
                            'photo_mime_type' => null,
                            'photo_size' => null,
                            'photo_uploaded_at' => null,
                        ]);
                        $this->info("🧹 Cleared invalid photo data for Employee {$employee->id}");
                    }
                }
            } catch (\Exception $e) {
                $this->error("❌ Error fixing Employee {$employee->id}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("📊 Summary: Fixed {$fixed} employees, {$errors} errors");
    }

    private function findEmployeePhotos($employeeId)
    {
        $photoDir = 'employees/photos';
        $foundFiles = [];

        if (!Storage::disk('public')->exists($photoDir)) {
            return $foundFiles;
        }

        // Pattern ที่จะค้นหา
        $patterns = [
            "emp_{$employeeId}_",     // emp_14_20250807042054_9Xb0Kw.jpg
            "employee_{$employeeId}_", // employee_14_timestamp.jpg
            "emp{$employeeId}_",      // emp14_timestamp.jpg
            "{$employeeId}_",         // 14_timestamp.jpg
        ];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $files = Storage::disk('public')->files($photoDir);

        foreach ($files as $file) {
            $filename = basename($file);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowedExtensions)) {
                continue;
            }

            // ตรวจสอบ pattern
            foreach ($patterns as $pattern) {
                if (str_starts_with($filename, $pattern)) {
                    $foundFiles[] = $file;
                    break;
                }
            }
        }

        // เรียงตามวันที่ล่าสุด
        usort($foundFiles, function($a, $b) {
            $timeA = Storage::disk('public')->lastModified($a);
            $timeB = Storage::disk('public')->lastModified($b);
            return $timeB - $timeA;
        });

        return $foundFiles;
    }
}

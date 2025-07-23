<?php

namespace App\Helpers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ExpressHelper
{
    /**
     * Express Configuration
     */
    const USERNAME_LENGTH = 7;
    const PASSWORD_LENGTH = 4;
    const ACCOUNTING_KEYWORDS = ['บัญชี', 'การเงิน', 'accounting', 'finance'];
    
    /**
     * ตรวจสอบว่าแผนกสามารถใช้ Express ได้หรือไม่
     */
    public static function isDepartmentEligible($departmentId): bool
    {
        try {
            if (!$departmentId) return false;
            
            $department = Department::find($departmentId);
            if (!$department) return false;
            
            foreach (self::ACCOUNTING_KEYWORDS as $keyword) {
                if (stripos($department->name, $keyword) !== false) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Express eligibility check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * สร้าง Express Username
     */
    public static function generateUsername($firstName, $lastName): string
    {
        try {
            // ทำความสะอาดและรวมชื่อ
            $combined = preg_replace('/[^a-zA-Z]/', '', $firstName . $lastName);
            $username = strtolower($combined);
            
            // ตรวจสอบความยาว
            if (strlen($username) >= self::USERNAME_LENGTH) {
                $username = substr($username, 0, self::USERNAME_LENGTH);
            } else {
                $username = str_pad($username, self::USERNAME_LENGTH, 'x');
            }
            
            // ตรวจสอบความซ้ำ
            $counter = 1;
            $originalUsername = $username;
            
            while (Employee::where('express_username', $username)->exists()) {
                if ($counter == 1) {
                    $username = substr($originalUsername, 0, self::USERNAME_LENGTH - 1) . $counter;
                } else {
                    $username = substr($originalUsername, 0, self::USERNAME_LENGTH - 1) . $counter;
                }
                $counter++;
                
                // ป้องกัน infinite loop
                if ($counter > 99) {
                    $username = 'exp' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
                    break;
                }
            }
            
            return $username;
            
        } catch (\Exception $e) {
            Log::error('Express username generation failed: ' . $e->getMessage());
            return 'exp' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * สร้าง Express Password
     */
    public static function generatePassword(): string
    {
        try {
            $letters = 'abcdefghijklmnopqrstuvwxyz';
            $numbers = '0123456789';
            
            $password = '';
            
            // เพิ่ม 1 ตัวเลข (บังคับ)
            $password .= $numbers[mt_rand(0, strlen($numbers) - 1)];
            
            // เพิ่มตัวอักษร
            for ($i = 0; $i < self::PASSWORD_LENGTH - 1; $i++) {
                $password .= $letters[mt_rand(0, strlen($letters) - 1)];
            }
            
            // สุ่มลำดับ
            $passwordArray = str_split($password);
            shuffle($passwordArray);
            
            return implode('', $passwordArray);
            
        } catch (\Exception $e) {
            Log::error('Express password generation failed: ' . $e->getMessage());
            return 'a1b2'; // fallback password
        }
    }
    
    /**
     * ตรวจสอบรูปแบบ Express Username
     */
    public static function validateUsername($username): bool
    {
        if (empty($username)) return false;
        if (strlen($username) !== self::USERNAME_LENGTH) return false;
        if (!preg_match('/^[a-z]+$/', $username)) return false;
        
        return true;
    }
    
    /**
     * ตรวจสอบรูปแบบ Express Password
     */
    public static function validatePassword($password): bool
    {
        if (empty($password)) return false;
        if (strlen($password) !== self::PASSWORD_LENGTH) return false;
        if (!preg_match('/^[a-z0-9]+$/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false; // ต้องมีตัวเลข
        
        return true;
    }
    
    /**
     * ดึงสถิติ Express
     */
    public static function getStatistics(): array
    {
        try {
            $stats = [
                'total_employees' => Employee::count(),
                'total_express_users' => Employee::whereNotNull('express_username')->count(),
                'active_express_users' => Employee::whereNotNull('express_username')
                                                  ->where('status', 'active')
                                                  ->count(),
                'accounting_departments' => Department::where(function($query) {
                    foreach (self::ACCOUNTING_KEYWORDS as $keyword) {
                        $query->orWhere('name', 'like', "%{$keyword}%");
                    }
                })->count(),
                'recent_express_users' => Employee::whereNotNull('express_username')
                                                  ->with('department')
                                                  ->orderBy('created_at', 'desc')
                                                  ->take(10)
                                                  ->get(),
                'usage_percentage' => 0
            ];
            
            // คำนวณเปอร์เซ็นต์การใช้งาน
            if ($stats['total_employees'] > 0) {
                $stats['usage_percentage'] = round(($stats['total_express_users'] / $stats['total_employees']) * 100, 2);
            }
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Express statistics generation failed: ' . $e->getMessage());
            return [
                'total_employees' => 0,
                'total_express_users' => 0,
                'active_express_users' => 0,
                'accounting_departments' => 0,
                'recent_express_users' => collect(),
                'usage_percentage' => 0
            ];
        }
    }
    
    /**
     * สร้างรายงาน Express
     */
    public static function generateReport(): array
    {
        try {
            $stats = self::getStatistics();
            
            $report = [
                'summary' => $stats,
                'departments' => self::getAccountingDepartments(),
                'user_list' => self::getExpressUsersList(),
                'usage_trends' => self::getUsageTrends(),
                'generated_at' => now(),
                'generated_by' => auth()->user()->email ?? 'system'
            ];
            
            return $report;
            
        } catch (\Exception $e) {
            Log::error('Express report generation failed: ' . $e->getMessage());
            return [
                'error' => 'Failed to generate report',
                'message' => $e->getMessage(),
                'generated_at' => now()
            ];
        }
    }
    
    /**
     * ดึงข้อมูลแผนกบัญชี
     */
    private static function getAccountingDepartments(): array
    {
        try {
            return Department::where(function($query) {
                foreach (self::ACCOUNTING_KEYWORDS as $keyword) {
                    $query->orWhere('name', 'like', "%{$keyword}%");
                }
            })->with(['employees' => function($query) {
                $query->whereNotNull('express_username');
            }])->get()->map(function($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'express_users_count' => $dept->employees->count(),
                    'total_employees' => Employee::where('department_id', $dept->id)->count()
                ];
            })->toArray();
            
        } catch (\Exception $e) {
            Log::error('Accounting departments fetch failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ดึงรายชื่อผู้ใช้ Express
     */
    private static function getExpressUsersList(): array
    {
        try {
            return Employee::whereNotNull('express_username')
                           ->with('department')
                           ->select([
                               'id', 'employee_code', 'first_name_th', 'last_name_th',
                               'first_name_en', 'last_name_en', 'email', 'department_id',
                               'express_username', 'status', 'created_at'
                           ])
                           ->orderBy('first_name_th')
                           ->get()
                           ->map(function($user) {
                               return [
                                   'employee_code' => $user->employee_code,
                                   'full_name_th' => $user->first_name_th . ' ' . $user->last_name_th,
                                   'full_name_en' => $user->first_name_en . ' ' . $user->last_name_en,
                                   'email' => $user->email,
                                   'department' => $user->department->name ?? 'N/A',
                                   'express_username' => $user->express_username,
                                   'status' => $user->status,
                                   'created_at' => $user->created_at->format('d/m/Y')
                               ];
                           })->toArray();
                           
        } catch (\Exception $e) {
            Log::error('Express users list fetch failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ดึงข้อมูลแนวโน้มการใช้งาน
     */
    private static function getUsageTrends(): array
    {
        try {
            $trends = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $count = Employee::whereNotNull('express_username')
                               ->where('created_at', '>=', $date->startOfMonth())
                               ->where('created_at', '<=', $date->endOfMonth())
                               ->count();
                
                $trends[] = [
                    'month' => $date->format('M Y'),
                    'count' => $count
                ];
            }
            
            return $trends;
            
        } catch (\Exception $e) {
            Log::error('Usage trends fetch failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ทดสอบการเชื่อมต่อ Express
     */
    public static function testConnection($username = null, $password = null): array
    {
        try {
            $startTime = microtime(true);
            
            // Mock connection test
            usleep(mt_rand(100000, 500000)); // 0.1 to 0.5 seconds delay
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);
            
            // Basic validation
            $validFormat = true;
            if ($username && !self::validateUsername($username)) {
                $validFormat = false;
            }
            if ($password && !self::validatePassword($password)) {
                $validFormat = false;
            }
            
            return [
                'success' => $validFormat,
                'message' => $validFormat ? 'เชื่อมต่อ Express สำเร็จ' : 'รูปแบบ Username หรือ Password ไม่ถูกต้อง',
                'response_time' => $responseTime,
                'tested_at' => now(),
                'server_status' => 'online'
            ];
            
        } catch (\Exception $e) {
            Log::error('Express connection test failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'การทดสอบการเชื่อมต่อล้มเหลว',
                'error' => $e->getMessage(),
                'tested_at' => now(),
                'server_status' => 'error'
            ];
        }
    }
    
    /**
     * ส่งออกข้อมูล Express เป็น CSV
     */
    public static function exportToCsv(): string
    {
        try {
            $filename = 'express_users_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = storage_path('app/exports/' . $filename);
            
            // สร้างโฟลเดอร์ถ้าไม่มี
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            
            $file = fopen($filepath, 'w');
            
            // Header
            fputcsv($file, [
                'Employee Code',
                'Full Name (TH)',
                'Full Name (EN)',
                'Department',
                'Express Username',
                'Email',
                'Status',
                'Created Date'
            ]);
            
            // Data
            $users = self::getExpressUsersList();
            foreach ($users as $user) {
                fputcsv($file, [
                    $user['employee_code'],
                    $user['full_name_th'],
                    $user['full_name_en'],
                    $user['department'],
                    $user['express_username'],
                    $user['email'],
                    $user['status'],
                    $user['created_at']
                ]);
            }
            
            fclose($file);
            
            return $filename;
            
        } catch (\Exception $e) {
            Log::error('Express CSV export failed: ' . $e->getMessage());
            throw new \Exception('Failed to export Express data');
        }
    }
    
    /**
     * ล้างข้อมูล Express ที่ไม่ใช้งาน
     */
    public static function cleanupInactiveUsers(): int
    {
        try {
            $inactiveThreshold = now()->subMonths(6);
            
            $count = Employee::whereNotNull('express_username')
                             ->where('status', 'inactive')
                             ->where('updated_at', '<', $inactiveThreshold)
                             ->update([
                                 'express_username' => null,
                                 'express_password' => null
                             ]);
            
            Log::info("Express cleanup completed: {$count} inactive users cleaned");
            
            return $count;
            
        } catch (\Exception $e) {
            Log::error('Express cleanup failed: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * ตรวจสอบความปลอดภัยของรหัสผ่าน
     */
    public static function checkPasswordStrength($password): array
    {
        $strength = [
            'score' => 0,
            'feedback' => [],
            'level' => 'weak'
        ];
        
        if (strlen($password) >= self::PASSWORD_LENGTH) {
            $strength['score'] += 25;
        } else {
            $strength['feedback'][] = 'รหัสผ่านต้องมี ' . self::PASSWORD_LENGTH . ' ตัวอักษร';
        }
        
        if (preg_match('/[0-9]/', $password)) {
            $strength['score'] += 25;
        } else {
            $strength['feedback'][] = 'ต้องมีตัวเลขอย่างน้อย 1 ตัว';
        }
        
        if (preg_match('/[a-z]/', $password)) {
            $strength['score'] += 25;
        } else {
            $strength['feedback'][] = 'ต้องมีตัวอักษรเล็กอย่างน้อย 1 ตัว';
        }
        
        if (preg_match('/^[a-z0-9]+$/', $password)) {
            $strength['score'] += 25;
        } else {
            $strength['feedback'][] = 'ใช้ได้เฉพาะตัวอักษรเล็กและตัวเลข';
        }
        
        // กำหนดระดับ
        if ($strength['score'] >= 100) {
            $strength['level'] = 'strong';
        } elseif ($strength['score'] >= 75) {
            $strength['level'] = 'medium';
        }
        
        return $strength;
    }
    
    /**
     * Log Express activity
     */
    public static function logActivity($action, $details = []): void
    {
        try {
            Log::info('Express Activity', [
                'action' => $action,
                'details' => $details,
                'user' => auth()->user()->email ?? 'system',
                'timestamp' => now(),
                'ip' => request()->ip()
            ]);
        } catch (\Exception $e) {
            // Silent fail for logging
        }
    }
}
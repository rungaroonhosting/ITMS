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
        echo "🚀 Starting Safe Express Username v2.0 Migration...\n";
        
        // ✅ 1. แก้ไข express_username column - รองรับ 1-7 ตัวอักษร (ปลอดภัย)
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'express_username')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('express_username', 7)->nullable()->change();
            });
            echo "✅ Express username column updated to support 1-7 characters\n";
        } else {
            // สร้าง column ใหม่ถ้าไม่มี
            if (Schema::hasTable('employees')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('express_username', 7)->nullable()->after('username');
                });
                echo "✅ Express username column created\n";
            }
        }
        
        // ✅ 2. เพิ่ม/แก้ไข express_password column (ปลอดภัย)
        if (Schema::hasTable('employees')) {
            if (!Schema::hasColumn('employees', 'express_password')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('express_password', 4)->nullable()->after('express_username');
                });
                echo "✅ Express password column created\n";
            } else {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('express_password', 4)->nullable()->change();
                });
                echo "✅ Express password column updated\n";
            }
        }
        
        // ✅ 3. เพิ่ม hire_date และ salary ถ้ายังไม่มี (ปลอดภัย)
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'hire_date')) {
                    $table->date('hire_date')->nullable()->after('position');
                    echo "✅ Hire date column created\n";
                } else {
                    echo "ℹ️ Hire date column already exists, skipping...\n";
                }
                
                if (!Schema::hasColumn('employees', 'salary')) {
                    $table->decimal('salary', 10, 2)->nullable()->after('hire_date');
                    echo "✅ Salary column created\n";
                } else {
                    echo "ℹ️ Salary column already exists, skipping...\n";
                }
            });
        }
        
        // ✅ 4. ทำความสะอาดข้อมูล Express ที่ผิดรูปแบบ
        $this->cleanExpressData();
        
        // ✅ 5. เพิ่ม indexes สำหรับประสิทธิภาพ (ปลอดภัย)
        $this->addSafeIndexes();
        
        // ✅ 6. อัปเดต Express data สำหรับแผนกที่เปิดใช้งาน
        $this->updateExpressEnabledDepartments();
        
        echo "🎉 Safe Express Username v2.0 Migration completed successfully!\n";
        echo "⚡ Features: 1-7 characters username, 4 unique digits password, safe column handling\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Rolling back Safe Express Username v2.0 Migration...\n";
        
        // ไม่ลบ columns เพราะอาจมีข้อมูลสำคัญ
        // แต่จะล้างข้อมูล Express ที่ไม่ถูกต้อง
        
        try {
            if (Schema::hasTable('employees')) {
                DB::table('employees')->update([
                    'express_username' => null,
                    'express_password' => null
                ]);
                echo "✅ Express data cleared for rollback\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ Rollback warning: " . $e->getMessage() . "\n";
        }
        
        echo "🔄 Safe rollback completed\n";
    }

    /**
     * ทำความสะอาดข้อมูล Express ที่ผิดรูปแบบ
     */
    private function cleanExpressData(): void
    {
        try {
            echo "🧹 Cleaning Express data...\n";
            
            // ตรวจสอบว่าตาราง employees มีอยู่จริง
            if (!Schema::hasTable('employees')) {
                echo "ℹ️ Employees table not found, skipping data cleaning\n";
                return;
            }
            
            // ล้าง Express username ที่ผิดรูปแบบ
            $invalidUsernames = DB::table('employees')
                ->whereNotNull('express_username')
                ->where(function($query) {
                    $query->where(DB::raw('CHAR_LENGTH(express_username)'), '>', 7)
                          ->orWhere(DB::raw('CHAR_LENGTH(express_username)'), '<', 1)
                          ->orWhere('express_username', 'REGEXP', '[^a-zA-Z0-9]');
                })
                ->get();

            foreach ($invalidUsernames as $employee) {
                // สร้าง Express username ใหม่
                $newUsername = $this->generateValidExpressUsername($employee->first_name_en ?? '', $employee->last_name_en ?? '');
                
                DB::table('employees')
                    ->where('id', $employee->id)
                    ->update(['express_username' => $newUsername]);
                
                echo "🔄 Fixed Express username for employee ID {$employee->id}: {$employee->express_username} → {$newUsername}\n";
            }
            
            // ล้าง Express password ที่ผิดรูปแบบ
            $invalidPasswords = DB::table('employees')
                ->whereNotNull('express_password')
                ->where(function($query) {
                    $query->where(DB::raw('CHAR_LENGTH(express_password)'), '!=', 4)
                          ->orWhere('express_password', 'NOT REGEXP', '^[0-9]{4}$');
                })
                ->get();

            foreach ($invalidPasswords as $employee) {
                // สร้าง Express password ใหม่ (4 ตัวเลขไม่ซ้ำ)
                $newPassword = $this->generateValidExpressPassword();
                
                DB::table('employees')
                    ->where('id', $employee->id)
                    ->update(['express_password' => $newPassword]);
                
                echo "🔄 Fixed Express password for employee ID {$employee->id}: {$employee->express_password} → {$newPassword}\n";
            }
            
            echo "✅ Express data cleaning completed\n";
            
        } catch (\Exception $e) {
            echo "⚠️ Express data cleaning warning: " . $e->getMessage() . "\n";
            \Log::warning('Express data cleaning error: ' . $e->getMessage());
        }
    }

    /**
     * เพิ่ม indexes แบบปลอดภัย
     */
    private function addSafeIndexes(): void
    {
        try {
            echo "🔍 Adding safe indexes...\n";
            
            if (!Schema::hasTable('employees')) {
                echo "ℹ️ Employees table not found, skipping index creation\n";
                return;
            }
            
            $indexesToAdd = [
                ['express_username', 'employees_express_username_idx'],
                ['express_password', 'employees_express_password_idx'],
                ['hire_date', 'employees_hire_date_idx'],
                ['salary', 'employees_salary_idx']
            ];
            
            foreach ($indexesToAdd as [$column, $indexName]) {
                if (Schema::hasColumn('employees', $column)) {
                    try {
                        // ตรวจสอบว่า index มีอยู่แล้วหรือไม่
                        $indexes = DB::select("SHOW INDEX FROM employees WHERE Key_name = ?", [$indexName]);
                        
                        if (empty($indexes)) {
                            Schema::table('employees', function (Blueprint $table) use ($column, $indexName) {
                                $table->index($column, $indexName);
                            });
                            echo "✅ Index {$indexName} added for column {$column}\n";
                        } else {
                            echo "ℹ️ Index {$indexName} already exists\n";
                        }
                    } catch (\Exception $e) {
                        echo "⚠️ Could not add index {$indexName}: " . $e->getMessage() . "\n";
                    }
                }
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Index creation warning: " . $e->getMessage() . "\n";
        }
    }

    /**
     * อัปเดต Express data สำหรับแผนกที่เปิดใช้งาน
     */
    private function updateExpressEnabledDepartments(): void
    {
        try {
            echo "⚡ Updating Express-enabled departments...\n";
            
            if (!Schema::hasTable('employees') || !Schema::hasTable('departments')) {
                echo "ℹ️ Required tables not found, skipping Express department update\n";
                return;
            }
            
            // ค้นหาพนักงานในแผนกที่เปิด Express แต่ยังไม่มี Express credentials
            $employeesNeedingExpress = DB::table('employees')
                ->join('departments', 'employees.department_id', '=', 'departments.id')
                ->where('departments.express_enabled', true)
                ->whereNull('employees.deleted_at')
                ->where(function($query) {
                    $query->whereNull('employees.express_username')
                          ->orWhereNull('employees.express_password')
                          ->orWhere('employees.express_username', '')
                          ->orWhere('employees.express_password', '');
                })
                ->select('employees.*', 'departments.name as department_name')
                ->limit(50) // จำกัดจำนวนเพื่อป้องกันการทำงานนานเกินไป
                ->get();

            foreach ($employeesNeedingExpress as $employee) {
                $username = $employee->express_username ?: $this->generateValidExpressUsername($employee->first_name_en ?? '', $employee->last_name_en ?? '');
                $password = $employee->express_password ?: $this->generateValidExpressPassword();
                
                DB::table('employees')
                    ->where('id', $employee->id)
                    ->update([
                        'express_username' => $username,
                        'express_password' => $password
                    ]);
                
                echo "⚡ Generated Express credentials for {$employee->first_name_th} {$employee->last_name_th} in {$employee->department_name}\n";
            }
            
            echo "✅ Express-enabled departments updated\n";
            
        } catch (\Exception $e) {
            echo "⚠️ Express department update warning: " . $e->getMessage() . "\n";
        }
    }

    /**
     * สร้าง Express username ที่ถูกต้อง (1-7 ตัวอักษร)
     */
    private function generateValidExpressUsername($firstName, $lastName): string
    {
        // ทำความสะอาดชื่อ
        $firstName = preg_replace('/[^a-zA-Z0-9]/', '', $firstName);
        $lastName = preg_replace('/[^a-zA-Z0-9]/', '', $lastName);
        
        if (!$firstName) {
            $firstName = 'user';
        }
        
        // กรณีที่ 1: ใช้ชื่อจริงถ้าไม่เกิน 7 ตัว
        $fullName = strtolower($firstName);
        if (strlen($fullName) >= 1 && strlen($fullName) <= 7) {
            if (!$this->isExpressUsernameExists($fullName)) {
                return $fullName;
            }
        }
        
        // กรณีที่ 2: ตัดชื่อให้เหลือ 7 ตัว
        if (strlen($fullName) > 7) {
            $fullName = substr($fullName, 0, 7);
            if (!$this->isExpressUsernameExists($fullName)) {
                return $fullName;
            }
        }
        
        // กรณีที่ 3: เพิ่มตัวเลขต่อท้าย
        $baseUsername = substr(strtolower($firstName), 0, 6);
        for ($i = 1; $i <= 9; $i++) {
            $username = $baseUsername . $i;
            if (strlen($username) <= 7 && !$this->isExpressUsernameExists($username)) {
                return $username;
            }
        }
        
        // กรณีสุดท้าย: สุ่ม
        do {
            $username = 'emp' . random_int(100, 999);
        } while ($this->isExpressUsernameExists($username));
        
        return $username;
    }

    /**
     * สร้าง Express password ที่ถูกต้อง (4 ตัวเลขไม่ซ้ำ)
     */
    private function generateValidExpressPassword(): string
    {
        $attempts = 0;
        do {
            $digits = [];
            while (count($digits) < 4) {
                $digit = random_int(0, 9);
                if (!in_array($digit, $digits)) {
                    $digits[] = $digit;
                }
            }
            $password = implode('', $digits);
            $attempts++;
        } while ($this->isExpressPasswordExists($password) && $attempts < 100);
        
        return $password;
    }

    /**
     * ตรวจสอบว่า Express username มีอยู่แล้วหรือไม่
     */
    private function isExpressUsernameExists($username): bool
    {
        if (!Schema::hasTable('employees')) {
            return false;
        }
        
        return DB::table('employees')
            ->where('express_username', $username)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * ตรวจสอบว่า Express password มีอยู่แล้วหรือไม่
     */
    private function isExpressPasswordExists($password): bool
    {
        if (!Schema::hasTable('employees')) {
            return false;
        }
        
        return DB::table('employees')
            ->where('express_password', $password)
            ->whereNull('deleted_at')
            ->exists();
    }
};

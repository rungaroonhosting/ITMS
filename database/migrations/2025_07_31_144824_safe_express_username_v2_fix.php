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
        echo "🚀 Starting SUPER SAFE Express Username v2.0 Migration...\n";
        
        // ตรวจสอบตารางที่จำเป็น
        if (!Schema::hasTable('employees')) {
            echo "❌ Table 'employees' not found. Please create employees table first.\n";
            return;
        }
        
        echo "✅ Table 'employees' found, proceeding...\n";
        
        // ✅ 1. จัดการ express_username column แบบปลอดภัย
        $this->handleExpressUsernameColumn();
        
        // ✅ 2. จัดการ express_password column แบบปลอดภัย  
        $this->handleExpressPasswordColumn();
        
        // ✅ 3. จัดการ columns อื่นๆ แบบปลอดภัย
        $this->handleOptionalColumns();
        
        // ✅ 4. ทำความสะอาดข้อมูล Express
        $this->cleanExpressData();
        
        // ✅ 5. เพิ่ม indexes แบบปลอดภัย
        $this->addSafeIndexes();
        
        echo "🎉 SUPER SAFE Migration completed successfully!\n";
        echo "⚡ Express v2.0: Username 1-7 chars, Password 4 unique digits\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Rolling back SUPER SAFE Migration...\n";
        
        try {
            if (Schema::hasTable('employees')) {
                DB::table('employees')->update([
                    'express_username' => null,
                    'express_password' => null
                ]);
                echo "✅ Express data cleared\n";
            }
        } catch (\Exception $e) {
            echo "⚠️ Rollback warning: " . $e->getMessage() . "\n";
        }
        
        echo "🔄 Safe rollback completed\n";
    }
    
    /**
     * จัดการ express_username column แบบปลอดภัย
     */
    private function handleExpressUsernameColumn(): void
    {
        try {
            echo "🔍 Checking express_username column...\n";
            
            $hasColumn = false;
            try {
                $hasColumn = Schema::hasColumn('employees', 'express_username');
            } catch (\Exception $e) {
                echo "⚠️ Error checking express_username column: " . $e->getMessage() . "\n";
                $hasColumn = false;
            }
            
            if ($hasColumn) {
                // Column มีอยู่แล้ว - อัปเดตความยาว
                try {
                    Schema::table('employees', function (Blueprint $table) {
                        $table->string('express_username', 7)->nullable()->change();
                    });
                    echo "✅ Express username column updated to 7 chars\n";
                } catch (\Exception $e) {
                    echo "⚠️ Could not update express_username: " . $e->getMessage() . "\n";
                }
            } else {
                // Column ไม่มี - สร้างใหม่
                try {
                    Schema::table('employees', function (Blueprint $table) {
                        $table->string('express_username', 7)->nullable();
                    });
                    echo "✅ Express username column created\n";
                } catch (\Exception $e) {
                    echo "⚠️ Could not create express_username: " . $e->getMessage() . "\n";
                }
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Express username handling error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * จัดการ express_password column แบบปลอดภัย
     */
    private function handleExpressPasswordColumn(): void
    {
        try {
            echo "🔍 Checking express_password column...\n";
            
            $hasColumn = false;
            try {
                $hasColumn = Schema::hasColumn('employees', 'express_password');
            } catch (\Exception $e) {
                echo "⚠️ Error checking express_password column: " . $e->getMessage() . "\n";
                $hasColumn = false;
            }
            
            if ($hasColumn) {
                // Column มีอยู่แล้ว - อัปเดตความยาว
                try {
                    Schema::table('employees', function (Blueprint $table) {
                        $table->string('express_password', 4)->nullable()->change();
                    });
                    echo "✅ Express password column updated to 4 chars\n";
                } catch (\Exception $e) {
                    echo "⚠️ Could not update express_password: " . $e->getMessage() . "\n";
                }
            } else {
                // Column ไม่มี - สร้างใหม่
                try {
                    Schema::table('employees', function (Blueprint $table) {
                        $table->string('express_password', 4)->nullable();
                    });
                    echo "✅ Express password column created\n";
                } catch (\Exception $e) {
                    echo "⚠️ Could not create express_password: " . $e->getMessage() . "\n";
                }
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Express password handling error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * จัดการ columns อื่นๆ แบบปลอดภัย (hire_date, salary)
     */
    private function handleOptionalColumns(): void
    {
        // จัดการ hire_date
        try {
            echo "🔍 Checking hire_date column...\n";
            
            $hasHireDate = false;
            try {
                $hasHireDate = Schema::hasColumn('employees', 'hire_date');
            } catch (\Exception $e) {
                $hasHireDate = false;
            }
            
            if (!$hasHireDate) {
                try {
                    Schema::table('employees', function (Blueprint $table) {
                        $table->date('hire_date')->nullable();
                    });
                    echo "✅ Hire date column created\n";
                } catch (\Exception $e) {
                    echo "⚠️ Could not create hire_date: " . $e->getMessage() . "\n";
                }
            } else {
                echo "ℹ️ Hire date column already exists, skipping...\n";
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Hire date handling error: " . $e->getMessage() . "\n";
        }
        
        // จัดการ salary
        try {
            echo "🔍 Checking salary column...\n";
            
            $hasSalary = false;
            try {
                $hasSalary = Schema::hasColumn('employees', 'salary');
            } catch (\Exception $e) {
                $hasSalary = false;
            }
            
            if (!$hasSalary) {
                try {
                    Schema::table('employees', function (Blueprint $table) {
                        $table->decimal('salary', 10, 2)->nullable();
                    });
                    echo "✅ Salary column created\n";
                } catch (\Exception $e) {
                    echo "⚠️ Could not create salary: " . $e->getMessage() . "\n";
                }
            } else {
                echo "ℹ️ Salary column already exists, skipping...\n";
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Salary handling error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * ทำความสะอาดข้อมูล Express ที่ผิดรูปแบบ
     */
    private function cleanExpressData(): void
    {
        try {
            echo "🧹 Cleaning Express data...\n";
            
            if (!Schema::hasTable('employees')) {
                echo "ℹ️ Employees table not found, skipping data cleaning\n";
                return;
            }
            
            // ล้าง Express username ที่ผิดรูปแบบ
            $fixed = 0;
            try {
                $invalidUsernames = DB::select("
                    SELECT id, express_username, first_name_en, last_name_en 
                    FROM employees 
                    WHERE express_username IS NOT NULL 
                    AND (
                        CHAR_LENGTH(express_username) > 7 OR 
                        CHAR_LENGTH(express_username) < 1 OR 
                        express_username REGEXP '[^a-zA-Z0-9]'
                    )
                    LIMIT 50
                ");

                foreach ($invalidUsernames as $employee) {
                    $newUsername = $this->generateValidExpressUsername($employee->first_name_en ?? '', $employee->last_name_en ?? '');
                    
                    DB::table('employees')
                        ->where('id', $employee->id)
                        ->update(['express_username' => $newUsername]);
                    
                    $fixed++;
                }
                
                if ($fixed > 0) {
                    echo "✅ Fixed {$fixed} invalid Express usernames\n";
                }
                
            } catch (\Exception $e) {
                echo "⚠️ Express username cleaning warning: " . $e->getMessage() . "\n";
            }
            
            // ล้าง Express password ที่ผิดรูปแบบ
            $fixed = 0;
            try {
                $invalidPasswords = DB::select("
                    SELECT id, express_password 
                    FROM employees 
                    WHERE express_password IS NOT NULL 
                    AND (
                        CHAR_LENGTH(express_password) != 4 OR 
                        express_password NOT REGEXP '^[0-9]{4}$'
                    )
                    LIMIT 50
                ");

                foreach ($invalidPasswords as $employee) {
                    $newPassword = $this->generateValidExpressPassword();
                    
                    DB::table('employees')
                        ->where('id', $employee->id)
                        ->update(['express_password' => $newPassword]);
                    
                    $fixed++;
                }
                
                if ($fixed > 0) {
                    echo "✅ Fixed {$fixed} invalid Express passwords\n";
                }
                
            } catch (\Exception $e) {
                echo "⚠️ Express password cleaning warning: " . $e->getMessage() . "\n";
            }
            
            echo "✅ Express data cleaning completed\n";
            
        } catch (\Exception $e) {
            echo "⚠️ Express data cleaning error: " . $e->getMessage() . "\n";
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
                'express_username' => 'idx_employees_express_username',
                'express_password' => 'idx_employees_express_password',
            ];
            
            foreach ($indexesToAdd as $column => $indexName) {
                try {
                    if (Schema::hasColumn('employees', $column)) {
                        // ตรวจสอบว่า index มีอยู่แล้วหรือไม่
                        $indexes = DB::select("SHOW INDEX FROM employees WHERE Key_name = ?", [$indexName]);
                        
                        if (empty($indexes)) {
                            Schema::table('employees', function (Blueprint $table) use ($column, $indexName) {
                                $table->index($column, $indexName);
                            });
                            echo "✅ Index {$indexName} added for {$column}\n";
                        } else {
                            echo "ℹ️ Index {$indexName} already exists\n";
                        }
                    }
                } catch (\Exception $e) {
                    echo "⚠️ Could not add index {$indexName}: " . $e->getMessage() . "\n";
                }
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Index creation warning: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * สร้าง Express username ที่ถูกต้อง (1-7 ตัวอักษร)
     */
    private function generateValidExpressUsername($firstName, $lastName): string
    {
        // ทำความสะอาดชื่อ
        $firstName = preg_replace('/[^a-zA-Z0-9]/', '', $firstName ?? '');
        $lastName = preg_replace('/[^a-zA-Z0-9]/', '', $lastName ?? '');
        
        if (empty($firstName)) {
            $firstName = 'user';
        }
        
        // ใช้ชื่อจริงถ้าไม่เกิน 7 ตัว
        $fullName = strtolower($firstName);
        if (strlen($fullName) >= 1 && strlen($fullName) <= 7) {
            if (!$this->isExpressUsernameExists($fullName)) {
                return $fullName;
            }
        }
        
        // ตัดชื่อให้เหลือ 7 ตัว
        if (strlen($fullName) > 7) {
            $fullName = substr($fullName, 0, 7);
            if (!$this->isExpressUsernameExists($fullName)) {
                return $fullName;
            }
        }
        
        // เพิ่มตัวเลขต่อท้าย
        $baseUsername = substr(strtolower($firstName), 0, 6);
        for ($i = 1; $i <= 9; $i++) {
            $username = $baseUsername . $i;
            if (strlen($username) <= 7 && !$this->isExpressUsernameExists($username)) {
                return $username;
            }
        }
        
        // สุ่ม
        do {
            $username = 'emp' . random_int(100, 999);
        } while ($this->isExpressUsernameExists($username) && $username !== '');
        
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
        try {
            if (!Schema::hasTable('employees') || empty($username)) {
                return false;
            }
            
            return DB::table('employees')
                ->where('express_username', $username)
                ->whereNull('deleted_at')
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * ตรวจสอบว่า Express password มีอยู่แล้วหรือไม่
     */
    private function isExpressPasswordExists($password): bool
    {
        try {
            if (!Schema::hasTable('employees') || empty($password)) {
                return false;
            }
            
            return DB::table('employees')
                ->where('express_password', $password)
                ->whereNull('deleted_at')
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
};

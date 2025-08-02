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
        echo "🏢 Creating branches table...\n";
        
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            
            // ข้อมูลพื้นฐานของสาขา
            $table->string('branch_code', 10)->unique()->comment('รหัสสาขา เช่น BKK01, CNX01');
            $table->string('name', 100)->comment('ชื่อสาขา');
            $table->text('address')->nullable()->comment('ที่อยู่สาขา');
            $table->string('phone', 20)->nullable()->comment('เบอร์โทรศัพท์สาขา');
            $table->string('email', 100)->nullable()->comment('อีเมลสาขา');
            
            // ข้อมูลการจัดการ
            $table->unsignedBigInteger('manager_id')->nullable()->comment('ผู้จัดการสาขา');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('สถานะสาขา');
            
            // ข้อมูลเพิ่มเติม
            $table->integer('capacity')->nullable()->comment('จำนวนพนักงานที่รองรับได้');
            $table->decimal('area_sqm', 8, 2)->nullable()->comment('พื้นที่สาขา (ตร.ม.)');
            $table->date('opening_date')->nullable()->comment('วันที่เปิดสาขา');
            
            // ข้อมูลระบบ
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
            
            // Indexes
            $table->index('branch_code', 'idx_branches_code');
            $table->index('name', 'idx_branches_name');
            $table->index('status', 'idx_branches_status');
            $table->index('manager_id', 'idx_branches_manager');
            $table->index('opening_date', 'idx_branches_opening_date');
        });
        
        echo "✅ Branches table created successfully!\n";
        
        // สร้างข้อมูลสาขาเริ่มต้น
        $this->seedDefaultBranches();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Dropping branches table...\n";
        
        Schema::dropIfExists('branches');
        
        echo "✅ Branches table dropped successfully!\n";
    }
    
    /**
     * สร้างข้อมูลสาขาเริ่มต้น
     */
    private function seedDefaultBranches(): void
    {
        try {
            echo "🌱 Seeding default branches...\n";
            
            $defaultBranches = [
                [
                    'branch_code' => 'HQ001',
                    'name' => 'สำนักงานใหญ่',
                    'address' => '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
                    'phone' => '02-123-4567',
                    'email' => 'hq@company.com',
                    'status' => 'active',
                    'capacity' => 100,
                    'area_sqm' => 500.00,
                    'opening_date' => '2020-01-01',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'branch_code' => 'BKK01',
                    'name' => 'สาขาสีลม',
                    'address' => '456 ถนนสีลม แขวงสีลม เขตบางรัก กรุงเทพฯ 10500',
                    'phone' => '02-234-5678',
                    'email' => 'silom@company.com',
                    'status' => 'active',
                    'capacity' => 50,
                    'area_sqm' => 300.00,
                    'opening_date' => '2021-06-15',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'branch_code' => 'CNX01',
                    'name' => 'สาขาเชียงใหม่',
                    'address' => '789 ถนนนิมมานเหมินท์ ตำบลสุเทพ อำเภอเมือง เชียงใหม่ 50200',
                    'phone' => '053-123-456',
                    'email' => 'chiangmai@company.com',
                    'status' => 'active',
                    'capacity' => 30,
                    'area_sqm' => 200.00,
                    'opening_date' => '2022-03-01',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'branch_code' => 'PKT01',
                    'name' => 'สาขาภูเก็ต',
                    'address' => '321 ถนนราษฎร์อุทิศ ตำบลรัษฎา อำเภอเมือง ภูเก็ต 83000',
                    'phone' => '076-987-654',
                    'email' => 'phuket@company.com',
                    'status' => 'active',
                    'capacity' => 25,
                    'area_sqm' => 180.00,
                    'opening_date' => '2023-01-15',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'branch_code' => 'BKK02',
                    'name' => 'สาขารัชดาภิเษก (ปิดชั่วคราว)',
                    'address' => '555 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพฯ 10400',
                    'phone' => '02-345-6789',
                    'email' => 'ratchada@company.com',
                    'status' => 'inactive',
                    'capacity' => 40,
                    'area_sqm' => 250.00,
                    'opening_date' => '2021-12-01',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];
            
            DB::table('branches')->insert($defaultBranches);
            
            echo "✅ Default branches seeded: " . count($defaultBranches) . " branches\n";
            echo "🏢 Branches: HQ001 (สำนักงานใหญ่), BKK01 (สีลม), CNX01 (เชียงใหม่), PKT01 (ภูเก็ต), BKK02 (ปิดชั่วคราว)\n";
            
        } catch (\Exception $e) {
            echo "⚠️ Seeding warning: " . $e->getMessage() . "\n";
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ลบตารางเดิมถ้ามี
        Schema::dropIfExists('departments');
        
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // เพิ่มข้อมูลแผนกเริ่มต้น
        DB::table('departments')->insert([
            [
                'id' => 1,
                'name' => 'แผนกเทคโนโลยีสารสนเทศ',
                'code' => 'IT',
                'description' => 'แผนกที่ดูแลระบบคอมพิวเตอร์และเทคโนโลยี',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'name' => 'แผนกบัญชี',
                'code' => 'ACC',
                'description' => 'แผนกการเงินและบัญชี',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'name' => 'แผนกทรัพยากรบุคคล',
                'code' => 'HR',
                'description' => 'แผนกทรัพยากรบุคคล',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 4,
                'name' => 'แผนกขาย',
                'code' => 'SALES',
                'description' => 'แผนกการขายและการตลาด',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

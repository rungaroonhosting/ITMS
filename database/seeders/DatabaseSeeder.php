<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting ITMS database seeding...');
        
        // ข้อมูลจะถูกสร้างผ่าน migrations แล้ว
        // ไม่ต้องรัน seeder เพิ่ม
        
        $this->command->info('✅ Database seeded successfully!');
        $this->command->line('');
        $this->command->line('📋 Sample employees created:');
        $this->command->line('🔑 Super Admin: admin@bettersystem.co.th');
        $this->command->line('🔑 IT Manager: manager@bettersystem.co.th'); 
        $this->command->line('🔑 Employee: employee@bettersystem.co.th');
        $this->command->line('🔐 Password (all): password123');
        $this->command->line('');
        $this->command->line('🏢 Departments: IT, ACC, HR, SALES');
        $this->command->line('📧 Email domains: bettersystem.co.th, better-groups.com');
        $this->command->line('');
        $this->command->info('🎯 Ready to start Phase 3-4: Controllers & Views!');
    }
}

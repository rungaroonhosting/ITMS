<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Employee\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => 'IT', 'name' => 'Information Technology', 'description' => 'Responsible for IT infrastructure and support'],
            ['code' => 'HR', 'name' => 'Human Resources', 'description' => 'Employee management and development'],
            ['code' => 'FIN', 'name' => 'Finance', 'description' => 'Financial planning and accounting'],
            ['code' => 'OPS', 'name' => 'Operations', 'description' => 'Daily operations management'],
            ['code' => 'MKT', 'name' => 'Marketing', 'description' => 'Marketing and communications'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['code' => $dept['code']], // ค้นหาด้วย code
                $dept // ข้อมูลที่จะอัปเดตหรือสร้างใหม่
            );
        }
    }
}

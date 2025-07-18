<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Employee\Models\Position;
use App\Modules\Employee\Models\Department;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $itDept = Department::where('code', 'IT')->first();
        $hrDept = Department::where('code', 'HR')->first();
        $finDept = Department::where('code', 'FIN')->first();

        $positions = [
            ['code' => 'IT001', 'name' => 'System Administrator', 'department_id' => $itDept->id, 'level' => 'senior', 'min_salary' => 45000, 'max_salary' => 65000],
            ['code' => 'IT002', 'name' => 'IT Support', 'department_id' => $itDept->id, 'level' => 'junior', 'min_salary' => 25000, 'max_salary' => 35000],
            ['code' => 'IT003', 'name' => 'IT Manager', 'department_id' => $itDept->id, 'level' => 'manager', 'min_salary' => 60000, 'max_salary' => 80000],
            ['code' => 'HR001', 'name' => 'HR Officer', 'department_id' => $hrDept->id, 'level' => 'junior', 'min_salary' => 30000, 'max_salary' => 40000],
            ['code' => 'HR002', 'name' => 'HR Manager', 'department_id' => $hrDept->id, 'level' => 'manager', 'min_salary' => 50000, 'max_salary' => 70000],
            ['code' => 'FIN001', 'name' => 'Accountant', 'department_id' => $finDept->id, 'level' => 'junior', 'min_salary' => 28000, 'max_salary' => 38000],
            ['code' => 'FIN002', 'name' => 'Finance Manager', 'department_id' => $finDept->id, 'level' => 'manager', 'min_salary' => 55000, 'max_salary' => 75000],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['code' => $position['code']],
                $position
            );
        }
    }
}

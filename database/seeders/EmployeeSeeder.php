<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\Department;
use App\Modules\Employee\Models\Position;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $itDept = Department::where('code', 'IT')->first();
        $itManagerPos = Position::where('code', 'IT003')->first();
        $itAdminPos = Position::where('code', 'IT001')->first();
        $itSupportPos = Position::where('code', 'IT002')->first();

        $employees = [
            [
                'employee_id' => 'EMP202501001',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'admin@itms.com',
                'phone' => '02-123-4567',
                'department_id' => $itDept->id,
                'position_id' => $itAdminPos->id,
                'hire_date' => '2024-01-15',
                'employment_status' => 'active',
                'salary' => 55000,
            ],
            [
                'employee_id' => 'EMP202501002',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'manager@itms.com',
                'phone' => '02-123-4568',
                'department_id' => $itDept->id,
                'position_id' => $itManagerPos->id,
                'hire_date' => '2023-06-01',
                'employment_status' => 'active',
                'salary' => 70000,
            ],
            [
                'employee_id' => 'EMP202501003',
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'email' => 'employee@itms.com',
                'phone' => '02-123-4569',
                'department_id' => $itDept->id,
                'position_id' => $itSupportPos->id,
                'hire_date' => '2024-03-01',
                'employment_status' => 'active',
                'salary' => 30000,
            ],
        ];

        foreach ($employees as $emp) {
            Employee::updateOrCreate(
                ['employee_id' => $emp['employee_id']],
                $emp
            );
        }

        // Set supervisor relationship
        $manager = Employee::where('employee_id', 'EMP202501002')->first();
        $employee = Employee::where('employee_id', 'EMP202501003')->first();
        $employee->update(['supervisor_id' => $manager->id]);

        // Update department manager
        $itDept->update(['manager_id' => $manager->id]);
    }
}

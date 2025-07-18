<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Modules\Employee\Models\Employee;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            $role = match($employee->position->level) {
                'manager' => 'manager',
                'senior' => 'admin',
                default => 'employee'
            };

            $permissions = match($role) {
                'admin' => [
                    'employee.view', 'employee.create', 'employee.update', 'employee.delete',
                    'asset.view', 'asset.create', 'asset.update', 'asset.delete',
                    'incident.view', 'incident.create', 'incident.update', 'incident.delete',
                    'service_request.view', 'service_request.create', 'service_request.update', 'service_request.delete',
                    'agreement.view', 'agreement.create', 'agreement.update', 'agreement.delete',
                ],
                'manager' => [
                    'employee.view', 'employee.create', 'employee.update',
                    'asset.view', 'asset.create', 'asset.update',
                    'incident.view', 'incident.create', 'incident.update',
                    'service_request.view', 'service_request.create', 'service_request.update',
                    'agreement.view', 'agreement.create', 'agreement.update',
                ],
                'employee' => [
                    'incident.view', 'incident.create',
                    'service_request.view', 'service_request.create',
                ],
                default => []
            };

            User::create([
                'name' => $employee->full_name,
                'email' => $employee->email,
                'password' => Hash::make('password'),
                'role' => $role,
                'permissions' => $permissions,
                'employee_id' => $employee->id,
                'is_active' => true,
            ]);
        }
    }
}

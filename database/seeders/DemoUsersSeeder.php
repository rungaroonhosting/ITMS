<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@itms.com'],
            [
                'name' => 'Super Administrator',
                'email' => 'admin@itms.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'permissions' => ['*'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // IT Admin
        User::updateOrCreate(
            ['email' => 'manager@itms.com'],
            [
                'name' => 'IT Manager',
                'email' => 'manager@itms.com',
                'password' => Hash::make('password'),
                'role' => 'it_admin',
                'permissions' => [
                    'employees.view', 'employees.create', 'employees.update',
                    'assets.view', 'assets.create', 'assets.update', 'assets.delete',
                    'incidents.view', 'incidents.create', 'incidents.update', 'incidents.delete',
                    'service_requests.view', 'service_requests.create', 'service_requests.update',
                    'agreements.view', 'agreements.create', 'agreements.update',
                    'reports.view',
                ],
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Employee
        User::updateOrCreate(
            ['email' => 'employee@itms.com'],
            [
                'name' => 'John Employee',
                'email' => 'employee@itms.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'permissions' => [
                    'incidents.view', 'incidents.create',
                    'service_requests.view', 'service_requests.create',
                    'assets.view',
                ],
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Additional test users
        User::updateOrCreate(
            ['email' => 'test.admin@itms.com'],
            [
                'name' => 'Test IT Admin',
                'email' => 'test.admin@itms.com',
                'password' => Hash::make('password'),
                'role' => 'it_admin',
                'permissions' => [
                    'employees.view', 'employees.create', 'employees.update',
                    'assets.view', 'assets.create', 'assets.update',
                    'incidents.view', 'incidents.create', 'incidents.update',
                    'service_requests.view', 'service_requests.create', 'service_requests.update',
                ],
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'jane.employee@itms.com'],
            [
                'name' => 'Jane Employee',
                'email' => 'jane.employee@itms.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'permissions' => [
                    'incidents.view', 'incidents.create',
                    'service_requests.view', 'service_requests.create',
                    'assets.view',
                ],
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}

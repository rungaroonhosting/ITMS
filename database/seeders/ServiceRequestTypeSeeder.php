<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\ServiceRequest\Models\ServiceRequestType;

class ServiceRequestTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'NEW_ACCOUNT', 'name' => 'New Account Request', 'description' => 'Request for new user account', 'color' => '#17a2b8', 'sla_hours' => 48, 'requires_approval' => true],
            ['code' => 'PASSWORD_RESET', 'name' => 'Password Reset', 'description' => 'Request to reset password', 'color' => '#ffc107', 'sla_hours' => 4, 'requires_approval' => false],
            ['code' => 'SOFTWARE_INSTALL', 'name' => 'Software Installation', 'description' => 'Request to install software', 'color' => '#28a745', 'sla_hours' => 24, 'requires_approval' => true],
            ['code' => 'HARDWARE_REQUEST', 'name' => 'Hardware Request', 'description' => 'Request for hardware equipment', 'color' => '#fd7e14', 'sla_hours' => 72, 'requires_approval' => true],
            ['code' => 'ACCESS_REQUEST', 'name' => 'Access Request', 'description' => 'Request for system access', 'color' => '#6f42c1', 'sla_hours' => 48, 'requires_approval' => true],
            ['code' => 'MAINTENANCE', 'name' => 'Maintenance Request', 'description' => 'Request for system maintenance', 'color' => '#dc3545', 'sla_hours' => 48, 'requires_approval' => false],
            ['code' => 'TRAINING', 'name' => 'Training Request', 'description' => 'Request for training or support', 'color' => '#20c997', 'sla_hours' => 72, 'requires_approval' => false],
            ['code' => 'OTHER', 'name' => 'Other Request', 'description' => 'Other types of service requests', 'color' => '#6c757d', 'sla_hours' => 72, 'requires_approval' => false],
        ];

        foreach ($types as $type) {
            ServiceRequestType::create($type);
        }
    }
}

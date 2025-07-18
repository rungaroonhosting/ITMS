<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Incident\Models\IncidentType;

class IncidentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'HARDWARE', 'name' => 'Hardware Issue', 'description' => 'Hardware malfunction or failure', 'color' => '#dc3545', 'default_priority_level' => 3, 'sla_hours' => 4],
            ['code' => 'SOFTWARE', 'name' => 'Software Issue', 'description' => 'Software bugs or application problems', 'color' => '#ffc107', 'default_priority_level' => 2, 'sla_hours' => 8],
            ['code' => 'NETWORK', 'name' => 'Network Problem', 'description' => 'Network connectivity issues', 'color' => '#fd7e14', 'default_priority_level' => 3, 'sla_hours' => 2],
            ['code' => 'SECURITY', 'name' => 'Security Incident', 'description' => 'Security breaches or vulnerabilities', 'color' => '#6f42c1', 'default_priority_level' => 4, 'sla_hours' => 1],
            ['code' => 'ACCESS', 'name' => 'Access Issue', 'description' => 'Login or permission problems', 'color' => '#17a2b8', 'default_priority_level' => 2, 'sla_hours' => 4],
            ['code' => 'PERFORMANCE', 'name' => 'Performance Issue', 'description' => 'System or application performance problems', 'color' => '#28a745', 'default_priority_level' => 2, 'sla_hours' => 12],
            ['code' => 'OTHER', 'name' => 'Other', 'description' => 'Other types of incidents', 'color' => '#6c757d', 'default_priority_level' => 2, 'sla_hours' => 24],
        ];

        foreach ($types as $type) {
            IncidentType::create($type);
        }
    }
}

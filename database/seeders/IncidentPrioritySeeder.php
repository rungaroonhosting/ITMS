<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Incident\Models\IncidentPriority;

class IncidentPrioritySeeder extends Seeder
{
    public function run(): void
    {
        $priorities = [
            ['code' => 'LOW', 'name' => 'Low', 'description' => 'Low priority incident', 'level' => 1, 'color' => '#28a745', 'response_time_hours' => 24, 'resolution_time_hours' => 72],
            ['code' => 'MEDIUM', 'name' => 'Medium', 'description' => 'Medium priority incident', 'level' => 2, 'color' => '#ffc107', 'response_time_hours' => 8, 'resolution_time_hours' => 48],
            ['code' => 'HIGH', 'name' => 'High', 'description' => 'High priority incident', 'level' => 3, 'color' => '#fd7e14', 'response_time_hours' => 4, 'resolution_time_hours' => 24],
            ['code' => 'CRITICAL', 'name' => 'Critical', 'description' => 'Critical priority incident', 'level' => 4, 'color' => '#dc3545', 'response_time_hours' => 1, 'resolution_time_hours' => 8],
        ];

        foreach ($priorities as $priority) {
            IncidentPriority::create($priority);
        }
    }
}

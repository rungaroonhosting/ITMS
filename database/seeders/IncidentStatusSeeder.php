<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Incident\Models\IncidentStatus;

class IncidentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'NEW', 'name' => 'New', 'description' => 'Newly reported incident', 'color' => '#17a2b8', 'is_closed' => false, 'sort_order' => 1],
            ['code' => 'ASSIGNED', 'name' => 'Assigned', 'description' => 'Incident assigned to technician', 'color' => '#ffc107', 'is_closed' => false, 'sort_order' => 2],
            ['code' => 'IN_PROGRESS', 'name' => 'In Progress', 'description' => 'Incident is being worked on', 'color' => '#fd7e14', 'is_closed' => false, 'sort_order' => 3],
            ['code' => 'PENDING', 'name' => 'Pending', 'description' => 'Waiting for additional information', 'color' => '#6f42c1', 'is_closed' => false, 'sort_order' => 4],
            ['code' => 'RESOLVED', 'name' => 'Resolved', 'description' => 'Incident has been resolved', 'color' => '#28a745', 'is_closed' => false, 'sort_order' => 5],
            ['code' => 'CLOSED', 'name' => 'Closed', 'description' => 'Incident is closed', 'color' => '#6c757d', 'is_closed' => true, 'sort_order' => 6],
            ['code' => 'CANCELLED', 'name' => 'Cancelled', 'description' => 'Incident was cancelled', 'color' => '#dc3545', 'is_closed' => true, 'sort_order' => 7],
        ];

        foreach ($statuses as $status) {
            IncidentStatus::create($status);
        }
    }
}

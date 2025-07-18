<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Asset\Models\AssetStatus;

class AssetStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'AVAILABLE', 'name' => 'Available', 'description' => 'Asset is available for assignment', 'color' => '#28a745', 'sort_order' => 1],
            ['code' => 'ASSIGNED', 'name' => 'Assigned', 'description' => 'Asset is assigned to an employee', 'color' => '#17a2b8', 'sort_order' => 2],
            ['code' => 'IN_USE', 'name' => 'In Use', 'description' => 'Asset is currently being used', 'color' => '#007bff', 'sort_order' => 3],
            ['code' => 'MAINTENANCE', 'name' => 'Under Maintenance', 'description' => 'Asset is under maintenance', 'color' => '#ffc107', 'sort_order' => 4],
            ['code' => 'REPAIR', 'name' => 'Under Repair', 'description' => 'Asset is being repaired', 'color' => '#fd7e14', 'sort_order' => 5],
            ['code' => 'DAMAGED', 'name' => 'Damaged', 'description' => 'Asset is damaged', 'color' => '#dc3545', 'sort_order' => 6],
            ['code' => 'LOST', 'name' => 'Lost', 'description' => 'Asset is lost or missing', 'color' => '#6f42c1', 'sort_order' => 7],
            ['code' => 'DISPOSED', 'name' => 'Disposed', 'description' => 'Asset has been disposed', 'color' => '#6c757d', 'sort_order' => 8],
            ['code' => 'RETIRED', 'name' => 'Retired', 'description' => 'Asset has been retired', 'color' => '#343a40', 'sort_order' => 9],
        ];

        foreach ($statuses as $status) {
            AssetStatus::create($status);
        }
    }
}

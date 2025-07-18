<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Agreement\Models\AgreementType;

class AgreementTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'SLA', 'name' => 'Service Level Agreement', 'description' => 'Agreement defining service levels', 'color' => '#17a2b8'],
            ['code' => 'LICENSE', 'name' => 'Software License', 'description' => 'Software licensing agreement', 'color' => '#28a745'],
            ['code' => 'SUPPORT', 'name' => 'Support Contract', 'description' => 'Technical support agreement', 'color' => '#ffc107'],
            ['code' => 'MAINTENANCE', 'name' => 'Maintenance Agreement', 'description' => 'Equipment maintenance contract', 'color' => '#fd7e14'],
            ['code' => 'LEASE', 'name' => 'Equipment Lease', 'description' => 'Equipment leasing agreement', 'color' => '#6f42c1'],
            ['code' => 'VENDOR', 'name' => 'Vendor Agreement', 'description' => 'General vendor agreement', 'color' => '#dc3545'],
            ['code' => 'OTHER', 'name' => 'Other Agreement', 'description' => 'Other types of agreements', 'color' => '#6c757d'],
        ];

        foreach ($types as $type) {
            AgreementType::create($type);
        }
    }
}

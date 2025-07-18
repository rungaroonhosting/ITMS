<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ITMSSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class,
            EmployeeSeeder::class,
            UserSeeder::class,
            AssetCategorySeeder::class,
            AssetStatusSeeder::class,
            IncidentTypeSeeder::class,
            IncidentPrioritySeeder::class,
            IncidentStatusSeeder::class,
            ServiceRequestTypeSeeder::class,
            AgreementTypeSeeder::class,
        ]);
    }
}

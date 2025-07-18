<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Asset\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'COMP', 'name' => 'Computer Hardware', 'description' => 'Desktop computers, laptops, servers', 'depreciation_years' => 5],
            ['code' => 'NET', 'name' => 'Network Equipment', 'description' => 'Routers, switches, access points', 'depreciation_years' => 7],
            ['code' => 'PRINT', 'name' => 'Printing Devices', 'description' => 'Printers, scanners, copiers', 'depreciation_years' => 5],
            ['code' => 'MOB', 'name' => 'Mobile Devices', 'description' => 'Smartphones, tablets', 'depreciation_years' => 3],
            ['code' => 'SOFT', 'name' => 'Software', 'description' => 'Software licenses and applications', 'depreciation_years' => 3],
            ['code' => 'FURN', 'name' => 'Furniture', 'description' => 'Office furniture and equipment', 'depreciation_years' => 10],
        ];

        foreach ($categories as $category) {
            AssetCategory::create($category);
        }

        // Create subcategories
        $compCategory = AssetCategory::where('code', 'COMP')->first();
        $subcategories = [
            ['code' => 'DESKTOP', 'name' => 'Desktop Computer', 'parent_id' => $compCategory->id, 'depreciation_years' => 5],
            ['code' => 'LAPTOP', 'name' => 'Laptop Computer', 'parent_id' => $compCategory->id, 'depreciation_years' => 4],
            ['code' => 'SERVER', 'name' => 'Server', 'parent_id' => $compCategory->id, 'depreciation_years' => 7],
        ];

        foreach ($subcategories as $subcategory) {
            AssetCategory::create($subcategory);
        }
    }
}

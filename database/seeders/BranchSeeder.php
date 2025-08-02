<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\User;

class BranchSeeder extends Seeder
{
    public function run()
    {
        // Get some users to assign as managers
        $users = User::all();
        
        $branches = [
            [
                'name' => 'สำนักงานใหญ่',
                'code' => 'HQ001',
                'address' => '123 ถ.สีลม แขวงสีลม เขตบางรัก กรุงเทพฯ 10500',
                'phone' => '02-234-5678',
                'email' => 'hq@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาเซ็นทรัลเวิลด์',
                'code' => 'CW002',
                'address' => '999/9 ถ.ราม 1 แขวงปทุมวัน เขตปทุมวัน กรุงเทพฯ 10330',
                'phone' => '02-345-6789',
                'email' => 'centralworld@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาสยามพารากอน',
                'code' => 'SP003',
                'address' => '991 ถ.ราม 1 แขวงปทุมวัน เขตปทุมวัน กรุงเทพฯ 10330',
                'phone' => '02-456-7890',
                'email' => 'siamparagon@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาเมกาบางนา',
                'code' => 'MB004',
                'address' => '39 ถ.บางนา-ตราด กม.8 บางนา กรุงเทพฯ 10260',
                'phone' => '02-567-8901',
                'email' => 'megabangna@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาฟิวเจอร์พาร์ครังสิต',
                'code' => 'FP005',
                'address' => '94 ถ.พหลโยธิน กม.42 รังสิต ธัญบุรี ปทุมธานี 12110',
                'phone' => '02-678-9012',
                'email' => 'futurepark@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาเซ็นทรัลพิษณุโลก',
                'code' => 'CP006',
                'address' => '43/99 ถ.เอเซีย แขวงในเมือง เขตเมือง พิษณุโลก 65000',
                'phone' => '055-789-012',
                'email' => 'centralphitsanulok@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาเซ็นทรัลขอนแก่น',
                'code' => 'CK007',
                'address' => '222 ถ.มิตรภาพ แขวงในเมือง เขตเมือง ขอนแก่น 40000',
                'phone' => '043-890-123',
                'email' => 'centralkhonkaen@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาเซ็นทรัลหาดใหญ่',
                'code' => 'CH008',
                'address' => '55 ถ.เพชรเกษม แขวงหาดใหญ่ เขตหาดใหญ่ สงขลา 90110',
                'phone' => '074-901-234',
                'email' => 'centralhatyai@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาเซ็นทรัลเชียงใหม่',
                'code' => 'CC009',
                'address' => '99 ถ.ห้วยแก้ว แขวงช้างเผือก เขตเมือง เชียงใหม่ 50300',
                'phone' => '053-012-345',
                'email' => 'centralchiangmai@company.com',
                'is_active' => false, // Temporarily closed
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
            [
                'name' => 'สาขาออนไลน์',
                'code' => 'ON010',
                'address' => 'Online Store - Virtual Address',
                'phone' => '02-123-4567',
                'email' => 'online@company.com',
                'is_active' => true,
                'manager_id' => $users->isNotEmpty() ? $users->random()->id : null,
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::create($branchData);
        }

        $this->command->info('Created 10 branches with sample data.');
    }
}

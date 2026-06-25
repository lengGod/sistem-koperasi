<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            [
                'member_number' => 'MBR-000001',
                'nik' => '3175011201010001',
                'name' => 'Budi Santoso',
                'gender' => 'male',
                'birth_place' => 'Jakarta',
                'birth_date' => '1987-04-12',
                'phone' => '081234567890',
                'email' => 'budi.santoso@example.com',
                'address' => 'Jl. Melati No. 12, Jakarta Selatan',
                'joined_at' => '2023-01-10',
                'status' => 'active',
            ],
            [
                'member_number' => 'MBR-000002',
                'nik' => '3175011201010002',
                'name' => 'Siti Aminah',
                'gender' => 'female',
                'birth_place' => 'Bandung',
                'birth_date' => '1990-09-23',
                'phone' => '081298765432',
                'email' => 'siti.aminah@example.com',
                'address' => 'Jl. Anggrek No. 8, Bandung',
                'joined_at' => '2023-02-15',
                'status' => 'active',
            ],
            [
                'member_number' => 'MBR-000003',
                'nik' => '3175011201010003',
                'name' => 'Andi Wijaya',
                'gender' => 'male',
                'birth_place' => 'Surabaya',
                'birth_date' => '1985-11-07',
                'phone' => '081377788899',
                'email' => 'andi.wijaya@example.com',
                'address' => 'Jl. Mawar No. 21, Surabaya',
                'joined_at' => '2023-03-05',
                'status' => 'inactive',
            ],
        ];

        foreach ($members as $member) {
            Member::updateOrCreate(
                ['member_number' => $member['member_number']],
                $member
            );
        }

        Member::factory()->count(12)->create();
    }
}

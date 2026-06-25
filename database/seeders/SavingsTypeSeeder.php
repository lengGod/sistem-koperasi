<?php

namespace Database\Seeders;

use App\Models\SavingsType;
use Illuminate\Database\Seeder;

class SavingsTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'POKOK',
                'name' => 'Simpanan Pokok',
                'description' => 'Setoran pokok yang dibayarkan saat menjadi anggota koperasi.',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'code' => 'WAJIB',
                'name' => 'Simpanan Wajib',
                'description' => 'Setoran rutin anggota sesuai ketentuan koperasi.',
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'code' => 'SUKARELA',
                'name' => 'Simpanan Sukarela',
                'description' => 'Setoran tambahan yang bersifat fleksibel.',
                'is_mandatory' => false,
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            SavingsType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}

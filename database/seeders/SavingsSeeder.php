<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Savings;
use App\Models\SavingsType;
use App\Models\User;
use Illuminate\Database\Seeder;

class SavingsSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::where('email', 'admin@koperasi.com')->value('id');

        $records = [
            [
                'reference_number' => 'SAV-2026-0001',
                'member_number' => 'MBR-000001',
                'savings_code' => 'POKOK',
                'transaction_type' => 'deposit',
                'amount' => 1000000,
                'transaction_date' => now()->startOfMonth()->addDays(2)->toDateString(),
                'notes' => 'Setoran awal keanggotaan.',
            ],
            [
                'reference_number' => 'SAV-2026-0002',
                'member_number' => 'MBR-000002',
                'savings_code' => 'WAJIB',
                'transaction_type' => 'deposit',
                'amount' => 150000,
                'transaction_date' => now()->startOfMonth()->addDays(5)->toDateString(),
                'notes' => 'Setoran wajib bulanan.',
            ],
            [
                'reference_number' => 'SAV-2026-0003',
                'member_number' => 'MBR-000003',
                'savings_code' => 'SUKARELA',
                'transaction_type' => 'deposit',
                'amount' => 500000,
                'transaction_date' => now()->subMonthNoOverflow()->startOfMonth()->addDays(11)->toDateString(),
                'notes' => 'Setoran sukarela.',
            ],
            [
                'reference_number' => 'SAV-2026-0004',
                'member_number' => 'MBR-000001',
                'savings_code' => 'SUKARELA',
                'transaction_type' => 'withdrawal',
                'amount' => 200000,
                'transaction_date' => now()->subMonthNoOverflow()->startOfMonth()->addDays(20)->toDateString(),
                'notes' => 'Penarikan sebagian simpanan.',
            ],
            [
                'reference_number' => 'SAV-2026-0005',
                'member_number' => 'MBR-000002',
                'savings_code' => 'WAJIB',
                'transaction_type' => 'deposit',
                'amount' => 150000,
                'transaction_date' => now()->subMonthsNoOverflow(2)->startOfMonth()->addDays(7)->toDateString(),
                'notes' => 'Setoran wajib bulanan.',
            ],
            [
                'reference_number' => 'SAV-2026-0006',
                'member_number' => 'MBR-000003',
                'savings_code' => 'SUKARELA',
                'transaction_type' => 'deposit',
                'amount' => 750000,
                'transaction_date' => now()->subMonthsNoOverflow(3)->startOfMonth()->addDays(14)->toDateString(),
                'notes' => 'Setoran sukarela tambahan.',
            ],
        ];

        foreach ($records as $record) {
            $memberId = Member::where('member_number', $record['member_number'])->value('id');
            $savingsTypeId = SavingsType::where('code', $record['savings_code'])->value('id');

            if (! $memberId || ! $savingsTypeId) {
                continue;
            }

            Savings::updateOrCreate(
                ['reference_number' => $record['reference_number']],
                [
                    'member_id' => $memberId,
                    'savings_type_id' => $savingsTypeId,
                    'transaction_type' => $record['transaction_type'],
                    'amount' => $record['amount'],
                    'transaction_date' => $record['transaction_date'],
                    'notes' => $record['notes'],
                    'created_by' => $creatorId,
                ]
            );
        }
    }
}

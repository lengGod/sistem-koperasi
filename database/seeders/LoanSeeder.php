<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::where('email', 'admin@koperasi.com')->value('id');

        $records = [
            [
                'loan_number' => 'LN-2026-0001',
                'member_number' => 'MBR-000001',
                'principal_amount' => 12000000,
                'interest_rate' => 1.50,
                'term_months' => 12,
                'monthly_installment' => 1100000,
                'remaining_balance' => 9800000,
                'disbursed_at' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                'due_date' => now()->addMonthsNoOverflow(10)->endOfMonth()->toDateString(),
                'status' => 'active',
                'notes' => 'Pinjaman modal usaha.',
            ],
            [
                'loan_number' => 'LN-2026-0002',
                'member_number' => 'MBR-000002',
                'principal_amount' => 8000000,
                'interest_rate' => 1.25,
                'term_months' => 10,
                'monthly_installment' => 860000,
                'remaining_balance' => 5600000,
                'disbursed_at' => now()->subMonthsNoOverflow(2)->startOfMonth()->toDateString(),
                'due_date' => now()->addMonthsNoOverflow(8)->endOfMonth()->toDateString(),
                'status' => 'active',
                'notes' => 'Pinjaman renovasi rumah.',
            ],
            [
                'loan_number' => 'LN-2026-0003',
                'member_number' => 'MBR-000003',
                'principal_amount' => 5000000,
                'interest_rate' => 1.00,
                'term_months' => 6,
                'monthly_installment' => 850000,
                'remaining_balance' => 0,
                'disbursed_at' => now()->subMonthsNoOverflow(8)->startOfMonth()->toDateString(),
                'due_date' => now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
                'status' => 'completed',
                'notes' => 'Pinjaman yang sudah lunas.',
            ],
        ];

        foreach ($records as $record) {
            $memberId = Member::where('member_number', $record['member_number'])->value('id');

            if (! $memberId) {
                continue;
            }

            Loan::updateOrCreate(
                ['loan_number' => $record['loan_number']],
                [
                    'member_id' => $memberId,
                    'principal_amount' => $record['principal_amount'],
                    'interest_rate' => $record['interest_rate'],
                    'term_months' => $record['term_months'],
                    'monthly_installment' => $record['monthly_installment'],
                    'remaining_balance' => $record['remaining_balance'],
                    'disbursed_at' => $record['disbursed_at'],
                    'due_date' => $record['due_date'],
                    'status' => $record['status'],
                    'notes' => $record['notes'],
                    'created_by' => $creatorId,
                ]
            );
        }
    }
}

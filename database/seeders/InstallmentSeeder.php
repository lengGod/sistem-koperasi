<?php

namespace Database\Seeders;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstallmentSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::where('email', 'admin@koperasi.com')->value('id');

        $records = [
            [
                'loan_number' => 'LN-2026-0001',
                'installment_number' => 1,
                'due_date' => now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
                'principal_amount' => 800000,
                'interest_amount' => 300000,
                'amount' => 1100000,
                'paid_amount' => 1100000,
                'paid_at' => now()->subMonthNoOverflow()->endOfMonth()->subDays(1)->toDateString(),
                'status' => 'paid',
                'notes' => 'Pembayaran pertama.',
            ],
            [
                'loan_number' => 'LN-2026-0001',
                'installment_number' => 2,
                'due_date' => now()->startOfMonth()->addDays(10)->toDateString(),
                'principal_amount' => 800000,
                'interest_amount' => 300000,
                'amount' => 1100000,
                'paid_amount' => 1100000,
                'paid_at' => now()->startOfMonth()->addDays(12)->toDateString(),
                'status' => 'paid',
                'notes' => 'Pembayaran kedua.',
            ],
            [
                'loan_number' => 'LN-2026-0001',
                'installment_number' => 3,
                'due_date' => now()->addDays(15)->toDateString(),
                'principal_amount' => 800000,
                'interest_amount' => 300000,
                'amount' => 1100000,
                'paid_amount' => 0,
                'paid_at' => null,
                'status' => 'pending',
                'notes' => 'Menunggu jatuh tempo.',
            ],
            [
                'loan_number' => 'LN-2026-0002',
                'installment_number' => 1,
                'due_date' => now()->subDays(20)->toDateString(),
                'principal_amount' => 600000,
                'interest_amount' => 260000,
                'amount' => 860000,
                'paid_amount' => 500000,
                'paid_at' => now()->subDays(18)->toDateString(),
                'status' => 'partial',
                'notes' => 'Pembayaran sebagian.',
            ],
            [
                'loan_number' => 'LN-2026-0002',
                'installment_number' => 2,
                'due_date' => now()->subDays(3)->toDateString(),
                'principal_amount' => 600000,
                'interest_amount' => 260000,
                'amount' => 860000,
                'paid_amount' => 0,
                'paid_at' => null,
                'status' => 'late',
                'notes' => 'Belum dibayar.',
            ],
            [
                'loan_number' => 'LN-2026-0003',
                'installment_number' => 6,
                'due_date' => now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
                'principal_amount' => 600000,
                'interest_amount' => 250000,
                'amount' => 850000,
                'paid_amount' => 850000,
                'paid_at' => now()->subMonthNoOverflow()->endOfMonth()->subDay()->toDateString(),
                'status' => 'paid',
                'notes' => 'Cicilan terakhir.',
            ],
        ];

        foreach ($records as $record) {
            $loanId = Loan::where('loan_number', $record['loan_number'])->value('id');

            if (! $loanId) {
                continue;
            }

            Installment::updateOrCreate(
                [
                    'loan_id' => $loanId,
                    'installment_number' => $record['installment_number'],
                ],
                [
                    'due_date' => $record['due_date'],
                    'principal_amount' => $record['principal_amount'],
                    'interest_amount' => $record['interest_amount'],
                    'amount' => $record['amount'],
                    'paid_amount' => $record['paid_amount'],
                    'paid_at' => $record['paid_at'],
                    'status' => $record['status'],
                    'notes' => $record['notes'],
                    'created_by' => $creatorId,
                ]
            );
        }
    }
}

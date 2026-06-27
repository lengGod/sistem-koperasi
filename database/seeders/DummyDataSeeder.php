<?php

namespace Database\Seeders;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Savings;
use App\Models\SavingsType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::first();
        $creatorId = $creator ? $creator->id : null;
        
        $pokokTypeId = SavingsType::where('code', 'POKOK')->value('id');
        $wajibTypeId = SavingsType::where('code', 'WAJIB')->value('id');

        for ($i = 1; $i <= 5; $i++) {
            // 1. Create Member
            $member = Member::create([
                'member_number' => 'DUMMY-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Dummy Member ' . $i,
                'work_unit' => 'DUMMY_UNIT',
                'status' => 'active',
                'joined_at' => now(),
            ]);

            // 2. Create Savings
            Savings::create([
                'member_id' => $member->id,
                'savings_type_id' => $pokokTypeId,
                'transaction_type' => 'deposit',
                'amount' => 250000,
                'transaction_date' => '2026-06-01',
                'notes' => 'Simpanan Pokok Dummy',
                'reference_number' => 'D-POKOK-' . $member->id,
                'created_by' => $creatorId,
            ]);

            Savings::create([
                'member_id' => $member->id,
                'savings_type_id' => $wajibTypeId,
                'transaction_type' => 'deposit',
                'amount' => 100000,
                'transaction_date' => '2026-06-01',
                'notes' => 'Simpanan Wajib Dummy',
                'reference_number' => 'D-WAJIB-' . $member->id,
                'created_by' => $creatorId,
            ]);

            // 3. Create Loan
            $loan = Loan::create([
                'member_id' => $member->id,
                'loan_number' => 'L-DUMMY-' . $member->id,
                'principal_amount' => 1000000,
                'term_months' => 6,
                'monthly_installment' => 200000,
                'remaining_balance' => 800000,
                'status' => 'active',
                'created_by' => $creatorId,
            ]);

            // 4. Create Installments
            Installment::create([
                'loan_id' => $loan->id,
                'installment_number' => 1,
                'due_date' => now()->addMonth(),
                'amount' => 200000,
                'status' => 'pending',
                'created_by' => $creatorId,
            ]);
            
            Installment::create([
                'loan_id' => $loan->id,
                'installment_number' => 2,
                'due_date' => now()->addMonths(2),
                'amount' => 200000,
                'status' => 'paid',
                'paid_at' => now()->addMonths(1),
                'created_by' => $creatorId,
            ]);
        }
    }
}

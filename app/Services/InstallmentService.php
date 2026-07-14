<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class InstallmentService
{
    public function create(array $data): Installment
    {
        return DB::transaction(function () use ($data) {
            $installment = Installment::create($data);
            if (($installment->paid_amount ?? 0) > 0) {
                $loan = Loan::find($installment->loan_id);
                $loan->decrement('remaining_balance', $installment->paid_amount);
            }
            return $installment;
        });
    }

    public function update(Installment $installment, array $data): Installment
    {
        return DB::transaction(function () use ($installment, $data) {
            $oldPaidAmount = $installment->paid_amount ?? 0;
            $newPaidAmount = $data['paid_amount'] ?? 0;

            $installment->update($data);

            if ($oldPaidAmount != $newPaidAmount) {
                $loan = Loan::find($installment->loan_id);
                $diff = $newPaidAmount - $oldPaidAmount;
                // If diff > 0, we paid more, so remaining decreases
                // If diff < 0, we paid less, so remaining increases
                if ($diff > 0) {
                    $loan->decrement('remaining_balance', $diff);
                } else {
                    $loan->increment('remaining_balance', abs($diff));
                }
            }
            return $installment;
        });
    }

    public function delete(Installment $installment): void
    {
        DB::transaction(function () use ($installment) {
            if (($installment->paid_amount ?? 0) > 0) {
                $loan = Loan::find($installment->loan_id);
                $loan->increment('remaining_balance', $installment->paid_amount);
            }
            $installment->delete();
        });
    }

    public function deleteMany(\Illuminate\Support\Collection $installmentIds): int
    {
        return DB::transaction(function () use ($installmentIds) {
            $installments = Installment::whereIn('id', $installmentIds)->get();
            
            $adjustments = [];
            foreach ($installments as $installment) {
                if (($installment->paid_amount ?? 0) > 0) {
                    $loanId = $installment->loan_id;
                    $adjustments[$loanId] = ($adjustments[$loanId] ?? 0) + $installment->paid_amount;
                }
            }
            
            foreach ($adjustments as $loanId => $amount) {
                Loan::where('id', $loanId)->increment('remaining_balance', $amount);
            }
            
            return Installment::whereIn('id', $installmentIds)->delete();
        });
    }
}

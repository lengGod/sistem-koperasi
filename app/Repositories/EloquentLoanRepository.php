<?php

namespace App\Repositories;

use App\Models\Loan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentLoanRepository implements LoanRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Loan::query()
            ->with('member')
            ->latest('disbursed_at')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Loan
    {
        return Loan::create($data);
    }

    public function update(Loan $loan, array $data): Loan
    {
        $loan->update($data);

        return $loan->refresh();
    }

    public function delete(Loan $loan): void
    {
        $loan->delete();
    }

    public function deleteMany(\Illuminate\Support\Collection $loanIds): int
    {
        return Loan::query()
            ->whereIn('id', $loanIds->all())
            ->delete();
    }
}

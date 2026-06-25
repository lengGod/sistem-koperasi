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
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('loan_number', 'like', "%{$search}%")
                        ->orWhereHas('member', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('member_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
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

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
                                ->orWhere('member_number', 'like', "%{$search}%")
                                ->orWhere('account_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('loans.status', $status))
            ->when(($filters['sort'] ?? null) === 'member_name', fn ($query) => $query->join('members', 'loans.member_id', '=', 'members.id')->orderBy('members.name')->select('loans.*')->orderBy('loans.loan_number'), fn ($query) => $query->join('members', 'loans.member_id', '=', 'members.id')->orderBy('members.name')->orderBy('loans.loan_number')->select('loans.*'))
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

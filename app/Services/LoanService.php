<?php

namespace App\Services;

use App\Models\Loan;
use App\Repositories\LoanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LoanService
{
    public function __construct(private readonly LoanRepositoryInterface $loans)
    {
    }

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->loans->paginate($filters, $perPage);
    }

    public function create(array $data): Loan
    {
        return $this->loans->create($data);
    }

    public function update(Loan $loan, array $data): Loan
    {
        return $this->loans->update($loan, $data);
    }

    public function delete(Loan $loan): void
    {
        $this->loans->delete($loan);
    }

    public function deleteMany(\Illuminate\Support\Collection $loanIds): int
    {
        return $this->loans->deleteMany($loanIds);
    }
}

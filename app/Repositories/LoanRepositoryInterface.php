<?php

namespace App\Repositories;

use App\Models\Loan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LoanRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Loan;

    public function update(Loan $loan, array $data): Loan;

    public function delete(Loan $loan): void;

    public function deleteMany(\Illuminate\Support\Collection $loanIds): int;
}

<?php

namespace App\Repositories;

use App\Models\Savings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SavingsRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Savings;

    public function update(Savings $savings, array $data): Savings;

    public function delete(Savings $savings): void;

    public function deleteMany(\Illuminate\Support\Collection $savingsIds): int;
}

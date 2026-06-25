<?php

namespace App\Services;

use App\Models\Savings;
use App\Repositories\SavingsRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SavingsService
{
    public function __construct(private readonly SavingsRepositoryInterface $savings)
    {
    }

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->savings->paginate($filters, $perPage);
    }

    public function create(array $data): Savings
    {
        return $this->savings->create($data);
    }

    public function update(Savings $savings, array $data): Savings
    {
        return $this->savings->update($savings, $data);
    }

    public function delete(Savings $savings): void
    {
        $this->savings->delete($savings);
    }

    public function deleteMany(\Illuminate\Support\Collection $savingsIds): int
    {
        return $this->savings->deleteMany($savingsIds);
    }
}

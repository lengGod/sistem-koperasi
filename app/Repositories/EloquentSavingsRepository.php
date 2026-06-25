<?php

namespace App\Repositories;

use App\Models\Savings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSavingsRepository implements SavingsRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Savings::query()
            ->with(['member', 'savingsType'])
            ->latest('transaction_date')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Savings
    {
        return Savings::create($data);
    }

    public function update(Savings $savings, array $data): Savings
    {
        $savings->update($data);

        return $savings->refresh();
    }

    public function delete(Savings $savings): void
    {
        $savings->delete();
    }

    public function deleteMany(\Illuminate\Support\Collection $savingsIds): int
    {
        return Savings::query()
            ->whereIn('id', $savingsIds->all())
            ->delete();
    }
}

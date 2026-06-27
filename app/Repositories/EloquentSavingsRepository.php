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
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('member', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('member_number', 'like', "%{$search}%")
                                ->orWhere('account_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['type'] ?? null, fn ($query, string $type) => $query->where('transaction_type', $type))
            ->when(($filters['sort'] ?? null) === 'member_name', fn ($query) => $query->join('members', 'savings.member_id', '=', 'members.id')->orderBy('members.name')->orderBy('savings.transaction_date')->select('savings.*'), fn ($query) => $query->join('members', 'savings.member_id', '=', 'members.id')->orderBy('members.name')->orderBy('savings.transaction_date')->select('savings.*'))
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

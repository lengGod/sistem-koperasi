<?php

namespace App\Repositories;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentMemberRepository implements MemberRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Member::query()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('member_number', 'like', "%{$search}%")
                        ->orWhere('account_number', 'like', "%{$search}%")
                        ->orWhere('work_unit', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('employment_status', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['employment_status'] ?? null, fn ($query, string $status) => $query->where('employment_status', $status))
            ->when(($filters['sort'] ?? null) === 'name', fn ($query) => $query->orderBy('name')->orderBy('member_number'), fn ($query) => $query->orderBy('name')->orderBy('member_number'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Member
    {
        return Member::create($data);
    }

    public function update(Member $member, array $data): Member
    {
        $member->update($data);

        return $member->refresh();
    }

    public function delete(Member $member): void
    {
        $member->delete();
    }

    public function deleteMany(Collection $memberIds): int
    {
        return Member::query()
            ->whereIn('id', $memberIds->all())
            ->delete();
    }

    public function generateMemberNumber(): string
    {
        $latestMember = Member::query()
            ->where('member_number', 'like', 'KOP-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latestMember) {
            $number = (int) substr($latestMember->member_number, 4);
            return 'KOP-' . str_pad((string) ($number + 1), 4, '0', STR_PAD_LEFT);
        }

        return 'KOP-0001';
    }
}

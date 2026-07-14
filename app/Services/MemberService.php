<?php

namespace App\Services;

use App\Models\Member;
use App\Repositories\MemberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MemberService
{
    public function __construct(private readonly MemberRepositoryInterface $members)
    {
    }

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->members->paginate($filters, $perPage);
    }

    public function create(array $data): Member
    {
        if (empty($data['member_number'])) {
            $data['member_number'] = $this->members->generateMemberNumber();
        }

        return $this->members->create($data);
    }

    public function update(Member $member, array $data): Member
    {
        return $this->members->update($member, $data);
    }

    public function delete(Member $member): void
    {
        $this->members->delete($member);
    }

    public function deleteMany(Collection $memberIds): int
    {
        return $this->members->deleteMany($memberIds);
    }
}

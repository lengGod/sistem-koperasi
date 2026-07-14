<?php

namespace App\Repositories;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MemberRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Member;

    public function update(Member $member, array $data): Member;

    public function delete(Member $member): void;

    public function deleteMany(Collection $memberIds): int;

    public function generateMemberNumber(): string;
}

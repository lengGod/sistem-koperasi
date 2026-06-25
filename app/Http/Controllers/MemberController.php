<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(private readonly MemberService $members)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $members = $this->members->paginate($request->only(['search', 'status']));

        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('members.create', [
            'member' => new Member([
                'joined_at' => now()->toDateString(),
                'status' => 'active',
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = $this->members->create($request->validated());

        return redirect()
            ->route('members.show', $member)
            ->with('status', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): View
    {
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $this->members->update($member, $request->validated());

        return redirect()
            ->route('members.show', $member)
            ->with('status', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member): RedirectResponse
    {
        $this->members->delete($member);

        return redirect()
            ->route('members.index')
            ->with('status', 'Anggota berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['integer', 'distinct', 'exists:members,id'],
        ]);

        $deleted = $this->members->deleteMany(collect($validated['member_ids']));

        return redirect()
            ->route('members.index')
            ->with('status', $deleted > 1
                ? "{$deleted} anggota berhasil dihapus."
                : 'Anggota berhasil dihapus.');
    }
}

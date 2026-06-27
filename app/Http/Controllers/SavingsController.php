<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Savings;
use App\Models\SavingsType;
use App\Services\SavingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class SavingsController extends Controller
{
    public function __construct(private readonly SavingsService $savings)
    {
    }

    public function index(): View
    {
        $savings = $this->savings->paginate(request()->only(['search', 'type', 'sort']));

        return view('savings.index', compact('savings'));
    }

    public function create(): View
    {
        return view('savings.create', [
            'saving' => new Savings(['transaction_date' => now()->toDateString()]),
            'members' => Member::query()->orderBy('name')->get(),
            'savingsTypes' => SavingsType::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'savings_type_id' => ['required', 'exists:savings_types,id'],
            'transaction_type' => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount' => ['required', 'numeric', 'min:1000'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reference_number' => ['nullable', 'string', 'max:100', 'unique:savings,reference_number'],
        ]);

        $saving = $this->savings->create($data);

        return redirect()->route('savings.show', $saving)->with('status', 'Transaksi simpanan berhasil ditambahkan.');
    }

    public function show(Savings $saving): View
    {
        return view('savings.show', compact('saving'));
    }

    public function edit(Savings $saving): View
    {
        return view('savings.edit', [
            'saving' => $saving,
            'members' => Member::query()->orderBy('name')->get(),
            'savingsTypes' => SavingsType::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Savings $saving): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'savings_type_id' => ['required', 'exists:savings_types,id'],
            'transaction_type' => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount' => ['required', 'numeric', 'min:1000'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reference_number' => ['nullable', 'string', 'max:100', Rule::unique('savings', 'reference_number')->ignore($saving)],
        ]);

        $this->savings->update($saving, $data);

        return redirect()->route('savings.show', $saving)->with('status', 'Transaksi simpanan berhasil diperbarui.');
    }

    public function destroy(Savings $saving): RedirectResponse
    {
        $this->savings->delete($saving);

        return redirect()->route('savings.index')->with('status', 'Transaksi simpanan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'savings_ids' => ['required', 'array', 'min:1'],
            'savings_ids.*' => ['integer', 'distinct', 'exists:savings,id'],
        ]);

        $deleted = $this->savings->deleteMany(collect($validated['savings_ids']));

        return redirect()
            ->route('savings.index')
            ->with('status', $deleted > 1
                ? "{$deleted} transaksi simpanan berhasil dihapus."
                : 'Transaksi simpanan berhasil dihapus.');
    }
}

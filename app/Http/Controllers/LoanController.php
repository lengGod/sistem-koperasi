<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loans) {}

    public function index(Request $request): View
    {
        $loans = $this->loans->paginate($request->only(['search', 'status', 'sort']));

        return view('loans.index', compact('loans'));
    }

    public function create(Request $request): View
    {
        return view('loans.create', [
            'loan' => new Loan([
                'loan_number' => $this->generateLoanNumber(),
                'term_months' => 12,
                'disbursed_at' => now()->toDateString(),
                'status' => 'active',
                'member_id' => $request->query('member_id'),
            ]),
            'members' => Member::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'loan_number' => $request->filled('loan_number') ? $request->loan_number : $this->generateLoanNumber(),
            'status' => $request->filled('status') ? $request->status : 'active',
        ]);

        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'loan_number' => ['required', 'string', 'max:50', 'unique:loans,loan_number'],
            'principal_amount' => ['required', 'numeric', 'min:1000'],
            'interest_rate' => ['required', 'numeric', 'min:0'],
            'term_months' => ['required', 'integer', 'min:1'],
            'monthly_installment' => ['required', 'numeric', 'min:0'],
            'remaining_balance' => ['nullable', 'numeric', 'min:0'],
            'disbursed_at' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'active', 'completed', 'overdue', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['remaining_balance'] = $data['remaining_balance'] ?? $data['principal_amount'];
        $loan = $this->loans->create($data);

        return redirect()->route('loans.show', $loan)->with('status', 'Pinjaman berhasil ditambahkan.');
    }

    public function show(Loan $loan): View
    {
        $loan->load(['member', 'installments']);

        return view('loans.show', compact('loan'));
    }

    public function edit(Loan $loan): View
    {
        return view('loans.edit', [
            'loan' => $loan,
            'members' => Member::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Loan $loan): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'loan_number' => ['required', 'string', 'max:50', Rule::unique('loans', 'loan_number')->ignore($loan)],
            'principal_amount' => ['required', 'numeric', 'min:1000'],
            'interest_rate' => ['required', 'numeric', 'min:0'],
            'term_months' => ['required', 'integer', 'min:1'],
            'monthly_installment' => ['required', 'numeric', 'min:0'],
            'remaining_balance' => ['nullable', 'numeric', 'min:0'],
            'disbursed_at' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'active', 'completed', 'overdue', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->loans->update($loan, $data);

        return redirect()->route('loans.show', $loan)->with('status', 'Pinjaman berhasil diperbarui.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->loans->delete($loan);

        return redirect()->route('loans.index')->with('status', 'Pinjaman berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loan_ids' => ['required', 'array', 'min:1'],
            'loan_ids.*' => ['integer', 'distinct', 'exists:loans,id'],
        ]);

        $deleted = $this->loans->deleteMany(collect($validated['loan_ids']));

        return redirect()
            ->route('loans.index')
            ->with('status', $deleted > 1
                ? "{$deleted} pinjaman berhasil dihapus."
                : 'Pinjaman berhasil dihapus.');
    }

    private function generateLoanNumber(): string
    {
        do {
            $loanNumber = 'LN-' . now()->format('YmdHi') . '-' . random_int(100, 999);
        } while (Loan::where('loan_number', $loanNumber)->exists());

        return $loanNumber;
    }
}

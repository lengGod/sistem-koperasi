<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class InstallmentController extends Controller
{
    public function index(): View
    {
        $installments = Installment::query()->with('loan.member')->latest('due_date')->latest()->paginate(10)->withQueryString();

        return view('installments.index', compact('installments'));
    }

    public function create(): View
    {
        return view('installments.create', [
            'installment' => new Installment(['status' => 'pending']),
            'loans' => Loan::query()->with('member')->orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'loan_id' => ['required', 'exists:loans,id'],
            'installment_number' => ['required', 'integer', 'min:1'],
            'due_date' => ['required', 'date'],
            'principal_amount' => ['required', 'numeric', 'min:0'],
            'interest_amount' => ['required', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'partial', 'paid', 'late'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $installment = Installment::create($data);

        return redirect()->route('installments.show', $installment)->with('status', 'Angsuran berhasil ditambahkan.');
    }

    public function show(Installment $installment): View
    {
        $installment->load('loan.member');

        return view('installments.show', compact('installment'));
    }

    public function edit(Installment $installment): View
    {
        return view('installments.edit', [
            'installment' => $installment,
            'loans' => Loan::query()->with('member')->orderByDesc('id')->get(),
        ]);
    }

    public function update(Request $request, Installment $installment): RedirectResponse
    {
        $data = $request->validate([
            'loan_id' => ['required', 'exists:loans,id'],
            'installment_number' => ['required', 'integer', 'min:1'],
            'due_date' => ['required', 'date'],
            'principal_amount' => ['required', 'numeric', 'min:0'],
            'interest_amount' => ['required', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'partial', 'paid', 'late'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $installment->update($data);

        return redirect()->route('installments.show', $installment)->with('status', 'Angsuran berhasil diperbarui.');
    }

    public function destroy(Installment $installment): RedirectResponse
    {
        $installment->delete();

        return redirect()->route('installments.index')->with('status', 'Angsuran berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'installment_ids' => ['required', 'array', 'min:1'],
            'installment_ids.*' => ['integer', 'distinct', 'exists:installments,id'],
        ]);

        $deleted = Installment::whereIn('id', $validated['installment_ids'])->delete();

        return redirect()
            ->route('installments.index')
            ->with('status', $deleted > 1
                ? "{$deleted} angsuran berhasil dihapus."
                : 'Angsuran berhasil dihapus.');
    }
}

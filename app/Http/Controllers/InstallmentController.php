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
    public function __construct(private readonly \App\Services\InstallmentService $installments) {}

    public function index(Request $request): View
    {
        $query = Installment::query()->with('loan.member');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search): void {
                $query->whereHas('loan', function ($query) use ($search): void {
                    $query->where('loan_number', 'like', "%{$search}%")
                        ->orWhereHas('member', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('member_number', 'like', "%{$search}%")
                                ->orWhere('account_number', 'like', "%{$search}%");
                        });
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('installments.status', $request->input('status'));
        }

        if ($request->input('sort') === 'member_name') {
            $query->join('loans', 'installments.loan_id', '=', 'loans.id')
                ->join('members', 'loans.member_id', '=', 'members.id')
                ->orderBy('members.name')
                ->orderBy('installments.due_date')
                ->select('installments.*');
        } else {
            $query->join('loans', 'installments.loan_id', '=', 'loans.id')
                ->join('members', 'loans.member_id', '=', 'members.id')
                ->orderBy('members.name')
                ->orderBy('installments.due_date')
                ->select('installments.*');
        }

        $installments = $query->paginate(10)->withQueryString();

        return view('installments.index', compact('installments'));
    }

    public function create(Request $request): View
    {
        return view('installments.create', [
            'installment' => new Installment([
                'status' => 'pending',
                'loan_id' => $request->query('loan_id'),
            ]),
            'loans' => Loan::query()->with('member')->orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'loan_id' => ['required', 'exists:loans,id'],
            'due_date' => ['required', 'date'],
            'principal_amount' => ['required', 'numeric', 'min:0'],
            'interest_amount' => ['required', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'partial', 'paid', 'late'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $nextNumber = Installment::where('loan_id', $data['loan_id'])->count() + 1;
        $data['installment_number'] = $nextNumber;

        $installment = $this->installments->create($data);

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
            'due_date' => ['required', 'date'],
            'principal_amount' => ['required', 'numeric', 'min:0'],
            'interest_amount' => ['required', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'partial', 'paid', 'late'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->installments->update($installment, $data);

        return redirect()->route('installments.show', $installment)->with('status', 'Angsuran berhasil diperbarui.');
    }

    public function destroy(Installment $installment): RedirectResponse
    {
        $this->installments->delete($installment);

        return redirect()->route('installments.index')->with('status', 'Angsuran berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'installment_ids' => ['required', 'array', 'min:1'],
            'installment_ids.*' => ['integer', 'distinct', 'exists:installments,id'],
        ]);

        $deleted = $this->installments->deleteMany(collect($validated['installment_ids']));

        return redirect()
            ->route('installments.index')
            ->with('status', $deleted > 1
                ? "{$deleted} angsuran berhasil dihapus."
                : 'Angsuran berhasil dihapus.');
    }
}

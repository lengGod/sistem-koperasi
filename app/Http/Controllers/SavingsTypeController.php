<?php

namespace App\Http\Controllers;

use App\Models\SavingsType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class SavingsTypeController extends Controller
{
    public function index(Request $request): View
    {
        $savingsTypes = SavingsType::query()->orderBy('name')->paginate(10)->withQueryString();

        return view('savings-types.index', compact('savingsTypes'));
    }

    public function create(): View
    {
        return view('savings-types.create', ['savingsType' => new SavingsType(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:savings_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_mandatory' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_mandatory'] = $request->boolean('is_mandatory');
        $data['is_active'] = $request->boolean('is_active', true);

        $savingsType = SavingsType::create($data);

        return redirect()->route('savings-types.show', $savingsType)->with('status', 'Jenis simpanan berhasil ditambahkan.');
    }

    public function show(SavingsType $savingsType): View
    {
        return view('savings-types.show', compact('savingsType'));
    }

    public function edit(SavingsType $savingsType): View
    {
        return view('savings-types.edit', compact('savingsType'));
    }

    public function update(Request $request, SavingsType $savingsType): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('savings_types', 'code')->ignore($savingsType)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_mandatory' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_mandatory'] = $request->boolean('is_mandatory');
        $data['is_active'] = $request->boolean('is_active', true);

        $savingsType->update($data);

        return redirect()->route('savings-types.show', $savingsType)->with('status', 'Jenis simpanan berhasil diperbarui.');
    }

    public function destroy(SavingsType $savingsType): RedirectResponse
    {
        $savingsType->delete();

        return redirect()->route('savings-types.index')->with('status', 'Jenis simpanan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'savings_type_ids' => ['required', 'array', 'min:1'],
            'savings_type_ids.*' => ['integer', 'distinct', 'exists:savings_types,id'],
        ]);

        $deleted = SavingsType::whereIn('id', $validated['savings_type_ids'])->delete();

        return redirect()
            ->route('savings-types.index')
            ->with('status', $deleted > 1
                ? "{$deleted} jenis simpanan berhasil dihapus."
                : 'Jenis simpanan berhasil dihapus.');
    }
}

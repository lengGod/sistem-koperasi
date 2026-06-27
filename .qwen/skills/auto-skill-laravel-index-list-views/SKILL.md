---
name: laravel-index-list-views
description: Build Laravel 11 index (list) views in this koperasi project that (1) display a user-facing field (e.g. `account_number` 15-digit BRI) rather than the internal synthetic key (`KOP-XXXX`), (2) default-sort rows ascending by the related entity's `name`, (3) expose click-to-sort headers that preserve all other query-string filters (search, status, page), and (4) cover the cross-cutting view edits (forms, detail page, repositories, controllers) needed for any new display field.
source: auto-skill
extracted_at: '2026-06-27T18:09:35.172Z'
---

# Building Display-Field List Views in the Koperasi App

When the user asks to change what a column "means" in any index view (Anggota / Pinjaman / Simpanan / Angsuran / Jenis Simpanan) — or to make the table sort by name — apply the full pattern below. The non-obvious parts are: (a) separating the **internal synthetic key** from the **user-facing display field**, and (b) making the sort link preserve every other filter on the URL.

This skill applies across the existing modules: `members`, `loans`, `savings`, `installments`, `savings-types`.

## 1. Distinguish internal key from display field

This project gives each `Member` two identifiers:

| Field | Role | Source | Editable by user? |
|---|---|---|---|
| `member_number` (e.g. `KOP-0001`) | **Internal synthetic key** — auto-generated in `KoperasiMemberSeeder`, never changed | seeder loop counter | No |
| `account_number` (15-digit BRI) | **User-facing display value** — printed on the table, on receipts, on bank transfers | Excel master data or manual edit | Yes (nullable, unique) |

When the user says "no rekening" they mean the **15-digit `account_number`**, not `KOP-XXXX`. But many existing views, form fields, search boxes, and even other modules (loans, savings) currently render `{{ $member->member_number }}` and call it "No Rekening". Updating one view without the others creates inconsistency.

**Always inventory every reference before changing what a column displays.** Search both `resources/views/` and `app/` for the field name:

```bash
grep -rn "member_number" resources/views/
grep -rn "member_number" app/
grep -rn "account_number" resources/views/
grep -rn "account_number" app/
```

Common places the field appears:

| View | What it shows | Fix |
|---|---|---|
| `resources/views/members/index.blade.php` table cell | "No Rekening" column | Switch to `{{ $member->account_number ?: '-' }}` |
| `resources/views/members/show.blade.php` header badge | badge next to name | Switch to `{{ $member->account_number ?: 'Belum ada no rekening' }}` |
| `resources/views/members/_form.blade.php` input | "No Rekening" form input | Switch `name` and `id` to `account_number`, drop the `required` attribute |
| `resources/views/loans/_form.blade.php` Alpine binding | `x-data="{ accountNumber: @js($selectedMember?->member_number ?? '') }"` and `data-account="{{ $member->member_number }}"` | Update both the variable AND the `data-account` attribute |
| `resources/views/loans/index.blade.php`, `loans/show.blade.php` | any `{{ $loan->member?->member_number ?? '-' }}` labeled "No Rekening" | Switch to `account_number` |
| `resources/views/savings/_form.blade.php` | dropdown option label `{{ $member->member_number }} - {{ $member->name }}` | Keep `member_number` here — it is the join key for the picker |

**Trap:** the form's old `<input name="member_number">` had `required`. When you switch to `account_number`, mark it `nullable` (most members have no rekening) and add `Rule::unique('members', 'account_number')->whereNotNull('account_number')` to both `StoreMemberRequest` and `UpdateMemberRequest`. The `whereNotNull('account_number')` clause is essential — without it Laravel treats `NULL` as a duplicate of `NULL` and rejects the form for every member without an account number.

**Trap:** search must cover both fields. Update `EloquentMemberRepository::paginate()`:

```php
$query->where('name', 'like', "%{$search}%")
    ->orWhere('member_number', 'like', "%{$search}%")
    ->orWhere('account_number', 'like', "%{$search}%")   // add this
    ->orWhere('work_unit', 'like', "%{$search}%")
    ->orWhere('phone', 'like', "%{$search}%")
    ->orWhere('employment_status', 'like', "%{$search}%");
```

## 2. Default-sort by name ascending

The current default order in every repository is `latest()` on a timestamp column (e.g. `latest('disbursed_at')->latest()` for loans). When the user asks for alphabetical ordering, change the default — not just add a toggle.

For modules that own the `name` column directly (`members`, `savings_types`):

```php
->when(($filters['sort'] ?? null) === 'name',
    fn ($query) => $query->orderBy('name')->orderBy('member_number'),
    fn ($query) => $query->orderBy('name')->orderBy('member_number'))
```

For modules with a foreign key to `members` (`loans`, `savings`, `installments`), the rows themselves have no `name` column — you must `JOIN` the related table and order on its `name`:

```php
->when(($filters['sort'] ?? null) === 'member_name',
    fn ($query) => $query
        ->join('members', 'loans.member_id', '=', 'members.id')
        ->orderBy('members.name')
        ->orderBy('loans.loan_number')
        ->select('loans.*'),
    fn ($query) => $query
        ->join('members', 'loans.member_id', '=', 'members.id')
        ->orderBy('members.name')
        ->orderBy('loans.loan_number')
        ->select('loans.*'))
```

**Traps:**
- `select('loans.*')` after a join is **mandatory**, otherwise the SELECT becomes `*` from both tables and Eloquent hydrates the wrong model class.
- For chained relations (`installments → loans → members`), two joins are needed: `installments JOIN loans ON loan_id JOIN members ON loans.member_id = members.id`.
- For `SavingsController::index()` the current signature is `paginate()` with no args. Switch to `paginate(request()->only(['sort']))` so the request param reaches the repository.
- `InstallmentController::index()` does not use a repository — it's inline in the controller. Apply the join there directly.

## 3. Make the header click-to-sort

Replace the plain `<th>Nama</th>` (or `<th>Anggota</th>`) with this block — it preserves every other query-string param (search, filters, page) so users do not lose their place:

```blade
@php
    $isNameSort = request('sort') === 'name';   // or 'member_name'
    $sortParams = request()->except('sort');
    if (! $isNameSort) {
        $sortParams['sort'] = 'name';
    }
@endphp
<th class="px-6 py-4">
    <a href="{{ route('<route>.index', $sortParams) }}" class="inline-flex items-center gap-1 transition hover:text-primary">
        Nama
        @if ($isNameSort)
            <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
        @endif
    </a>
</th>
```

For modules where the header is "Anggota" instead of "Nama" (e.g. `savings/index.blade.php`), change the label and the sort key to `member_name` — the pattern is identical.

## 4. Add the column to the body if it does not exist

When a module currently has no "Anggota" column (e.g. `installments/index.blade.php` showed only loan number), and the user wants to sort by member name, **add the column** as part of the same change — otherwise the sort is invisible:

```blade
<td class="px-6 py-4 font-bold text-on-surface">{{ $installment->loan?->member?->name ?? '-' }}</td>
```

Then bump the empty-row `colspan` by one.

## 5. Pass `sort` through the controller

For each module's `index()` method, ensure the controller forwards `sort` to the repository:

```php
// before
$members = $this->members->paginate($request->only(['search', 'status']));
// after
$members = $this->members->paginate($request->only(['search', 'status', 'sort']));
```

## 6. Verify

Run a throwaway script that mirrors the repository's `paginate()` query (do **not** call the controller — just emulate the SQL). The script confirms the sort works at the DB layer before any browser test:

```php
<?php
// scripts_check_sort.php — delete after verifying
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;

echo "=== 5 anggota teratas (sort=name ASC) ===" . PHP_EOL;
Member::orderBy('name')->orderBy('member_number')
    ->take(5)->get(['member_number', 'name'])
    ->each(fn ($m) => "  {$m->member_number} | {$m->name}");
```

Expected: the first names start with **A** (Abdul, Afif, ...). If the first row starts with anything else, the sort is wrong.

## 7. Diagnose empty tables after a sort change

If a module's index page now renders zero rows, the cause is usually a stale seeder that still uses old keys (e.g. `LoanSeeder` references `MBR-000001`, `MBR-000002`, `MBR-000003` from the original `MemberSeeder`, but those members were replaced by `KOP-0001`-style keys in `KoperasiMemberSeeder`). The seeder's `if (! $memberId) continue;` guard silently skips unknown keys, so the table ends up empty without any error.

To detect this, check the actual data first:

```php
\App\Models\Loan::count();   // 0  ← empty
\App\Models\Member::where('member_number', 'MBR-000001')->count();   // 0  ← stale
```

**Fix:** delete or rewrite the stale seeder to use the new keys, OR write a fresh `KoperasiLoanSeeder` keyed on `KOP-XXXX` members. Do not assume an empty table is a sort bug — verify the underlying rows first.

## 8. Clean up

Delete the throwaway `scripts_check_*.php` file once verification passes. They are not part of the application and will pollute the repo.

## Common traps

- **Forgetting `whereNotNull('account_number')` on the unique rule** → form rejects every member who has no rekening because `NULL = NULL` under MySQL semantics without the clause. Always scope the unique rule to non-null values.
- **Reusing `$query->where(...)->orWhere(...)` on the search branch** → the `orWhere` opens a top-level OR across the whole query, including other filters. Wrap the ORs in a sub-closure: `$query->where(function ($q) use ($search) { $q->where(...)->orWhere(...); })`.
- **JOIN without `select('table.*')`** → Eloquent returns arrays instead of model instances and `with('member')` then fails silently. Always re-select the original table's columns after joining.
- **Forgetting to forward `sort` through the controller** → the URL has `?sort=name` but the repository receives no `sort` key and falls back to its default. The view shows an arrow icon but the order is unchanged.
- **Putting the header `<a href>` outside the `<thead>` block** → the link still works, but screen readers and `<form>` semantics get confused. Keep sort links inside the `<th>` cells.
- **Using `request()->except('page')`** → losing pagination loses the user's scroll position. `request()->except('sort')` is the right call here — it preserves `page`, `search`, and every other filter.
---
name: laravel-bulk-data-import
description: Import a master-data spreadsheet (members, savings, etc.) into this Laravel 11 koperasi project when the source provides **cumulative balances per period** rather than per-transaction rows. Covers migration for missing columns, synthetic-pivot seeder, delta-from-cumulative transaction generation, and Excel-vs-DB reconciliation.
source: auto-skill
extracted_at: '2026-06-27T00:00:00.000Z'
---

# Bulk-Importing Cooperative Member Data From Excel

When a user pastes a master-data spreadsheet (members, savings balances, etc.) and expects it to land in this Laravel app, follow this end-to-end procedure. The non-trivial part is that the source usually gives **cumulative balance per period**, but the existing `savings` schema is **transactional** (`member_id`, `savings_type_id`, `amount`, `transaction_date`). You must convert between the two without losing audit trail.

## 1. Inspect the target schema before writing code

Read `app/Models/Member.php`, `app/Models/Savings.php`, `app/Models/SavingsType.php`, and the matching `database/migrations/*.php`. Note exactly which fields exist, what is `nullable`, and what the `Savings` table requires (`member_id`, `savings_type_id`, `transaction_type`, `amount`, `transaction_date` — all NOT NULL per the migrations in this project).

Also read `database/seeders/DatabaseSeeder.php` to see the existing call order. New seeders must hook into that same order without breaking it.

## 2. Identify schema gaps from the source data

Make a list of every source column that has no matching column on the target table. For the April/May/June 2026 cooperative import the gaps were:

| Source column | Target column | Action |
|---|---|---|
| `KEANGGOTAAN` (AKTIF/PASIF) | `members.status` (active/inactive) | Map directly — no migration. |
| `JENIS PEKERJA` (BKS/MUTUAL/PKSS/ORGANIK/BG) | `members.work_unit` and `members.employment_status` | Store in both fields. |
| `NO REKENING` | none | **Add `account_number` column** (string 32, nullable, unique) via migration. |
| `SIMPANAN POKOK` (fixed 250.000 per row) | none | Generate one transaction per member in `savings` table at `savings_type_id` = `POKOK`. |
| `SIMPANAN WAJIB SALDO [bulan]` (cumulative) | none | Generate multiple transactions per member in `savings` table at `savings_type_id` = `WAJIB`, one per month. |

**Pattern:** add missing columns first via a new migration named `YYYY_MM_DD_NNNNNN_add_<column>_to_<table>.php`. Use `Schema::hasColumn()` guard in both `up()` and `down()` so the migration is idempotent if re-run.

## 3. Build the member seeder with a stable pivot key

The source data has no natural unique key (names collide, no NIK supplied, account numbers are missing for ~60% of rows). Generate a synthetic key in the seeder's own loop:

```php
$counter = 1;
foreach ($members as [$name, $workUnit, $status, $accountNumber, $saldoApril, $saldoMei, $saldoJuni]) {
    $memberNumber = 'KOP-' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT);
    $counter++;

    Member::updateOrCreate(
        ['member_number' => $memberNumber],
        [
            'name' => $name,
            'work_unit' => $workUnit,
            // ... remaining fields ...
            'account_number' => $accountNumber, // may be null
        ]
    );
}
```

Keep the array in source order — the synthetic key is meaningful only as a join key for the SavingsSeeder that follows. Use `updateOrCreate` keyed on `member_number` so the seeder is idempotent.

**Traps to avoid:**

- **Do not** use the source row number as the key — if you later delete a row from the array, every subsequent member_number shifts by one and the SavingsSeeder's array becomes misaligned.
- **Do not** use `Str::slug($name)` as the key — names in Indonesian spreadsheets are often misspelled or have trailing whitespace, so slug collisions are common.
- **Do not** trust account_number as unique — many source rows leave it blank. The unique index on `account_number` will reject duplicates when present, but nulls must remain allowed.

## 4. Translate cumulative balances into transactions

The source gives cumulative Wajib saldo per month. The Savings table stores per-month **deposits**. To convert:

```php
// $saldo = ['KOP-0001' => [saldoApril, saldoMei, saldoJuni], ...]
foreach ($saldo as $memberNumber => [$saldoApril, $saldoMei, $saldoJuni]) {
    // Simpanan Pokok: one transaction at join date (2026-04-01).
    saveTransaction($memberNumber, 'POKOK', 250000, '2026-04-01', ...);

    // Simpanan Wajib: monthly deposit = delta between consecutive cumulative balances.
    $monthly = [
        ['2026-04-30', $saldoApril],                                      // first month: full balance
        ['2026-05-31', max(0, $saldoMei - $saldoApril)],                 // subsequent: delta
        ['2026-06-30', max(0, $saldoJuni - $saldoMei)],
    ];
    foreach ($monthly as [$date, $amount]) {
        if ($amount <= 0) continue; // skip months where no deposit happened
        saveTransaction($memberNumber, 'WAJIB', $amount, $date, ...);
    }
}
```

The `max(0, ...)` guard prevents negative deltas (which would indicate a withdrawal — not present in this source data, but harmless if added later).

**Reference-number convention used in this project:** `POKOK-KOP-0042-202604` for Pokok, `WAJIB-KOP-0042-202605` for monthly Wajib. The `(member_number, period)` pair is unique per `reference_number`, which lets `Savings::updateOrCreate(['reference_number' => ...])` make the seeder fully idempotent.

**New members with zero prior history:** set their April and May saldo to 0 in the `$saldo` array, and their June saldo to the actual final balance (e.g., `275.000` = Pokok 250.000 + Wajib Juni 25.000). The delta logic will then create: Pokok transaction + one Wajib transaction in June for the full 275.000. Do NOT pre-fill `saldoApril = 275000` for new members — that would create a phantom April Wajib transaction of 275.000 instead of a June one.

## 5. Reconcile Excel grand totals vs DB after seeding

Before declaring success, run a reconciliation script that compares four numbers between the source spreadsheet and the database:

```php
// scripts_verify_db.php — throwaway, delete after verifying
$pokokTotal = Savings::where('savings_type_id', $pokokTypeId)->sum('amount');
$wajibTotal = Savings::where('savings_type_id', $wajibTypeId)->sum('amount');

foreach (['2026-04-30', '2026-05-31', '2026-06-30'] as $date) {
    $sum = Savings::where('savings_type_id', $wajibTypeId)
        ->where('transaction_type', 'deposit')
        ->where('transaction_date', '<=', $date)
        ->sum('amount');
    echo "Saldo kumulatif Wajib per $date: " . number_format($sum) . PHP_EOL;
}
```

Compare against the Excel "TOTAL" row. If sums do not match, the most common cause is **duplicates in the source data** that get skipped because the seeder keys on `member_number` (which assigns a new synthetic key to each). For example, the source listed "Herlan Wido Ardian" twice in the June rows with identical saldo — the seeder must create two separate member records (KOP-0087 and KOP-0140) to match the Excel total of 190.238.000.

## 6. Clean up after verification

Delete the throwaway verification script (`scripts_check_*.php`, `scripts_verify_*.php`) once the totals match. They are not part of the application and pollute the repo.

## 7. Register the new seeder in `DatabaseSeeder`

Replace the call to the old `MemberSeeder` (which only has 3 sample members) with the new `KoperasiMemberSeeder` so that `php artisan migrate:fresh --seed` produces the full dataset. Keep the call order: `SavingsTypeSeeder` → `KoperasiMemberSeeder` → `SavingsSeeder`. The `LoanSeeder` and `InstallmentSeeder` still run after and should not be removed.

## Common traps

- **Skipping the migration step.** Trying to seed `account_number` into a column that doesn't exist throws `SQLSTATE[42S22]: Column not found`. Always check `Schema::hasColumn()` before adding.
- **Cumulative vs per-transaction confusion.** If you store the saldo as a single row per member with `amount = saldo_juni`, you lose audit trail — you can never tell how much was deposited in April vs May vs June. Always generate one row per month.
- **Negative deltas.** When saldo in a later month is lower than the previous month, the raw delta is negative. The source data for this import never showed withdrawals, so `max(0, ...)` is the right guard. If withdrawals are possible, add a `'transaction_type' => $delta < 0 ? 'withdrawal' : 'deposit'` switch instead.
- **Date format.** The migration column type is `date` (not `datetime`), so use `Carbon::create(2026, 4, 30)->toDateString()` or pass an ISO string `'2026-04-30'`. Passing a full `datetime` will fail validation in `StoreSavingsRequest` later.
- **Factory generators leaking into seed.** The old `MemberSeeder` ends with `Member::factory()->count(12)->create();` — that line adds 12 random fake members on top of the 3 sample ones. Make sure your replacement seeder does NOT include any `Member::factory()` calls, or the reconciliation totals will be off.
- **Re-running the seeder.** Because both `KoperasiMemberSeeder` and `SavingsSeeder` use `updateOrCreate` with stable keys, `php artisan db:seed --class=KoperasiMemberSeeder` is safe to re-run. But if you change the order of the source array, every `member_number` shifts and the SavingsSeeder's `$saldo` array becomes wrong. Always regenerate the `$saldo` keys together with the members array.

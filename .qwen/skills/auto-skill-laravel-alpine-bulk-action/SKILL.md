---
name: laravel-alpine-bulk-action
description: Diagnose and fix three recurring Alpine.js bugs in Laravel Blade index views used by this project — (1) confirmation modals placed outside the `x-data="appShell"` scope that never appear, (2) bulk-action checkboxes bound with `x-model="selected"` whose `:disabled` submit button stays stuck, and (3) the header `<th>` "select-all" checkbox whose click does not propagate to the per-row `<td>` checkboxes.
source: auto-skill
extracted_at: '2026-06-27T00:00:00.000Z'
---

# Fixing Alpine.js Bulk-Action Bugs in Blade Views

Two distinct Alpine.js problems surface repeatedly in the index views of this project (`members`, `savings-types`, `savings`, `loans`, `installments`). They can coexist or appear independently. Diagnose in this order.

## 1. Confirmation modal never appears when "Hapus Terpilih" is clicked

**Symptom:** User checks one or more checkboxes, clicks the submit button (which becomes enabled), but no modal pops up. Form submission does not happen either. No console errors.

**Why it happens:** The modal markup uses Alpine directives like `x-show="confirmOpen"`, `x-text="confirmTitle"`, `@click="submitConfirm()"` — but in `resources/views/layouts/app.blade.php` the `<div x-data="appShell">` wrapper closes *before* the modal element. Alpine directives only read state from within the same `x-data` scope they live in. Outside the scope, `confirmOpen` evaluates to `undefined` (falsy), so `x-show` keeps the modal hidden. The submit handler in `resources/js/app.js` dispatches `open-confirm-modal` correctly, `openConfirm()` mutates state correctly — but the bindings never observe it.

**Fix:** Move the confirmation modal **inside** the `<div x-data="appShell">` block in `layouts/app.blade.php`. The opening `<div x-data="appShell" @open-confirm-modal.window="openConfirm($event.detail)">` must remain the outermost ancestor of every Alpine directive that touches `confirmOpen`, `confirmTitle`, `confirmMessage`, `confirmButton`, `confirmTone`, or calls `closeConfirm()`/`submitConfirm()`.

**Verification step after moving:**
- `grep -c 'x-data="appShell"' resources/views/layouts/app.blade.php` → must be `1` (one declaration, not duplicated).
- `grep -n 'x-show="confirmOpen"' resources/views/layouts/app.blade.php` → every match must have a line number *greater than* the line where `x-data="appShell"` opens.
- There must be exactly one `<div x-cloak x-show="confirmOpen" class="fixed inset-0 z-50 ...">` block. Before the fix there were two — one in-scope (broken placement) and one out-of-scope. Delete the duplicate.

**Common traps:**
- Do **not** wrap the modal in a *new* `<div x-data>` block — Alpine's `appShell` component holds the modal state. A nested `x-data` would shadow the parent's `confirmOpen` and the dispatched event listener `@open-confirm-modal.window` would no longer match.
- Keep the modal's `class="fixed inset-0 z-50"` so it overlays any table, sidebar, or sticky header.
- The toast (`#app-toast`) is plain DOM (no Alpine directives), so it can stay outside the `x-data` block — only the modal must move in.

## 2. "Hapus Terpilih" button stays disabled or stuck

**Symptom:** After fixing #1, the modal opens — but the submit button shows `:disabled="selected.length === 0"` either permanently disabled (greyed out) or stuck disabled even after the user ticks checkboxes. Sometimes clicking the header "Pilih Semua" doesn't change the button state.

**Why it happens:** The original pattern uses Alpine `x-model="selected"` directly on per-row `<input type="checkbox" name="member_ids[]" value="{{ $id }}">`. Alpine's checkbox-array binding is sensitive to type coercion: `value="{{ $id }}"` always renders as a **string** in HTML, but `$members->pluck('id')` returns **integers** (or whatever the model's primary key type is). The mismatch causes `selected.includes(id)` comparisons and `.length` checks to misbehave, especially after toggling or after "Pilih Semua" replaces the array with `memberIds.slice()`.

**Fix — replace `x-model` with explicit `:checked` + `@change` + helper methods.** Apply this to all five modules: `members/index.blade.php`, `savings-types/index.blade.php`, `savings/index.blade.php`, `loans/index.blade.php`, `installments/index.blade.php`.

Per-module change has three parts.

**(a) Replace the `x-data` block on the bulk form:**

```blade
x-data="{
    selected: [],
    itemIds: @js($itemIds),          // rename per module: memberIds, savingsTypeIds, savingsIds, loanIds, installmentIds
    allSelected() { return this.itemIds.length > 0 && this.selected.length === this.itemIds.length; },
    toggleAll(checked) { this.selected = checked ? this.itemIds.slice() : []; },
    toggleOne(id, checked) {
        const stringId = String(id);
        if (checked) {
            if (!this.selected.includes(stringId)) this.selected.push(stringId);
        } else {
            this.selected = this.selected.filter((value) => value !== stringId);
        }
    },
}"
```

`(b) Update the "Pilih Semua" button and the header checkbox:`

```blade
<button type="button" ... @click="toggleAll(!allSelected())">Pilih Semua</button>

<!-- Header checkbox in <thead> -->
<input type="checkbox" ... :checked="allSelected()" @change="toggleAll($event.target.checked)">
```

`(c) Update each per-row checkbox:`

```blade
<input
    type="checkbox"
    name="member_ids[]"           <!-- match the existing field name per module -->
    value="{{ $member->id }}"
    class="rounded border-outline-variant text-primary focus:ring-primary"
    :checked="selected.includes(String({{ $member->id }}))"
    @change="toggleOne({{ $member->id }}, $event.target.checked)">
```

The `String(...)` wrapping on both sides is the load-bearing piece — it normalizes the comparison so checkbox `value` attributes (rendered as HTML strings) match entries in the `selected` array regardless of whether the source `$ids` are integers, bigints, or UUIDs.

## 3. Header `<th>` checkbox click does not tick per-row `<td>` checkboxes

**Symptom:** User clicks the checkbox in the `<th>` header row (the "select all" toggle). The header checkbox itself visibly toggles, but **none of the per-row `<td>` checkboxes become checked** — they stay unselected. The Hapus Terpilih button stays disabled too, because the `selected` array never grew. No console errors.

**Why it happens:** The previous skill version recommended `:checked="allSelected()" @change="toggleAll($event.target.checked)"` for the header checkbox. The logic *seems* right (when `change` fires, read the new `checked` value and call `toggleAll`), but in practice the per-row `:checked="selected.includes(String({{ $id }}))"` binding can fail to re-render synchronously with the `$event.target.checked` read. The reactive cycle gets out of order: the DOM event mutates the checkbox state before Alpine finishes propagating the array state to all per-row bindings, and `selected` ends up partially updated or stale.

**Fix — replace the header checkbox's event binding from `@change="toggleAll($event.target.checked)"` to `@click="toggleAll(!allSelected())"`.** The `@click` handler reads state from Alpine (`!allSelected()`) rather than from the DOM event object, so the `state → DOM update` cycle stays one-way and consistent. This is the same pattern already used by the "Pilih Semua" button, which has always worked.

```blade
<!-- Header checkbox in <thead> — FIXED -->
<input type="checkbox"
    class="rounded border-outline-variant text-primary focus:ring-primary"
    @click="toggleAll(!allSelected())"
    :checked="allSelected()">
```

The "Pilih Semua" button already uses `@click="toggleAll(!allSelected())"` — after this fix, the header checkbox matches that pattern exactly, which is why both now behave identically.

**Why this is load-bearing:** Reading state via `allSelected()` (which inspects the reactive `selected` array) keeps the source of truth in Alpine, not in the DOM event. Reading `$event.target.checked` reads from the DOM, which has its own tick cycle that can drift from Alpine's reactivity. Always read state from Alpine when toggling Alpine-managed state.

**Verification after fix:**
- `grep -rn 'toggleAll(\$event' resources/views/` → must return **zero** matches (the old pattern is gone everywhere).
- `grep -rn '@click="toggleAll(!allSelected())"' resources/views/` → must return **10** matches: one "Pilih Semua" button and one header checkbox per index view × 5 modules (members, savings, loans, installments, savings-types).

**What stays the same:**
- The submit button keeps `:disabled="selected.length === 0"` — now this reflects reality because the helpers push/pop correctly.
- The `name="member_ids[]"` (or `savings_ids[]`, `savings_type_ids[]`, `loan_ids[]`, `installment_ids[]`) attributes on the checkboxes are what gets posted to the server. Removing `x-model` does not affect form serialization.

## Workflow

When a user reports "Hapus Terpilih" doesn't work in any index view:

1. Open `resources/views/layouts/app.blade.php` and confirm the confirmation modal lives inside the `<div x-data="appShell">` scope. If not, apply fix #1.
2. Open the affected module's `resources/views/<module>/index.blade.php` and look for `x-model="selected"` on per-row checkboxes. If present, apply fix #2.
3. Apply fix #2 to **all five** index views proactively — they share the same broken template, even if only one is reported.
4. Run `php -l` on each changed Blade file (syntax check) and `php artisan view:clear`.
5. Run `npm run build` so Vite emits the updated bundle hash; the layout loads `@vite(['resources/css/app.scss', 'resources/js/app.js'])`.
6. Refresh the page in the browser, tick a checkbox, click Hapus Terpilih — the modal must appear with title/message/buttons populated from `data-confirm-*` attributes.

## What NOT to change

- Do **not** rename `data-confirm-*` attributes on the bulk forms. The submit handler in `resources/js/app.js` reads them by name (`form.dataset.confirmTitle`, etc.).
- Do **not** switch the form to a regular HTML form without Alpine — the `disabled` state then has to be done with plain JS and loses the per-row reactivity.
- Do **not** add a nested `<div x-data>` to host the modal — see trap in section 1.
- Do **not** drop the `x-cloak` attribute on the modal; without it, the modal briefly flashes visible before Alpine evaluates `x-show` on first paint.
<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\SavingsTypeController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\ProfileController;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Savings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $totalMembers = Member::count();
    $totalSavings = Savings::sum('amount');
    $totalSavingsThisMonth = Savings::whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');
    $activeLoans = Loan::where('status', 'active')->count();
    $activeLoanBalance = Loan::where('status', 'active')->sum('remaining_balance');
    $dueInstallments = Installment::whereIn('status', ['pending', 'partial', 'late'])->count();
    $paidInstallments = Installment::where('status', 'paid')->count();
    $overdueInstallments = Installment::where('status', 'late')->count();

    $months = collect(range(5, 0))->map(function (int $offset) {
        $date = now()->subMonthsNoOverflow($offset)->startOfMonth();

        return [
            'key' => $date->format('Y-m'),
            'label' => $date->locale(app()->getLocale())->translatedFormat('M'),
            'month_start' => $date,
            'month_end' => $date->copy()->endOfMonth(),
        ];
    });

    $savingsByMonth = Savings::query()
        ->selectRaw("DATE_FORMAT(transaction_date, '%Y-%m') as month_key, SUM(amount) as total")
        ->groupBy('month_key')
        ->pluck('total', 'month_key');

    $installmentsByMonth = Installment::query()
        ->selectRaw("DATE_FORMAT(due_date, '%Y-%m') as month_key, SUM(amount) as total")
        ->groupBy('month_key')
        ->pluck('total', 'month_key');

    $chartBars = $months->map(function (array $month) use ($savingsByMonth, $installmentsByMonth) {
        $savings = (float) ($savingsByMonth[$month['key']] ?? 0);
        $installments = (float) ($installmentsByMonth[$month['key']] ?? 0);

        return [
            'month' => $month['label'],
            'savings' => $savings,
            'installments' => $installments,
            'total' => $savings + $installments,
        ];
    });

    $maxTotal = max(1, $chartBars->max('total'));

    $recentSavings = Savings::query()
        ->with(['member', 'savingsType'])
        ->latest('transaction_date')
        ->limit(5)
        ->get();

    $recentLoans = Loan::query()
        ->with('member')
        ->latest('created_at')
        ->limit(5)
        ->get();

    $upcomingInstallments = Installment::query()
        ->with('loan.member')
        ->whereIn('status', ['pending', 'partial', 'late'])
        ->orderBy('due_date')
        ->limit(5)
        ->get();

    return view('dashboard', compact(
        'totalMembers',
        'totalSavings',
        'totalSavingsThisMonth',
        'activeLoans',
        'activeLoanBalance',
        'dueInstallments',
        'paidInstallments',
        'overdueInstallments',
        'chartBars',
        'maxTotal',
        'recentSavings',
        'recentLoans',
        'upcomingInstallments'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('members/bulk-destroy', [MemberController::class, 'bulkDestroy'])->name('members.bulk-destroy');
    Route::resource('members', MemberController::class);

    Route::post('savings-types/bulk-destroy', [SavingsTypeController::class, 'bulkDestroy'])->name('savings-types.bulk-destroy');
    Route::resource('savings-types', SavingsTypeController::class);

    Route::post('savings/bulk-destroy', [SavingsController::class, 'bulkDestroy'])->name('savings.bulk-destroy');
    Route::resource('savings', SavingsController::class);

    Route::post('loans/bulk-destroy', [LoanController::class, 'bulkDestroy'])->name('loans.bulk-destroy');
    Route::resource('loans', LoanController::class);

    Route::post('installments/bulk-destroy', [InstallmentController::class, 'bulkDestroy'])->name('installments.bulk-destroy');
    Route::resource('installments', InstallmentController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

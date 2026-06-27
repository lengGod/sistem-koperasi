<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\SavingsTypeController;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Savings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/favicon.ico', function () {
    $path = public_path('favicon.ico');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'image/x-icon',
        'Cache-Control' => 'public, max-age=86400',
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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

require __DIR__ . '/auth.php';

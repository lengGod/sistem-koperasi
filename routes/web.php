<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfitReportController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\SavingsTypeController;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\ParkingTransactionController;
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

    Route::resource('users', UserController::class);

    Route::post('savings-types/bulk-destroy', [SavingsTypeController::class, 'bulkDestroy'])->name('savings-types.bulk-destroy');
    Route::resource('savings-types', SavingsTypeController::class);

    Route::post('savings/bulk-destroy', [SavingsController::class, 'bulkDestroy'])->name('savings.bulk-destroy');
    Route::resource('savings', SavingsController::class);

    Route::post('loans/bulk-destroy', [LoanController::class, 'bulkDestroy'])->name('loans.bulk-destroy');
    Route::resource('loans', LoanController::class);

    Route::post('installments/bulk-destroy', [InstallmentController::class, 'bulkDestroy'])->name('installments.bulk-destroy');
    Route::resource('installments', InstallmentController::class);

    Route::post('products/bulk-destroy', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
    Route::resource('products', ProductController::class);
    Route::post('product-categories/bulk-destroy', [ProductCategoryController::class, 'bulkDestroy'])->name('product-categories.bulk-destroy');
    Route::resource('product-categories', ProductCategoryController::class);
    
    // Parking Module
    Route::resource('parking-types', VehicleTypeController::class);
    Route::post('parking-transactions/bulk-destroy', [ParkingTransactionController::class, 'bulkDestroy'])->name('parking-transactions.bulk-destroy');
    Route::resource('parking-transactions', ParkingTransactionController::class);

    Route::resource('transactions', TransactionController::class);
    Route::post('transactions/{transaction}/reverse', [TransactionController::class, 'reverse'])->name('transactions.reverse');
    Route::post('transactions/bulk-reverse', [TransactionController::class, 'bulkReverse'])->name('transactions.bulk-reverse');
    Route::get('stock-histories', [StockHistoryController::class, 'index'])->name('stock-histories.index');
    Route::get('reports/profit', [ProfitReportController::class, 'index'])->name('reports.profit');
    Route::get('reports/profit/export', [ProfitReportController::class, 'exportProfit'])->name('reports.profit.export');
    Route::get('reports/koperasi', [ProfitReportController::class, 'koperasi'])->name('reports.koperasi.index');
    Route::get('reports/koperasi/export', [ProfitReportController::class, 'export'])->name('reports.koperasi.export');
    Route::get('stock-histories', [StockHistoryController::class, 'index'])->name('stock-histories.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

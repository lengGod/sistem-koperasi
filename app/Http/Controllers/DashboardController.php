<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Savings;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers = Member::count();
        $totalSavings = Savings::sum('amount');
        $latestSavingsDate = Savings::max('transaction_date');
        $reportDate = $latestSavingsDate ? Carbon::parse($latestSavingsDate) : now();
        $totalSavingsThisMonth = Savings::whereBetween('transaction_date', [
            $reportDate->copy()->startOfMonth(),
            $reportDate->copy()->endOfMonth(),
        ])->sum('amount');
        $activeLoans = Loan::where('status', 'active')->count();
        $activeLoanBalance = Loan::where('status', 'active')->sum('remaining_balance');
        $dueInstallments = Installment::whereIn('status', ['pending', 'partial', 'late'])->count();
        $paidInstallments = Installment::where('status', 'paid')->count();
        $overdueInstallments = Installment::where('status', 'late')->count();

        $firstSavingsDate = Savings::min('transaction_date');
        $startDate = $firstSavingsDate
            ? Carbon::parse($firstSavingsDate)->startOfMonth()
            : $reportDate->copy()->subMonthsNoOverflow(5)->startOfMonth();
        $endDate = $reportDate->copy()->startOfMonth();

        if ($startDate->diffInMonths($endDate) > 5) {
            $startDate = $endDate->copy()->subMonthsNoOverflow(5);
        }

        $months = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addMonthNoOverflow()) {
            $month = $date->copy();

            $months->push([
                'key' => $month->format('Y-m'),
                'label' => $month->locale(app()->getLocale())->translatedFormat('M Y'),
                'month_end' => $month->endOfMonth(),
            ]);
        }

        $savingsTransactions = Savings::query()
            ->whereDate('transaction_date', '<=', $endDate->copy()->endOfMonth())
            ->orderBy('transaction_date')
            ->get(['transaction_date', 'transaction_type', 'amount']);

        $installments = Installment::query()
            ->whereDate('due_date', '<=', $endDate->copy()->endOfMonth())
            ->orderBy('due_date')
            ->get(['due_date', 'amount']);

        $runningSavings = 0;
        $chartBars = $months->map(function (array $month) use ($savingsTransactions, $installments, &$runningSavings) {
            $monthlySavings = $savingsTransactions
                ->filter(fn(Savings $saving) => $saving->transaction_date->format('Y-m') === $month['key'])
                ->sum(fn(Savings $saving) => $saving->transaction_type === 'withdrawal' ? -1 * (float) $saving->amount : (float) $saving->amount);

            $runningSavings += $monthlySavings;

            $installmentsTotal = $installments
                ->filter(fn(Installment $installment) => $installment->due_date->format('Y-m') === $month['key'])
                ->sum(fn(Installment $installment) => (float) $installment->amount);

            return [
                'month' => $month['label'],
                'savings' => max(0, $runningSavings),
                'savings_in' => $monthlySavings,
                'installments' => $installmentsTotal,
                'total' => max(0, $runningSavings) + $installmentsTotal,
            ];
        });

        $maxTotal = max(1, $chartBars->max('total'));

        // dd($chartBars);

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
            'reportDate',
            'chartBars',
            'maxTotal',
            'recentSavings',
            'recentLoans',
            'upcomingInstallments'
        ));
    }
}

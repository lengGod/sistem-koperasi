<?php

namespace App\Http\Controllers;

use App\Exports\KoperasiExport;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Savings;
use App\Models\Installment;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ProfitReportController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_stock');

        $month = $request->input('month'); // Format YYYY-MM
        
        $query = Product::query();

        if ($month) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= ?', [$month]);
        }

        // Total Terjual (items) berdasarkan periode
        $query->withSum(['items as total_sold' => function ($query) use ($month) {
            if ($month) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month]);
            }
        }], 'quantity');

        // Total Pembelian (stockHistories) berdasarkan periode
        $query->withSum(['stockHistories as total_purchased' => function ($query) use ($month) {
            $query->where('type', 'masuk');
            if ($month) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month]);
            }
        }], 'quantity_change');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return view('reports.profit', compact('products', 'month'));
    }

    public function koperasi(Request $request)
    {
        $month = $request->input('month');

        $query = [
            'savings' => Savings::query(),
            'loans' => Loan::query(),
            'installments' => Installment::query(),
        ];

        if ($month) {
            foreach ($query as $key => $q) {
                // Assuming date columns are 'transaction_date', 'created_at', 'due_date'
                $column = match($key) {
                    'savings' => 'transaction_date',
                    'loans' => 'created_at',
                    'installments' => 'due_date',
                };
                $q->whereRaw("DATE_FORMAT($column, '%Y-%m') = ?", [$month]);
            }
        }

        $data = [
            'totalMembers' => Member::count(), // Members usually total count regardless of month filter in this context
            'totalSavings' => $query['savings']->sum('amount'),
            'totalLoans' => $query['loans']->sum('principal_amount'),
            'totalInstallmentsPaid' => $query['installments']->where('status', 'paid')->sum('amount'),
            'activeLoansBalance' => Loan::where('status', 'active')->sum('remaining_balance'),
            'overdueInstallments' => Installment::where('status', 'late')->count(),
            'month' => $month,
        ];

        return view('reports.koperasi', $data);
    }

    public function export(Request $request)
    {
        $month = $request->input('month');
        $fileName = 'laporan-koperasi-' . ($month ?? 'semua') . '.xlsx';
        
        return Excel::download(new KoperasiExport($month), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }
}

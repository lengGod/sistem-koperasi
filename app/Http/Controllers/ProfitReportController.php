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

        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        
        $query = Product::query();

        if ($startMonth || $endMonth) {
            if ($startMonth && $endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") BETWEEN ? AND ?', [$startMonth, $endMonth]);
            } elseif ($startMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= ?', [$startMonth]);
            } elseif ($endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= ?', [$endMonth]);
            }
        }

        // Total Terjual (items) berdasarkan periode
        $query->withSum(['items as total_sold' => function ($query) use ($startMonth, $endMonth) {
            if ($startMonth && $endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") BETWEEN ? AND ?', [$startMonth, $endMonth]);
            } elseif ($startMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= ?', [$startMonth]);
            } elseif ($endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= ?', [$endMonth]);
            }
        }], 'quantity');

        // Total Pembelian (stockHistories) berdasarkan periode
        $query->withSum(['stockHistories as total_purchased' => function ($query) use ($startMonth, $endMonth) {
            $query->where('type', 'masuk');
            if ($startMonth && $endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") BETWEEN ? AND ?', [$startMonth, $endMonth]);
            } elseif ($startMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= ?', [$startMonth]);
            } elseif ($endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= ?', [$endMonth]);
            }
        }], 'quantity_change');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return view('reports.profit', compact('products', 'startMonth', 'endMonth'));
    }

    public function koperasi(Request $request)
    {
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');

        $membersQuery = Member::query();

        if ($startMonth || $endMonth) {
            $membersQuery->where(function ($q) use ($startMonth, $endMonth) {
                $q->whereHas('savings', function ($sq) use ($startMonth, $endMonth) {
                    $this->applyDateRange($sq, 'savings.transaction_date', $startMonth, $endMonth);
                })
                ->orWhereHas('loans', function ($lq) use ($startMonth, $endMonth) {
                    $this->applyDateRange($lq, 'loans.created_at', $startMonth, $endMonth);
                })
                ->orWhereHas('installments', function ($iq) use ($startMonth, $endMonth) {
                    $this->applyDateRange($iq, 'installments.due_date', $startMonth, $endMonth);
                });
            });
        }

        $members = $membersQuery->get();
        $memberIds = $members->pluck('id');

        $query = [
            'savings' => Savings::query()->whereIn('member_id', $memberIds),
            'loans' => Loan::query()->whereIn('member_id', $memberIds),
            'installments' => Installment::query()->whereHas('loan', function($q) use ($memberIds) {
                $q->whereIn('member_id', $memberIds);
            }),
        ];

        if ($startMonth || $endMonth) {
            foreach ($query as $key => $q) {
                $column = match($key) {
                    'savings' => 'savings.transaction_date',
                    'loans' => 'loans.created_at',
                    'installments' => 'installments.due_date',
                };
                $this->applyDateRange($q, $column, $startMonth, $endMonth);
            }
        }

        $data = [
            'totalMembers' => $members->count(),
            'totalSavings' => $query['savings']->sum('amount'),
            'totalLoans' => $query['loans']->sum('principal_amount'),
            'totalInstallmentsPaid' => $query['installments']->where('status', 'paid')->sum('amount'),
            'activeLoansBalance' => Loan::whereIn('member_id', $memberIds)->where('status', 'active')->sum('remaining_balance'),
            'overdueInstallments' => Installment::whereHas('loan', function($q) use ($memberIds) {
                $q->whereIn('member_id', $memberIds);
            })->where('status', 'late')->count(),
            'start_month' => $startMonth,
            'end_month' => $endMonth,
        ];

        return view('reports.koperasi', $data);
    }

    private function applyDateRange($query, $column, $start, $end)
    {
        if ($start && $end) {
            $query->whereRaw("DATE_FORMAT($column, '%Y-%m') BETWEEN ? AND ?", [$start, $end]);
        } elseif ($start) {
            $query->whereRaw("DATE_FORMAT($column, '%Y-%m') >= ?", [$start]);
        } elseif ($end) {
            $query->whereRaw("DATE_FORMAT($column, '%Y-%m') <= ?", [$end]);
        }
    }

    public function export(Request $request)
    {
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        $fileName = 'laporan-koperasi-' . ($startMonth ?? 'awal') . '-ke-' . ($endMonth ?? 'akhir') . '.xlsx';
        
        return Excel::download(new KoperasiExport($startMonth, $endMonth), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }
}

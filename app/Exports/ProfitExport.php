<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProfitExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    use Exportable;

    protected $startMonth;
    protected $endMonth;
    protected $search;

    public function __construct($startMonth, $endMonth, $search)
    {
        $this->startMonth = $startMonth;
        $this->endMonth = $endMonth;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Product::query();

        if ($this->startMonth || $this->endMonth) {
            $start = $this->startMonth;
            $end = $this->endMonth;
            // ... (logika query yang sama dengan di ProfitReportController@index)
            if ($start && $end) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") BETWEEN ? AND ?', [$start, $end]);
            } elseif ($start) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= ?', [$start]);
            } elseif ($end) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= ?', [$end]);
            }
        }

        $query->withSum(['items as total_sold' => function ($query) {
            // ... (logika query yang sama dengan di ProfitReportController@index)
            if ($this->startMonth && $this->endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") BETWEEN ? AND ?', [$this->startMonth, $this->endMonth]);
            } elseif ($this->startMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= ?', [$this->startMonth]);
            } elseif ($this->endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= ?', [$this->endMonth]);
            }
        }], 'quantity');

        $query->withSum(['stockHistories as total_purchased' => function ($query) {
            $query->where('type', 'masuk');
            if ($this->startMonth && $this->endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") BETWEEN ? AND ?', [$this->startMonth, $this->endMonth]);
            } elseif ($this->startMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= ?', [$this->startMonth]);
            } elseif ($this->endMonth) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= ?', [$this->endMonth]);
            }
        }], 'quantity_change');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return $query->get();
    }

    public function title(): string
    {
        return 'Laporan Keuntungan';
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Terjual',
            'Stok Saat Ini',
            'Modal (Satuan)',
            'Jual (Satuan)',
            'Total Jual',
            'Untung (Satuan)',
            'Total Untung',
        ];
    }

    public function map($product): array
    {
        $sold = $product->total_sold ?? 0;
        $profitPerUnit = $product->price - $product->purchase_price;
        $totalJualItem = $product->price * $sold;
        $totalProfit = $profitPerUnit * $sold;

        return [
            $product->name,
            $sold,
            $product->stock,
            $product->purchase_price,
            $product->price,
            $totalJualItem,
            $profitPerUnit,
            $totalProfit,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                
                // Tambahkan baris baru untuk judul
                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:H1');
                
                $period = ($this->startMonth ?? 'Awal') . ' s/d ' . ($this->endMonth ?? 'Akhir');
                $sheet->setCellValue('A1', 'LAPORAN KEUNTUNGAN PENJUALAN ' . strtoupper($period));
                
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0056b3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Styling header tabel (sekarang baris 2)
                $sheet->getStyle('A2:H2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0056b3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class KoperasiExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    use Exportable;

    protected $startMonth;
    protected $endMonth;
    protected $months;
    private $rowNumber = 0;

    public function __construct($startMonth, $endMonth)
    {
        $this->startMonth = $startMonth;
        $this->endMonth = $endMonth;

        $query = \App\Models\Savings::query()
            ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month')
            ->whereHas('savingsType', fn($q) => $q->where('code', 'WAJIB'));
            
        if ($startMonth) $query->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") >= ?', [$startMonth]);
        if ($endMonth) $query->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") <= ?', [$endMonth]);

        $this->months = $query->distinct()->orderBy('month')->pluck('month');
    }

    public function collection()
    {
        $membersQuery = Member::query()
            ->with(['savings.savingsType', 'loans', 'installments']);

        if ($this->startMonth || $this->endMonth) {
            $start = $this->startMonth;
            $end = $this->endMonth;
            $membersQuery->where(function ($q) use ($start, $end) {
                $q->whereHas('savings', function ($sq) use ($start, $end) {
                    $this->applyDateRange($sq, 'savings.transaction_date', $start, $end);
                })
                ->orWhereHas('loans', function ($lq) use ($start, $end) {
                    $this->applyDateRange($lq, 'loans.created_at', $start, $end);
                })
                ->orWhereHas('installments', function ($iq) use ($start, $end) {
                    $this->applyDateRange($iq, 'installments.due_date', $start, $end);
                });
            });
        }

        return $membersQuery->orderBy('name', 'asc')->get();
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

    public function title(): string
    {
        return 'Rekap Koperasi';
    }

    public function headings(): array
    {
        $monthHeadings = $this->months->map(
            fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->locale('id')->translatedFormat('F Y')
        )->toArray();

        return [
            [
                'No',
                'Nama Anggota',
                'No Rekening',
                'Status',
                'Jenis Pekerjaan',
                'Simpanan Pokok',
                'SIMPANAN WAJIB',
                ...array_fill(0, count($monthHeadings), ''),
                'Total Seluruh Saldo',
                'Pinjaman',
                'Angsuran',
            ],
            [
                '',
                '',
                '',
                '',
                '',
                '',
                ...$monthHeadings,
                'Total Simpanan Wajib',
                '',
                '',
                '',
            ],
        ];
    }

    public function map($member): array
    {
        $this->rowNumber++;
        $statusMap = [
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'pending' => 'Menunggu Verifikasi',
        ];

        // Apply range filter to map methods as well
        $savings = $member->savings;
        if ($this->startMonth) {
            $savings = $savings->filter(fn($s) => \Carbon\Carbon::parse($s->transaction_date)->format('Y-m') >= $this->startMonth);
        }
        if ($this->endMonth) {
            $savings = $savings->filter(fn($s) => \Carbon\Carbon::parse($s->transaction_date)->format('Y-m') <= $this->endMonth);
        }

        return [
            $this->rowNumber,
            $member->name,
            '="' . $member->account_number . '"',
            $statusMap[$member->status] ?? $member->status,
            $member->employment_status,
            $member->savings->filter(fn($s) => $s->savingsType && $s->savingsType->code === 'POKOK')->sum('amount'),
            ...$this->months->map(fn($m) => $member->savings()
                ->whereHas('savingsType', fn($q) => $q->where('code', 'WAJIB'))
                ->whereRaw('DATE_FORMAT(transaction_date, "%Y-%m") = ?', [$m])
                ->sum('amount')),
            $savings->filter(fn($s) => $s->savingsType && $s->savingsType->code === 'WAJIB')->sum('amount'),
            $savings->sum(fn($saving) => $saving->transaction_type === 'withdrawal' ? -1 * (float) $saving->amount : (float) $saving->amount),
            $member->loans->sum('principal_amount'),
            $member->installments->where('status', 'paid')->sum('amount'),
        ];
    }

    public function columnFormats(): array
    {
        $formats = [
            'C' => NumberFormat::FORMAT_TEXT,
        ];

        $totalColumns = 6 + count($this->months) + 4;

        for ($i = 6; $i <= $totalColumns; $i++) {
            $columnLetter = Coordinate::stringFromColumnIndex($i);
            $formats[$columnLetter] = '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"??_);_(@_)';
        }

        return $formats;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->insertNewRowBefore(1, 3);

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                // Merge judul mulai dari A2 untuk menengahkan judul
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $period = ($this->startMonth ?? 'Awal') . ' s/d ' . ($this->endMonth ?? 'Akhir');
                $sheet->setCellValue('A2', 'LAPORAN REKAP KOPERASI SIGER ' . strtoupper($period));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => '0056b3']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(50);
                // ... (rest of the events remain similar)

                $firstSavingsColumn = Coordinate::stringFromColumnIndex(7);
                $lastSavingsColumn = Coordinate::stringFromColumnIndex(7 + count($this->months));

                foreach (range(1, 6) as $columnIndex) {
                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                    $sheet->mergeCells($columnLetter . '4:' . $columnLetter . '5');
                }

                if ($firstSavingsColumn !== $lastSavingsColumn) {
                    $sheet->mergeCells($firstSavingsColumn . '4:' . $lastSavingsColumn . '4');
                }

                for ($columnIndex = 8 + count($this->months); $columnIndex <= Coordinate::columnIndexFromString($highestColumn); $columnIndex++) {
                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                    $sheet->mergeCells($columnLetter . '4:' . $columnLetter . '5');
                }

                $headerRange = 'A4:' . $highestColumn . '5';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0056b3']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                if ($highestRow >= 6) {
                    $dataRange = 'A6:' . $highestColumn . $highestRow;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }
            },
        ];
    }
}

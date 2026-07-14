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

    protected $month;
    protected $months;
    private $rowNumber = 0;

    public function __construct($month)
    {
        $this->month = $month;
        $this->months = \App\Models\Savings::query()
            ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month')
            ->whereHas('savingsType', fn($q) => $q->where('code', 'WAJIB'))
            ->distinct()
            ->orderBy('month')
            ->pluck('month');
    }

    public function collection()
    {
        return Member::with(['savings.savingsType', 'loans', 'installments'])
            ->orderBy('name', 'asc')
            ->get();
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
            $member->savings()->whereHas('savingsType', fn($q) => $q->where('code', 'WAJIB'))->sum('amount'),
        ];
    }

    public function columnFormats(): array
    {
        $formats = [
            'C' => NumberFormat::FORMAT_TEXT,
        ];

        $totalColumns = 6 + count($this->months) + 1;

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
                $sheet->setCellValue('A2', 'LAPORAN REKAP KOPERASI ' . strtoupper($this->month ?? 'SEMUA WAKTU'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => '0056b3']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(50);

                $firstSavingsColumn = Coordinate::stringFromColumnIndex(7);
                $lastSavingsColumn = $highestColumn;

                foreach (range(1, 6) as $columnIndex) {
                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                    $sheet->mergeCells($columnLetter . '4:' . $columnLetter . '5');
                }

                if ($firstSavingsColumn !== $lastSavingsColumn) {
                    $sheet->mergeCells($firstSavingsColumn . '4:' . $lastSavingsColumn . '4');
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

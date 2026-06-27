<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Savings;
use App\Models\SavingsType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SavingsSeeder extends Seeder
{
    /**
     * Seed transaksi Simpanan Pokok dan Wajib per anggota, per bulan.
     *
     * Karena rekap dari Excel hanya menyediakan saldo AKHIR kumulatif per
     * bulan, kita menyimpan saldo April di kolom tambahan dan menghitung
     * setoran bulan Mei sebagai (saldo_mei - saldo_april), dst. Simpanan
     * Pokok dicatat satu kali di bulan gabung (2026-04-01).
     */
    public function run(): void
    {
        $creatorId = User::where('email', 'admin@koperasi.com')->value('id');
        $pokokTypeId = SavingsType::where('code', 'POKOK')->value('id');
        $wajibTypeId = SavingsType::where('code', 'WAJIB')->value('id');

        if (! $pokokTypeId || ! $wajibTypeId) {
            return;
        }

        // Saldo AKHIR Simpanan Wajib per akhir bulan. Kunci: nama anggota
        // persis seperti di KoperasiMemberSeeder. Untuk konsistensi, dipakai
        // member_number sebagai kunci lookup via tabel members.
        $saldo = [
            'KOP-0001' => [2700000, 2725000, 3000000],
            'KOP-0002' => [1500000, 1525000, 1800000],
            'KOP-0003' => [1925000, 1950000, 2225000],
            'KOP-0004' => [1625000, 1650000, 1925000],
            'KOP-0005' => [2125000, 2125000, 2400000],
            'KOP-0006' => [2600000, 2625000, 2900000],
            'KOP-0007' => [2525000, 2550000, 2825000],
            'KOP-0008' => [2700000, 2725000, 3000000],
            'KOP-0009' => [2700000, 2725000, 3000000],
            'KOP-0010' => [2700000, 2725000, 3000000],
            'KOP-0011' => [2700000, 2725000, 3000000],
            'KOP-0012' => [2700000, 2725000, 3000000],
            'KOP-0013' => [2625000, 2650000, 2925000],
            'KOP-0014' => [2625000, 2650000, 2925000],
            'KOP-0015' => [2700000, 2725000, 3000000],
            'KOP-0016' => [2625000, 2650000, 2925000],
            'KOP-0017' => [2700000, 2725000, 3000000],
            'KOP-0018' => [2700000, 2725000, 3000000],
            'KOP-0019' => [2700000, 2725000, 3000000],
            'KOP-0020' => [2700000, 2725000, 3000000],
            'KOP-0021' => [2700000, 2725000, 3000000],
            'KOP-0022' => [2700000, 2725000, 3000000],
            'KOP-0023' => [2700000, 2725000, 3000000],
            'KOP-0024' => [2200000, 2225000, 2500000],
            'KOP-0025' => [2475000, 2475000, 2725000],
            'KOP-0026' => [2575000, 2600000, 2875000],
            'KOP-0027' => [2525000, 2550000, 2825000],
            'KOP-0028' => [2325000, 2350000, 2625000],
            'KOP-0029' => [2025000, 2025000, 2275000],
            'KOP-0030' => [1275000, 1275000, 1525000],
            'KOP-0031' => [1625000, 1625000, 1875000],
            'KOP-0032' => [1825000, 1850000, 2125000],
            'KOP-0033' => [1900000, 1925000, 2200000],
            'KOP-0034' => [1900000, 1925000, 2200000],
            'KOP-0035' => [1750000, 1750000, 2000000],
            'KOP-0036' => [1125000, 1150000, 1425000],
            'KOP-0037' => [1500000, 1525000, 1800000],
            'KOP-0038' => [1500000, 1525000, 1800000],
            'KOP-0039' => [1500000, 1525000, 1800000],
            'KOP-0040' => [1500000, 1525000, 1800000],
            'KOP-0041' => [1000000, 1025000, 1300000],
            'KOP-0042' => [1375000, 1400000, 1675000],
            'KOP-0043' => [2700000, 2725000, 3000000],
            'KOP-0044' => [825000, 850000, 1125000],
            'KOP-0045' => [2425000, 2450000, 2725000],
            'KOP-0046' => [2350000, 2375000, 2650000],
            'KOP-0047' => [1900000, 1925000, 2200000],
            'KOP-0048' => [2150000, 2175000, 2450000],
            'KOP-0049' => [2175000, 2200000, 2475000],
            'KOP-0050' => [1775000, 1800000, 2075000],
            'KOP-0051' => [775000, 800000, 1075000],
            'KOP-0052' => [1650000, 1675000, 1950000],
            'KOP-0053' => [850000, 875000, 1150000],
            'KOP-0054' => [1450000, 1450000, 1700000],
            'KOP-0055' => [1225000, 1225000, 1475000],
            'KOP-0056' => [950000, 950000, 1200000],
            'KOP-0057' => [925000, 925000, 1175000],
            'KOP-0058' => [1250000, 1250000, 1500000],
            'KOP-0059' => [525000, 525000, 775000],
            'KOP-0060' => [1250000, 1250000, 1500000],
            'KOP-0061' => [600000, 600000, 850000],
            'KOP-0062' => [750000, 750000, 1000000],
            'KOP-0063' => [1075000, 1075000, 1325000],
            'KOP-0064' => [1025000, 1025000, 1275000],
            'KOP-0065' => [525000, 525000, 775000],
            'KOP-0066' => [1075000, 1075000, 1325000],
            'KOP-0067' => [200000, 200000, 450000],
            'KOP-0068' => [400000, 400000, 650000],
            'KOP-0069' => [275000, 275000, 525000],
            'KOP-0070' => [300000, 300000, 550000],
            'KOP-0071' => [650000, 650000, 900000],
            'KOP-0072' => [425000, 425000, 675000],
            'KOP-0073' => [1075000, 1075000, 1325000],
            'KOP-0074' => [1075000, 1075000, 1325000],
            'KOP-0075' => [750000, 750000, 1000000],
            'KOP-0076' => [925000, 925000, 1175000],
            'KOP-0077' => [625000, 625000, 875000],
            'KOP-0078' => [875000, 875000, 1125000],
            'KOP-0079' => [325000, 325000, 575000],
            'KOP-0080' => [525000, 525000, 775000],
            'KOP-0081' => [575000, 575000, 825000],
            'KOP-0082' => [900000, 900000, 1150000],
            'KOP-0083' => [600000, 600000, 850000],
            'KOP-0084' => [400000, 400000, 650000],
            'KOP-0085' => [325000, 325000, 575000],
            'KOP-0086' => [420000, 420000, 670000],
            'KOP-0087' => [800000, 800000, 1050000],
            'KOP-0088' => [500000, 500000, 750000],
            'KOP-0089' => [250000, 250000, 500000],
            'KOP-0090' => [675000, 675000, 925000],
            'KOP-0091' => [400000, 400000, 650000],
            'KOP-0092' => [650000, 650000, 900000],
            'KOP-0093' => [200000, 200000, 450000],
            'KOP-0094' => [245000, 245000, 495000],
            'KOP-0095' => [475000, 475000, 725000],
            'KOP-0096' => [475000, 475000, 725000],
            'KOP-0097' => [675000, 675000, 925000],
            'KOP-0098' => [875000, 875000, 1125000],
            'KOP-0099' => [923000, 923000, 1173000],
            'KOP-0100' => [1050000, 1050000, 1300000],
            'KOP-0101' => [700000, 700000, 950000],
            'KOP-0102' => [950000, 950000, 1200000],
            'KOP-0103' => [300000, 300000, 550000],
            'KOP-0104' => [500000, 500000, 750000],
            'KOP-0105' => [1000000, 1000000, 1250000],
            'KOP-0106' => [550000, 550000, 800000],
            'KOP-0107' => [375000, 375000, 625000],
            'KOP-0108' => [250000, 250000, 500000],
            'KOP-0109' => [300000, 300000, 550000],
            'KOP-0110' => [450000, 450000, 700000],
            'KOP-0111' => [250000, 250000, 500000],
            'KOP-0112' => [250000, 250000, 500000],
            'KOP-0113' => [25000, 25000, 275000],
            'KOP-0114' => [800000, 800000, 1050000],
            'KOP-0115' => [850000, 850000, 1100000],
            // 24 anggota baru Juni (April/Mei = 0, Juni = 275000)
            'KOP-0116' => [0, 0, 275000],
            'KOP-0117' => [0, 0, 275000],
            'KOP-0118' => [0, 0, 275000],
            'KOP-0119' => [0, 0, 275000],
            'KOP-0120' => [0, 0, 275000],
            'KOP-0121' => [0, 0, 275000],
            'KOP-0122' => [0, 0, 275000],
            'KOP-0123' => [0, 0, 275000],
            'KOP-0124' => [0, 0, 275000],
            'KOP-0125' => [0, 0, 275000],
            'KOP-0126' => [0, 0, 275000],
            'KOP-0127' => [0, 0, 275000],
            'KOP-0128' => [0, 0, 275000],
            'KOP-0129' => [0, 0, 275000],
            'KOP-0130' => [0, 0, 275000],
            'KOP-0131' => [0, 0, 275000],
            'KOP-0132' => [0, 0, 275000],
            'KOP-0133' => [0, 0, 275000],
            'KOP-0134' => [0, 0, 275000],
            'KOP-0135' => [0, 0, 275000],
            'KOP-0136' => [0, 0, 275000],
            'KOP-0137' => [0, 0, 275000],
            'KOP-0138' => [0, 0, 275000],
            'KOP-0139' => [0, 0, 275000],
            'KOP-0140' => [800000, 800000, 1050000],
        ];

        $members = Member::whereIn('member_number', array_keys($saldo))->get()->keyBy('member_number');

        foreach ($saldo as $memberNumber => [$saldoApril, $saldoMei, $saldoJuni]) {
            $member = $members->get($memberNumber);
            if (! $member) {
                continue;
            }

            // Simpanan Pokok: 1 transaksi di bulan gabung (April 2026).
            $this->saveTransaction(
                memberId: $member->id,
                savingsTypeId: $pokokTypeId,
                amount: 250000,
                transactionDate: Carbon::create(2026, 4, 1),
                referenceNumber: 'POKOK-' . $memberNumber . '-202604',
                notes: 'Simpanan Pokok keanggotaan.',
                creatorId: $creatorId
            );

            // Simpanan Wajib: setoran per bulan = selisih saldo akhir bulan
            // ini dengan bulan sebelumnya. Bulan pertama (April) = saldoApril.
            $monthly = [
                [Carbon::create(2026, 4, 30), $saldoApril],
                [Carbon::create(2026, 5, 31), max(0, $saldoMei - $saldoApril)],
                [Carbon::create(2026, 6, 30), max(0, $saldoJuni - $saldoMei)],
            ];

            foreach ($monthly as $idx => [$date, $amount]) {
                if ($amount <= 0) {
                    continue;
                }
                $monthLabel = $date->format('Ym');
                $this->saveTransaction(
                    memberId: $member->id,
                    savingsTypeId: $wajibTypeId,
                    amount: $amount,
                    transactionDate: $date,
                    referenceNumber: 'WAJIB-' . $memberNumber . '-' . $monthLabel,
                    notes: 'Simpanan Wajib bulan ' . $date->locale('id')->translatedFormat('F Y') . '.',
                    creatorId: $creatorId
                );
            }
        }
    }

    private function saveTransaction(
        int $memberId,
        int $savingsTypeId,
        int $amount,
        Carbon $transactionDate,
        string $referenceNumber,
        string $notes,
        ?int $creatorId,
    ): void {
        Savings::updateOrCreate(
            ['reference_number' => $referenceNumber],
            [
                'member_id' => $memberId,
                'savings_type_id' => $savingsTypeId,
                'transaction_type' => 'deposit',
                'amount' => $amount,
                'transaction_date' => $transactionDate->toDateString(),
                'notes' => $notes,
                'created_by' => $creatorId,
            ]
        );
    }
}

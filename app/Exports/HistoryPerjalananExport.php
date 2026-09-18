<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class HistoryPerjalananExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $inventaris;
    protected $timeline;
    protected $user;

    public function __construct($inventaris, $timeline, $user)
    {
        $this->inventaris = $inventaris;
        $this->timeline = $timeline;
        $this->user = $user;
    }

    public function collection()
    {
        return $this->timeline->values()->map(function ($item, $index) {
            $tanggal = '-';
            if (!empty($item['tanggal'])) {
                if ($item['tanggal'] instanceof Carbon) {
                    $tanggal = $item['tanggal']->format('d-m-Y H:i');
                } else {
                    $tanggal = Carbon::parse($item['tanggal'])->format('d-m-Y H:i');
                }
            }

            return [
                'No' => $index + 1,
                'Tanggal' => $tanggal,
                'Kode Aset' => $item['kode_aset'] ?? '-',
                'No Inventaris' => $item['inventaris'] ?? '-',
                'Aktivitas' => $item['aktivitas'] ?? '-',
                'User Baru' => $item['user_baru'] ?? $item['user_lama'] ?? '-',
                'Lokasi Baru' => $item['lokasi_baru'] ?? $item['lokasi_lama'] ?? '-',
                'Hak Akses' => $item['hak_akses'] ?? '-',
                'Keterangan' => $item['keterangan'] ?? '-',
                'Petugas' => $item['petugas'] ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL',
            'KODE ASET',
            'NO INVENTARIS',
            'AKTIVITAS',
            'USER BARU',
            'LOKASI BARU',
            'HAK AKSES',
            'KETERANGAN',
            'PETUGAS',
        ];
    }
}

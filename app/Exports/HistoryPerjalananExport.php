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

            // Format Aktivitas
            $aktivitas = $item['aktivitas'] ?? '-';
            if ($aktivitas === 'MUTASI') {
                $aktivitas = !empty($item['is_antar_perusahaan']) ? 'MUTASI ANTAR PT' : 'MUTASI INTERNAL';
            }

            // Format Pengguna Terakhir
            $penggunaTerakhir = $item['user_baru'] ?? $item['user_lama'] ?? '-';
            if (in_array($item['aktivitas'] ?? '', ['PEMINJAMAN', 'PENGEMBALIAN PINJAMAN'])) {
                $penggunaTerakhir = ($item['user_lama'] ?? '-') . ' -> ' . ($item['user_baru'] ?? '-');
            }

            // Format Lokasi & Perusahaan
            $lokasiPerusahaan = ($item['perusahaan'] ?? '-') . ' - ' . ($item['lokasi_baru'] ?? $item['lokasi_lama'] ?? '-');
            if (($item['aktivitas'] ?? '') === 'MUTASI') {
                if (!empty($item['is_antar_perusahaan'])) {
                    $lokasiPerusahaan = ($item['perusahaan_asal'] ?? '-') . ' [' . ($item['lokasi_lama'] ?? '-') . '] -> ' . ($item['perusahaan_tujuan'] ?? '-') . ' [' . ($item['lokasi_baru'] ?? '-') . ']';
                } else {
                    $lokasiPerusahaan = ($item['perusahaan'] ?? '-') . ' (' . ($item['lokasi_lama'] ?? '-') . ' -> ' . ($item['lokasi_baru'] ?? '-') . ')';
                }
            } elseif (in_array($item['aktivitas'] ?? '', ['PEMINJAMAN', 'PENGEMBALIAN PINJAMAN'])) {
                $lokasiPerusahaan = ($item['perusahaan'] ?? '-') . ' (' . ($item['lokasi_lama'] ?? '-') . ' -> ' . ($item['lokasi_baru'] ?? '-') . ')';
            }

            return [
                'No' => $index + 1,
                'Tanggal' => $tanggal,
                'Kode Aset' => $item['kode_aset'] ?? '-',
                'No Inventaris' => $item['inventaris'] ?? '-',
                'Aktivitas' => $aktivitas,
                'Pengguna Terakhir' => $penggunaTerakhir,
                'Lokasi & Perusahaan' => $lokasiPerusahaan,
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
            'PENGGUNA TERAKHIR',
            'LOKASI & PERUSAHAAN',
            'HAK AKSES',
            'KETERANGAN',
            'PETUGAS',
        ];
    }
}

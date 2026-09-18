<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HistoryPerjalananIndexExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $inventarisList;
    protected $user;

    public function __construct($inventarisList, $user)
    {
        $this->inventarisList = $inventarisList;
        $this->user = $user;
    }

    public function collection()
    {
        return $this->inventarisList->values()->map(function ($inv, $index) {
            $namaPemakai = 'Belum dipakai';
            if ($inv->status == 'DIPAKAI' && $inv->keluarTerakhir) {
                $maping = $inv->keluarTerakhir->maping;
                if ($maping && $maping->status == 'aktif') {
                    $namaPemakai = $maping->jenis_penerima == 'Perorangan' ? ($maping->karyawan?->nama_karyawan ?? '-') : ($maping->divisi ?? '-');
                } else {
                    $namaPemakai = $inv->keluarTerakhir->jenis_penerima == 'Perorangan' ? ($inv->keluarTerakhir->karyawan?->nama_karyawan ?? '-') : ($inv->keluarTerakhir->divisi_klr ?? '-');
                }
            } elseif ($inv->status == 'DIPINJAM' && $inv->peminjamanTerakhir) {
                $namaPemakai = $inv->peminjamanTerakhir->karyawan?->nama_karyawan ?? ($inv->peminjamanTerakhir->karyawanTujuan?->nama_karyawan ?? '-');
            }

            return [
                'No' => $index + 1,
                'Perusahaan' => $inv->perusahaan?->nama_perusahaan ?? '-',
                'Kode Aset' => $inv->kode_aset ?? '-',
                'No Inventaris' => $inv->no_inventaris ?? '-',
                'Kategori' => $inv->dataAset?->kategori?->nama_barang ?? '-',
                'Merek' => $inv->dataAset?->merek ?? '-',
                'Type' => $inv->dataAset?->type ?? '-',
                'Status Saat Ini' => $inv->status ?? '-',
                'Pemakai Saat Ini' => $namaPemakai,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'NO',
            'PERUSAHAAN',
            'KODE ASET',
            'NO INVENTARIS',
            'KATEGORI',
            'MEREK',
            'TYPE',
            'STATUS SAAT INI',
            'PEMAKAI SAAT INI',
        ];
    }
}

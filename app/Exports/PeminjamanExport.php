<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PeminjamanExport implements FromCollection, WithHeadings
{
  protected $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function collection()
  {
    $query = Peminjaman::with([
      'inventaris.dataAset.kategori',
      'inventaris.perusahaan',
      'karyawan',
      'karyawanTujuan',
      'perusahaanTujuan',
    ]);

    // Perusahaan
    if (auth()->user()->role != 'super_admin') {
      $query->whereHas('inventaris', function ($q) {
        $q->where('perusahaan_id', auth()->user()->id_perusahaan);
      });
    } elseif ($this->request->filled('perusahaan')) {
      $query->whereHas('inventaris', function ($q) {
        $q->where('perusahaan_id', $this->request->perusahaan);
      });
    }

    // Tanggal
    if ($this->request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_pinjam', '>=', $this->request->tanggal_awal);
    }

    if ($this->request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_pinjam', '<=', $this->request->tanggal_akhir);
    }

    // Jenis
    if ($this->request->filled('jenis')) {
      $query->where('jenis_peminjaman', $this->request->jenis);
    }

    // Status
    if ($this->request->filled('status')) {
      $query->where('status', $this->request->status);
    }

    // Search
    if ($this->request->filled('search')) {
      $search = $this->request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_peminjaman', 'like', "%{$search}%")

          ->orWhereHas('inventaris', function ($i) use ($search) {
            $i->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
          })

          ->orWhereHas('karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('karyawanTujuan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('perusahaanTujuan', function ($p) use ($search) {
            $p->where('nama_perusahaan', 'like', "%{$search}%");
          });
      });
    }

    return $query
      ->orderBy('tanggal_pinjam')
      ->get()
      ->map(function ($item) {
        return [
          'Kode Peminjaman' => $item->kode_peminjaman,

          'Perusahaan' => optional($item->inventaris->perusahaan)->nama_perusahaan,

          'Kode Aset' => optional($item->inventaris)->kode_aset,

          'No Inventaris' => optional($item->inventaris)->no_inventaris,

          'Kategori' => optional(optional($item->inventaris)->dataAset->kategori)->nama_barang,

          'Merek' => optional(optional($item->inventaris)->dataAset)->merek,

          'Type' => optional(optional($item->inventaris)->dataAset)->type,

          'Jenis Peminjaman' => strtoupper($item->jenis_peminjaman),

          'Peminjam' =>
            $item->jenis_peminjaman == 'internal'
              ? optional($item->karyawan)->nama_karyawan
              : optional($item->perusahaanTujuan)->nama_perusahaan,

          'Tanggal Pinjam' => $item->tanggal_pinjam,

          'Tanggal Rencana Kembali' => $item->tanggal_rencana_kembali,

          'Tanggal Kembali' => $item->tanggal_kembali,

          'Status' => $item->status,

          'Keperluan' => $item->keperluan,
        ];
      });
  }

  public function headings(): array
  {
    return [
      'KODE PEMINJAMAN',
      'PERUSAHAAN',
      'KODE ASET',
      'NO INVENTARIS',
      'KATEGORI',
      'MEREK',
      'TYPE',
      'JENIS',
      'PEMINJAM',
      'TANGGAL PINJAM',
      'RENCANA KEMBALI',
      'TANGGAL KEMBALI',
      'STATUS',
      'KEPERLUAN',
    ];
  }
}

<?php

namespace App\Exports;

use App\Models\Keluar;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KeluarExport implements FromCollection, WithHeadings
{
  protected $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function collection()
  {
    $user = auth()->user();

    $query = Keluar::with(['inventaris.dataAset.kategori', 'inventaris.perusahaan', 'karyawan', 'perusahaan']);

    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($this->request->perusahaan_id) {
      $query->where('perusahaan_id', $this->request->perusahaan_id);
    }

    if ($this->request->tanggal_awal) {
      $query->whereDate('tgl_keluar', '>=', $this->request->tanggal_awal);
    }

    if ($this->request->tanggal_akhir) {
      $query->whereDate('tgl_keluar', '<=', $this->request->tanggal_akhir);
    }

    if ($this->request->kategori_id) {
      $query->whereHas('inventaris.dataAset', function ($q) {
        $q->where('kategori_id', $this->request->kategori_id);
      });
    }

    if ($this->request->search) {
      $search = $this->request->search;

      $query->where(function ($q) use ($search) {
        $q->whereHas('inventaris', function ($i) use ($search) {
          $i->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
        })
          ->orWhereHas('karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          })
          ->orWhere('divisi_klr', 'like', "%{$search}%");
      });
    }

    return $query
      ->orderBy('tgl_keluar')
      ->get()
      ->map(function ($item) {
        return [
          'Perusahaan' => $item->perusahaan->nama_perusahaan ?? '-',

          'Kode Aset' => $item->inventaris->kode_aset ?? '-',

          'No Inventaris' => $item->inventaris->no_inventaris ?? '-',

          'Kategori' => $item->inventaris->dataAset->kategori->nama_barang ?? '-',

          'Merek' => $item->inventaris->dataAset->merek ?? '-',

          'Type' => $item->inventaris->dataAset->type ?? '-',

          'Penerima' => $item->jenis_penerima == 'Perorangan' ? $item->karyawan->nama_karyawan ?? '-' : 'DIVISI',

          'Divisi' => $item->jenis_penerima == 'Perorangan' ? $item->karyawan->divisi ?? '-' : $item->divisi_klr ?? '-',

          'Tanggal Keluar' => $item->tgl_keluar,

          'Status Inventaris' => $item->inventaris->status ?? '-',
        ];
      });
  }

  public function headings(): array
  {
    return [
      'PERUSAHAAN',
      'KODE ASET',
      'NO INVENTARIS',
      'KATEGORI',
      'MEREK',
      'TYPE',
      'PENERIMA',
      'DIVISI',
      'TANGGAL KELUAR',
      'STATUS',
    ];
  }
}

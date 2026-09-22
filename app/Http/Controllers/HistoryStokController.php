<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Models\HistoryPencabutan;
use App\Models\HistoryMutasi;
use App\Models\Inventaris;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HistoryStokController extends Controller
{
  public function historyStok(Request $request, string $dataAsetId)
  {
    $inventaris = Inventaris::with([
      'keluars.karyawan',
      'keluars.user',
      'keluars.lokasi',
      'keluars.maping',
      'dataAset.kategori',
      'perusahaan',
    ])
      ->where('data_aset_id', $dataAsetId)
      ->orderBy('kode_aset')
      ->get();

    $kodeAsets = $inventaris
      ->pluck('kode_aset')
      ->unique()
      ->sort()
      ->values();

    $timeline = $this->buildTimeline($inventaris, $request);

    return view('content.dashboard.transaksi-masuk.history-stok', compact('inventaris', 'timeline', 'kodeAsets'));
  }
  private function buildTimeline(Collection $inventaris, ?Request $request = null)
  {
    $timeline = collect();

    /*
|--------------------------------------------------------------------------
| SELURUH TRANSAKSI KELUAR
|--------------------------------------------------------------------------
*/

    foreach ($inventaris as $item) {
      foreach ($item->keluars->sortBy('tgl_keluar') as $index => $keluar) {
        /*
        |--------------------------------------------------------------------------
        | Nama penerima
        |--------------------------------------------------------------------------
        */

        if ($keluar->jenis_penerima == 'Perorangan') {
          $penerima = optional($keluar->karyawan)->nama_karyawan;
        } else {
          $penerima = $keluar->divisi_klr;
        }

        /*
        |--------------------------------------------------------------------------
        | Timeline
        |--------------------------------------------------------------------------
        */

        $timeline->push([
          'tanggal' => Carbon::parse($keluar->tgl_keluar ?? $keluar->created_at),

          'aktivitas' => 'KELUAR',

          'kode_aset' => $item->kode_aset,

          'inventaris' => $item->no_inventaris,

          'user_lama' => null,

          'user_baru' => $penerima,

          'lokasi_lama' => null,

          'lokasi_baru' => optional($keluar->lokasi)->nama_lokasi,

          'hak_akses' => null,

          'jenis_mutasi' => null,

          'keterangan' => $index == 0 ? 'Asset pertama kali digunakan' : 'Asset digunakan kembali setelah pencabutan',

          'petugas' => optional($keluar->user)->name,
        ]);
      }
    }
    /*
|--------------------------------------------------------------------------
| HISTORY MUTASI
|--------------------------------------------------------------------------
*/
    $historyMutasi = HistoryMutasi::with('creator')
      ->whereIn('inventaris_id', $inventaris->pluck('id'))
      ->orderBy('tanggal_mutasi')
      ->get();
    // dd([
    //   'jenis_penerima' => $keluar->jenis_penerima,
    //   'karyawan_id' => $keluar->karyawan_id,
    //   'karyawan' => $keluar->karyawan,
    //   'divisi_klr' => $keluar->divisi_klr,
    // ]);

    foreach ($historyMutasi as $item) {
      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal_mutasi ?? $item->created_at),

        'aktivitas' => 'MUTASI',

        'kode_aset' => $item->kode_aset_baru,

        'kode_aset_lama' => $item->kode_aset_lama,

        'kode_aset_baru' => $item->kode_aset_baru,

        'inventaris' => $item->no_inventaris_baru,

        'inventaris_lama' => $item->no_inventaris_lama,

        'inventaris_baru' => $item->no_inventaris_baru,

        'user_lama' => $item->user_lama,

        'user_baru' => $item->user_baru,

        'lokasi_lama' => $item->lokasi_lama,

        'lokasi_baru' => $item->lokasi_baru,

        'hak_akses' => $item->opsi_hak_akses,

        'jenis_mutasi' => $item->jenis_mutasi,

        'keterangan' => $item->jenis_mutasi == 'internal' ? 'Mutasi Internal' : 'Mutasi Antar Perusahaan',

        'petugas' => optional($item->creator)->name,
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORY PENCABUTAN
    |--------------------------------------------------------------------------
    */

    $historyCabut = HistoryPencabutan::with(['creator', 'maping.karyawan', 'maping.keluar.karyawan', 'inventaris.keluarTerakhir.karyawan'])
      ->whereIn('inventaris_id', $inventaris->pluck('id'))->get();

    foreach ($historyCabut as $item) {
      $userCabut = $item->user_lama;
      if (empty($userCabut) || $userCabut === '-') {
        $userCabut = $item->maping?->penerima
          ?? $item->maping?->karyawan?->nama_karyawan
          ?? $item->maping?->keluar?->karyawan?->nama_karyawan
          ?? $item->maping?->divisi
          ?? $item->maping?->keluar?->divisi_klr
          ?? $item->inventaris?->keluarTerakhir?->karyawan?->nama_karyawan
          ?? $item->inventaris?->keluarTerakhir?->divisi_klr;
      }

      $lokasiLama = $item->lokasi_lama;
      if (empty($lokasiLama) || $lokasiLama === '-') {
        $lokasiLama = $item->maping?->lokasi?->nama_lokasi
          ?? $item->maping?->keluar?->lokasi?->nama_lokasi;
      }

      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal_pencabutan ?? $item->created_at),

        'aktivitas' => 'PENCABUTAN',

        'kode_aset' => $item->kode_aset,

        'inventaris' => $item->no_inventaris,

        'user_lama' => $userCabut,

        'user_baru' => null,

        'lokasi_lama' => $lokasiLama,

        'lokasi_baru' => $item->lokasi_baru,

        'hak_akses' => null,

        'jenis_mutasi' => null,

        'keterangan' => $item->alasan,

        'petugas' => optional($item->creator)->name,
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request) {
      if ($request->filled('tanggal_awal')) {
        $start = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
        $timeline = $timeline->filter(function ($item) use ($start) {
          return $item['tanggal']->gte($start);
        });
      }

      if ($request->filled('tanggal_akhir')) {
        $end = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
        $timeline = $timeline->filter(function ($item) use ($end) {
          return $item['tanggal']->lte($end);
        });
      }
    }
    /*
|--------------------------------------------------------------------------
| FILTER KODE ASET
|--------------------------------------------------------------------------
*/

    if ($request && $request->filled('kode_aset')) {
      $kode = $request->kode_aset;

      $timeline = $timeline->filter(function ($item) use ($kode) {
        return ($item['kode_aset'] ?? null) == $kode ||
          ($item['kode_aset_lama'] ?? null) == $kode ||
          ($item['kode_aset_baru'] ?? null) == $kode;
      });
    }

    return $timeline->sortByDesc('tanggal')->values();
  }
  public function cetakHistoryStok(Request $request, $dataAsetId)
  {
    $inventaris = Inventaris::with([
      'keluars.karyawan',
      'keluars.user',
      'keluars.lokasi',
      'dataAset.kategori',
      'perusahaan',
    ])
      ->where('data_aset_id', $dataAsetId)
      ->get();

    $timeline = $this->buildTimeline($inventaris, $request);

    return view('content.dashboard.transaksi-masuk.cetak-history-stok', compact('inventaris', 'timeline'));
  }
}

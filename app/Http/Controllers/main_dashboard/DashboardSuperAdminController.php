<?php

namespace App\Http\Controllers\main_dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masuk;
use App\Models\Keluar;
use App\Models\Peminjaman;
use App\Models\Maintenance;
use App\Models\Maping;
use App\Models\Inventaris;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\HistoryMutasi;
use Illuminate\Support\Facades\DB;

class DashboardSuperAdminController extends Controller
{
  public function superAdmin()
  {
    $now = Carbon::now('Asia/Jakarta');

    $dashboard = [
      'executive' => $this->executive(),

      'company' => $this->companyPerformance(),
      'inventaris' => $this->inventarisGlobal(),

      'transaksi' => $this->transaksiGlobal(),
      'komposisi' => $this->komposisiInventaris(),

      'grafik' => $this->grafikGlobal(),

      'timeline' => $this->timelineGlobal(),

      'reminder' => $this->reminderGlobal(),
    ];

    return view('content.dashboard.superadmin', compact('dashboard', 'now'));
  }
  private function executive()
  {
    return [
      'perusahaan' => Perusahaan::count(),

      'inventaris' => Inventaris::count(),

      'user' => User::count(),

      'mapping' => Maping::count(),

      'maintenance' => Maintenance::count(),

      'peminjaman' => Peminjaman::count(),
    ];
  }
  private function companyPerformance()
  {
    return Perusahaan::all()->map(function ($perusahaan) {
      return [
        'nama' => $perusahaan->nama_perusahaan,

        'primary' => $perusahaan->primary_color,

        'secondary' => $perusahaan->secondary_color,

        'aset' => Inventaris::where('perusahaan_id', $perusahaan->id)->count(),

        'mapping' => Maping::where('id_perusahaan', $perusahaan->id)->count(),

        'user' => User::where('id_perusahaan', $perusahaan->id)->count(),

        'maintenance' => Maintenance::whereHas('inventaris', function ($q) use ($perusahaan) {
          $q->where('perusahaan_id', $perusahaan->id);
        })->count(),
      ];
    });
  }
  private function inventarisGlobal()
  {
    return [
      'tersedia' => Inventaris::where('status', 'TERSEDIA')->count(),

      'dipakai' => Inventaris::where('status', 'DIPAKAI')->count(),

      'dipinjam' => Inventaris::where('status', 'DIPINJAM')->count(),

      'rusak' => Inventaris::where('status', 'RUSAK')->count(),
      'afkir' => Inventaris::where('status', 'AFKIR')->count(),
    ];
  }
  private function transaksiGlobal()
  {
    $bulan = now()->month;
    $tahun = now()->year;

    return [
      'penerimaan' => [
        'total' => Masuk::count(),

        'bulan_ini' => Masuk::whereMonth('created_at', $bulan)
          ->whereYear('created_at', $tahun)
          ->count(),
      ],

      'pemakaian' => [
        'total' => Keluar::count(),

        'bulan_ini' => Keluar::whereMonth('created_at', $bulan)
          ->whereYear('created_at', $tahun)
          ->count(),
      ],

      'mutasi' => [
        'total' => HistoryMutasi::count(),

        'bulan_ini' => HistoryMutasi::whereMonth('created_at', $bulan)
          ->whereYear('created_at', $tahun)
          ->count(),
      ],

      'maintenance' => [
        'total' => Maintenance::count(),

        'diproses' => Maintenance::where('status', 'Diproses')->count(),
      ],

      'peminjaman' => [
        'total' => Peminjaman::count(),

        'dipinjam' => Peminjaman::where('status', 'Dipinjam')->count(),
      ],
    ];
  }
  private function grafikGlobal()
  {
    $bulan = [];
    $masuk = [];
    $keluar = [];
    $mutasi = [];
    $maintenance = [];
    $peminjaman = [];

    for ($i = 1; $i <= 12; $i++) {
      $bulan[] = Carbon::create()
        ->month($i)
        ->translatedFormat('M');

      $masuk[] = Masuk::whereMonth('created_at', $i)
        ->whereYear('created_at', now()->year)
        ->count();

      $keluar[] = Keluar::whereMonth('created_at', $i)
        ->whereYear('created_at', now()->year)
        ->count();

      $mutasi[] = HistoryMutasi::whereMonth('created_at', $i)
        ->whereYear('created_at', now()->year)
        ->count();

      $maintenance[] = Maintenance::whereMonth('created_at', $i)
        ->whereYear('created_at', now()->year)
        ->count();

      $peminjaman[] = Peminjaman::whereMonth('created_at', $i)
        ->whereYear('created_at', now()->year)
        ->count();
    }

    return [
      'bulan' => $bulan,

      'masuk' => $masuk,

      'keluar' => $keluar,

      'mutasi' => $mutasi,

      'maintenance' => $maintenance,

      'peminjaman' => $peminjaman,
    ];
  }
  private function timelineGlobal()
  {
    $timeline = collect();

    /*
    |--------------------------------------------------------------------------
    | PENERIMAAN
    |--------------------------------------------------------------------------
    */

    foreach (
      Masuk::with('perusahaan')
        ->latest()
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'judul' => 'Penerimaan Aset',

        'perusahaan' => optional($item->perusahaan)->nama_perusahaan,

        'icon' => 'bx bx-download',

        'color' => 'success',

        'waktu' => $item->created_at,
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PEMAKAIAN
    |--------------------------------------------------------------------------
    */

    foreach (
      Keluar::with('perusahaan')
        ->latest()
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'judul' => 'Pemakaian Aset',

        'perusahaan' => optional($item->perusahaan)->nama_perusahaan,

        'icon' => 'bx bx-desktop',

        'color' => 'primary',

        'waktu' => $item->created_at,
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MAINTENANCE
    |--------------------------------------------------------------------------
    */

    foreach (
      Maintenance::with('inventaris.perusahaan')
        ->latest()
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'judul' => $item->kode_service,

        'perusahaan' => optional($item->inventaris->perusahaan)->nama_perusahaan,

        'icon' => 'bx bx-wrench',

        'color' => 'danger',

        'waktu' => $item->created_at,
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    foreach (
      Peminjaman::with('perusahaanTujuan')
        ->latest()
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'judul' => $item->kode_peminjaman,

        'perusahaan' => optional($item->perusahaanTujuan)->nama_perusahaan,

        'icon' => 'bx bx-transfer',

        'color' => 'warning',

        'waktu' => $item->created_at,
      ]);
    }

    return $timeline

      ->sortByDesc('waktu')

      ->take(10)

      ->values();
  }
  private function komposisiInventaris()
  {
    return Inventaris::with('dataAset.kategori')
      ->get()
      ->groupBy(function ($item) {
        return optional($item->dataAset->kategori)->nama_barang ?? 'Lainnya';
      })
      ->map(function ($items, $kategori) {
        return [
          'kategori' => $kategori,

          'total' => $items->count(),
        ];
      })
      ->sortByDesc('total')
      ->values();
  }
  private function reminderGlobal()
  {
    return [
      'maintenance_pengajuan' => Maintenance::where('status', 'Pengajuan')->count(),

      'maintenance_diproses' => Maintenance::where('status', 'Diproses')->count(),

      'peminjaman_aktif' => Peminjaman::where('status', 'Dipinjam')->count(),

      'mapping_servis' => Maping::where('status', 'servis')->count(),

      'inventaris_rusak' => Inventaris::where('status', 'RUSAK')->count(),
      'inventaris_afkir' => Inventaris::where('status', 'AFKIR')->count(),
    ];
  }
}

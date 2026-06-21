<?php

namespace App\Http\Controllers\main_dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masuk;
use App\Models\Keluar;
use App\Models\Peminjaman;
use App\Models\MutasiMaping;
use App\Models\Maping;
use App\Models\Inventaris;
use App\Models\User;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\DB;

class DashboardSuperAdminController extends Controller
{
  public function index()
  {
    $now = Carbon::now('Asia/Jakarta');

    /* =====================================
| TOTAL
===================================== */

    // TOTAL MASUK
   $totalMasuk = Inventaris::count();

    // TOTAL KELUAR
    $totalKeluar = Keluar::distinct('inventaris_id')->count();

    // STOK REAL
    $totalStok = $totalMasuk - $totalKeluar;

    // TOTAL DIGUNAKAN
    $totalDigunakan = Maping::count();

    // TOTAL ASET
    $totalAset = $totalMasuk;

    /* =====================================
| KOMPOSISI STOK REAL
===================================== */

    $komposisiAset = Masuk::with(['kategori', 'keluars'])

      ->get()

      ->groupBy(function ($item) {
        return $item->kategori->nama_barang ?? 'LAINNYA';
      })

      ->map(function ($items, $namaBarang) {
        $stokMasuk = $items->sum('jumlah');

        $stokKeluar = $items->sum(function ($item) {
          return $item->keluars->sum('jumlah');
        });

        return [
          'nama_barang' => $namaBarang,
          'total' => max(0, $stokMasuk - $stokKeluar),
        ];
      })

      ->sortByDesc('total')

      ->values();

    /* =====================================
        | PEMINJAMAN
        ===================================== */

    $dipinjam = Peminjaman::where('status', 'Dipinjam')->count();

    $dikembalikan = Peminjaman::where('status', 'Dikembalikan')->count();

    /* =====================================
        | MUTASI
        ===================================== */

    $totalMutasi = MutasiMaping::count();

    /* =====================================
        | USER
        ===================================== */

    $perusahaanCount = Perusahaan::count();

    $petugasCount = User::where('role', 'petugas')->count();


    /* =====================================
        | GRAFIK
        ===================================== */

    $mutasiMasuk = Masuk::selectRaw('MONTH(created_at) bulan, COUNT(*) total')
      ->groupBy('bulan')
      ->pluck('total', 'bulan')
      ->toArray();

    $mutasiKeluar = Keluar::selectRaw('MONTH(created_at) bulan, COUNT(*) total')
      ->groupBy('bulan')
      ->pluck('total', 'bulan')
      ->toArray();

    $bulanLabel = [];

    $dataMasuk = [];

    $dataKeluar = [];

    for ($i = 1; $i <= 12; $i++) {
      $bulanLabel[] = Carbon::create()
        ->month($i)
        ->translatedFormat('M');

      $dataMasuk[] = $mutasiMasuk[$i] ?? 0;

      $dataKeluar[] = $mutasiKeluar[$i] ?? 0;
    }

    /* =====================================
| LIST PERUSAHAAN
===================================== */

   $perusahaanList = collect();

    return view(
      'content.dashboard.superadmin',
      compact(
        'now',
        'totalAset',
        'totalStok',
        'totalKeluar',
        'totalDigunakan',
        'komposisiAset',
        'dipinjam',
        'dikembalikan',
        'totalMutasi',
        'perusahaanCount',
        'petugasCount',
  
        'bulanLabel',
        'dataMasuk',
        'dataKeluar',
        'perusahaanList'
      )
    );
  }
}

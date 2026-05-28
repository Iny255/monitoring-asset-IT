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
    $totalMasuk = Masuk::sum('jumlah');

    // TOTAL KELUAR
    $totalKeluar = Keluar::sum('jumlah');

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

    $managerCount = User::where('role', 'manager')->count();

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

    $perusahaanList = Perusahaan::with(['masuk', 'keluar', 'maping'])

      ->get()

      ->map(function ($p) {
        // TOTAL BARANG MASUK
        $asetMasuk = $p->masuk->sum('jumlah');

        // TOTAL BARANG KELUAR
        $asetKeluar = $p->keluar->sum('jumlah');

        // STOK TERSEDIA
        $stokTersedia = $asetMasuk - $asetKeluar;

        // TOTAL DIGUNAKAN
        $asetDigunakan = $p->maping->count();

        return (object) [
          'nama_perusahaan' => $p->nama_perusahaan,

          // TOTAL MASUK
          'aset_masuk' => $asetMasuk,

          // TOTAL KELUAR
          'aset_keluar' => $asetKeluar,

          // STOK TERSEDIA
          'stok_tersedia' => $stokTersedia,

          // TOTAL DIGUNAKAN
          'aset_digunakan' => $asetDigunakan,

          // TOTAL ASET
          'total_aset' => $asetMasuk,
        ];
      });

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
        'managerCount',
        'bulanLabel',
        'dataMasuk',
        'dataKeluar',
        'perusahaanList'
      )
    );
  }
}

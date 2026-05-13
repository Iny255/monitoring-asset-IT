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

    $totalStok = Masuk::sum('jumlah');

    $totalKeluar = Keluar::count();

    $totalDigunakan = Maping::count();

    $totalAset = $totalStok + $totalKeluar;

    /* =====================================
    | KOMPOSISI ASET DINAMIS
===================================== */

    $komposisiAset = Masuk::select('kategoris.nama_barang', DB::raw('SUM(masuks.jumlah) as total'))
      ->join('kategoris', 'masuks.id_kategori', '=', 'kategoris.id')
      ->groupBy('kategoris.nama_barang')
      ->orderByDesc('total')
      ->get();

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
        $asetMasuk = $p->masuk->sum('jumlah');

        $asetKeluar = $p->keluar->count();

        $asetDigunakan = $p->maping->count();

        return (object) [
          'nama_perusahaan' => $p->nama_perusahaan,

          'aset_masuk' => $asetMasuk,

          'aset_keluar' => $asetKeluar,

          'aset_digunakan' => $asetDigunakan,

          // TOTAL ASET
          'total_aset' => $asetMasuk + $asetKeluar,
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

<?php

namespace App\Http\Controllers\main_dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Masuk;
use App\Models\Keluar;
use App\Models\Peminjaman;
use App\Models\MutasiMaping;

class DashboardPetugasController extends Controller
{
  public function petugas()
  {
    $now = Carbon::now('Asia/Jakarta');
    $user = auth()->user();

    // 🔒 FILTER PERUSAHAAN
    $filter = function ($query) use ($user) {
      if ($user->role !== 'super_admin') {
        $query->where('id_perusahaan', $user->id_perusahaan);
      }
    };

    /* ================= TOTAL ================= */

    $totalStok = Masuk::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('perusahaan_id', $user->id_perusahaan);
    })->sum('jumlah');

    $totalKeluar = Keluar::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('id_perusahaan', $user->id_perusahaan);
    })->count();

    $totalAset = $totalStok + $totalKeluar;

    /* ================= KOMPOSISI ASET DINAMIS ================= */

    $komposisiAset = DB::table('masuks')
      ->join('kategoris', 'masuks.id_kategori', '=', 'kategoris.id')

      ->select('kategoris.nama_barang', DB::raw('SUM(masuks.jumlah) as total'));

    if ($user->role !== 'super_admin') {
      $komposisiAset->where('masuks.perusahaan_id', $user->id_perusahaan);
    }

    $komposisiAset = $komposisiAset
      ->groupBy('kategoris.nama_barang')
      ->orderByDesc('total')
      ->get();
    /* ================= PEMINJAMAN ================= */

    $dipinjam = Peminjaman::where('status', 'Dipinjam')->count(); // sudah aman karena pakai global scope
    $dikembalikan = Peminjaman::where('status', 'Dikembalikan')->count();

    /* ================= MUTASI ================= */

    $totalMutasi = MutasiMaping::whereHas('maping', function ($q) use ($user) {
      if ($user->role !== 'super_admin') {
        $q->where('id_perusahaan', $user->id_perusahaan);
      }
    })->count();

    /* ================= GRAFIK ================= */

    $mutasiMasuk = Masuk::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('perusahaan_id', $user->id_perusahaan);
    })
      ->selectRaw('MONTH(created_at) bulan, COUNT(*) total')
      ->groupBy('bulan')
      ->pluck('total', 'bulan')
      ->toArray();

    $mutasiKeluar = Keluar::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('id_perusahaan', $user->id_perusahaan);
    })
      ->selectRaw('MONTH(created_at) bulan, COUNT(*) total')
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

    return view(
      'content.dashboard.dashboard-petugas',
      compact(
        'now',
        'totalAset',
        'komposisiAset',
        'dipinjam',
        'dikembalikan',
        'bulanLabel',
        'dataMasuk',
        'dataKeluar',
        'totalMutasi'
      )
    );
  }
}

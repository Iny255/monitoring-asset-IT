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

    // TOTAL BARANG MASUK
    $totalMasuk = Masuk::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('perusahaan_id', $user->id_perusahaan);
    })->sum('jumlah');

    // TOTAL BARANG KELUAR
    $totalKeluar = Keluar::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('id_perusahaan', $user->id_perusahaan);
    })->sum('jumlah');

    // STOK TERSEDIA
    $totalStok = $totalMasuk - $totalKeluar;

    // TOTAL ASET
    $totalAset = $totalMasuk;

    /* ================= KOMPOSISI STOK REAL ================= */

    $komposisiAset = Masuk::with(['kategori', 'keluars'])

      ->when($user->role !== 'super_admin', function ($q) use ($user) {
        $q->where('perusahaan_id', $user->id_perusahaan);
      })

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
        'totalStok',
        'totalKeluar',
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

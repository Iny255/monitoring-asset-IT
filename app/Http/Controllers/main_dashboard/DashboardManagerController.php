<?php

namespace App\Http\Controllers\main_dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Masuk;
use App\Models\Keluar;
use App\Models\Peminjaman;
use App\Models\MutasiMaping;

class DashboardManagerController extends Controller
{
  public function index()
  {
    // ✅ GANTI
    $user = auth()->user();
    $now = Carbon::now('Asia/Jakarta');

    /* ================= TOTAL ================= */

    // TOTAL MASUK
    $totalMasuk = Masuk::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('perusahaan_id', $user->id_perusahaan);
    })->sum('jumlah');

    // TOTAL KELUAR
    $totalKeluar = Keluar::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('id_perusahaan', $user->id_perusahaan);
    })->sum('jumlah');

    // STOK REAL
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
    $dipinjam = Peminjaman::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where(function ($qq) use ($user) {
        $qq->where('perusahaan_id', $user->id_perusahaan)->orWhere('tipe_peminjam', 'external');
      });
    })
      ->where('status', 'Dipinjam')
      ->count();

    $dikembalikan = Peminjaman::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where(function ($qq) use ($user) {
        $qq->where('perusahaan_id', $user->id_perusahaan)->orWhere('tipe_peminjam', 'external');
      });
    })
      ->where('status', 'Dikembalikan')
      ->count();

    /* ================= MUTASI ================= */
    $totalMutasi = MutasiMaping::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->whereHas('maping', function ($m) use ($user) {
        $m->where('id_perusahaan', $user->id_perusahaan);
      });
    })->count();

    /* ================= CHART ================= */
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
      'content.dashboard.dashboard-manager',
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

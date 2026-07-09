<?php

namespace App\Http\Controllers\main_dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Masuk;
use App\Models\Keluar;
use App\Models\Peminjaman;
use App\Models\MutasiMaping;
use App\Models\Inventaris;

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

    $totalAset = Inventaris::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('perusahaan_id', $user->id_perusahaan);
    })->count();

    $totalStok = Inventaris::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('perusahaan_id', $user->id_perusahaan);
    })
      ->where('status', 'TERSEDIA')
      ->count();

    $totalKeluar = Inventaris::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('perusahaan_id', $user->id_perusahaan);
    })
      ->where('status', 'DIPAKAI')
      ->count();

    /* ================= KOMPOSISI STOK REAL ================= */

    $komposisiAset = Inventaris::with('dataAset.kategori')

      ->when($user->role !== 'super_admin', function ($q) use ($user) {
        $q->where('perusahaan_id', $user->id_perusahaan);
      })

      ->get()

      ->groupBy(function ($item) {
        return $item->dataAset->kategori->nama_barang ?? 'LAINNYA';
      })

      ->map(function ($items, $namaBarang) {
        return [
          'nama_barang' => $namaBarang,
          'total' => $items->where('status', 'TERSEDIA')->count(),
        ];
      })

      ->sortByDesc('total')

      ->values();
   

    /* ================= GRAFIK ================= */

    $mutasiKeluar = Keluar::when($user->role !== 'super_admin', function ($q) use ($user) {
      $q->where('perusahaan_id', $user->id_perusahaan);
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
       
        'bulanLabel',
        'dataMasuk',
        'dataKeluar',
        
      )
    );
  }
}

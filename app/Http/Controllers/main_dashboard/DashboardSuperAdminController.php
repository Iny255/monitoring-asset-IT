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
        | KATEGORI
        ===================================== */

        $totalLaptop = Masuk::whereHas('kategori', function ($q) {
            $q->where('nama_barang', 'Laptop');
        })->sum('jumlah');

        $totalPrinter = Masuk::whereHas('kategori', function ($q) {
            $q->where('nama_barang', 'Printer');
        })->sum('jumlah');

        $totalHp = Masuk::whereHas('kategori', function ($q) {
            $q->whereIn('nama_barang', [
                'HP',
                'Tablet',
                'HP/Tablet',
                'Tablet/HP'
            ]);
        })->sum('jumlah');

        /* =====================================
        | PEMINJAMAN
        ===================================== */

        $dipinjam = Peminjaman::where(
            'status',
            'Dipinjam'
        )->count();

        $dikembalikan = Peminjaman::where(
            'status',
            'Dikembalikan'
        )->count();

        /* =====================================
        | MUTASI
        ===================================== */

        $totalMutasi = MutasiMaping::count();

        /* =====================================
        | USER
        ===================================== */

        $perusahaanCount = Perusahaan::count();

        $petugasCount = User::where(
            'role',
            'petugas'
        )->count();

        $managerCount = User::where(
            'role',
            'manager'
        )->count();

        /* =====================================
        | GRAFIK
        ===================================== */

        $mutasiMasuk = Masuk::selectRaw(
            'MONTH(created_at) bulan, COUNT(*) total'
        )
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $mutasiKeluar = Keluar::selectRaw(
            'MONTH(created_at) bulan, COUNT(*) total'
        )
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

        $perusahaanList = Perusahaan::withCount([
            'masuk',
            'keluar'
        ])->get();

        return view(
            'content.dashboard.superadmin',
            compact(
                'now',
                'totalAset',
                'totalStok',
                'totalKeluar',
                'totalDigunakan',
                'totalLaptop',
                'totalPrinter',
                'totalHp',
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

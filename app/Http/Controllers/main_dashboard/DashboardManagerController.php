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
    public function index() // ✅ GANTI
    {

        $now = Carbon::now('Asia/Jakarta');

        // TOTAL STOK (SEMUA BARANG MASUK)
        $totalStok = Masuk::sum('jumlah');

        // TOTAL ASET KELUAR
        $totalKeluar = Keluar::count();

        // TOTAL ASET = STOK + KELUAR
        $totalAset = $totalStok + $totalKeluar;

        /* ================= ASET BERDASARKAN TYPE ================= */
        $totalLaptop = (int) Masuk::whereHas('kategori', function ($q) {
            $q->where('nama_barang', 'Laptop');
        })->sum('jumlah');


        $totalPrinter = (int) Masuk::whereHas('kategori', function ($q) {
            $q->where('nama_barang', 'Printer');
        })->sum('jumlah');


        $totalHp = (int) Masuk::whereHas('kategori', function ($q) {
            $q->whereIn('nama_barang', ['HP', 'Tablet', 'HP/Tablet', 'HP / Tablet']);
        })->sum('jumlah');

        /* ================= PEMINJAMAN ================= */
        $dipinjam      = Peminjaman::where('status', 'Dipinjam')->count();
        $dikembalikan  = Peminjaman::where('status', 'Dikembalikan')->count();

        $totalMutasi = MutasiMaping::count();

        /* ================= MUTASI PER BULAN ================= */
        $mutasiMasuk = Masuk::selectRaw('MONTH(created_at) bulan, COUNT(*) total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $mutasiKeluar = Keluar::selectRaw('MONTH(created_at) bulan, COUNT(*) total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $bulanLabel = [];
        $dataMasuk  = [];
        $dataKeluar = [];

        for ($i = 1; $i <= 12; $i++) {
            $bulanLabel[] = Carbon::create()->month($i)->translatedFormat('M');
            $dataMasuk[]  = $mutasiMasuk[$i] ?? 0;
            $dataKeluar[] = $mutasiKeluar[$i] ?? 0;
        }

        return view(
            'content.dashboard.dashboard-manager',
            compact(
                'now',
                'totalAset',
                'totalLaptop',
                'totalPrinter',
                'totalHp',
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

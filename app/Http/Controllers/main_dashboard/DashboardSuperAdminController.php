<?php

namespace App\Http\Controllers\main_dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Masuk;
use App\Models\Keluar;
use App\Models\Peminjaman;
use App\Models\MutasiMaping;
use App\Models\User;
use App\Models\Perusahaan;

class DashboardSuperAdminController extends Controller
{
    public function index(Request $request)
    {
        $perusahaanId = $request->get('perusahaan_id');
        $user = auth()->user();

        // Superadmin bisa lihat semua atau filter perusahaan
        $queryMasuk = Masuk::with('perusahaan');
        $queryKeluar = Keluar::with('perusahaan');
        $queryPeminjaman = Peminjaman::with(['keluar.perusahaan']);
        $queryMutasi = MutasiMaping::with(['maping.masuk.perusahaan']);
        $queryUser = User::with('perusahaan');

if ($perusahaanId && $perusahaanId !== 'all') {
            $queryMasuk->whereHas('perusahaan', fn($q) => $q->where('id', $perusahaanId));
            $queryKeluar->where('id_perusahaan', $perusahaanId);
            $queryPeminjaman->whereHas('keluar', fn($q) => $q->whereHas('perusahaan', fn($q2) => $q2->where('id', $perusahaanId)));
            $queryMutasi->whereHas('maping.keluar.masuk.perusahaan', fn($q) => $q->where('id', $perusahaanId));
            $queryUser->where('id_perusahaan', $perusahaanId);
        }

        $now = Carbon::now('Asia/Jakarta');

        $totalStok = $queryMasuk->sum('jumlah');
        $totalKeluar = $queryKeluar->count();
        $totalAset = $totalStok + $totalKeluar;

        $totalLaptop = (int) $queryMasuk->clone()
            ->whereHas('kategori', function ($q) {
                $q->where('nama_barang', 'Laptop');
            })->sum('jumlah');

        $totalPrinter = (int) $queryMasuk->clone()
            ->whereHas('kategori', function ($q) {
                $q->where('nama_barang', 'Printer');
            })->sum('jumlah');

        $totalHp = (int) $queryMasuk->clone()
            ->whereHas('kategori', function ($q) {
                $q->whereIn('nama_barang', ['HP', 'Tablet', 'HP/Tablet', 'Tablet/HP']);
            })->sum('jumlah');

        $dipinjam = $queryPeminjaman->where('status', 'Dipinjam')->count();
        $dikembalikan = $queryPeminjaman->where('status', 'Dikembalikan')->count();
        $totalMutasi = $queryMutasi->count();

        $perusahaanCount = Perusahaan::count();
        $petugasCount = $queryUser->where('role', 'petugas')->count();
        $managerCount = $queryUser->where('role', 'manager')->count();

        $mutasiMasuk = $queryMasuk->clone()
            ->selectRaw('MONTH(created_at) bulan, COUNT(*) total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $mutasiKeluar = $queryKeluar->clone()
            ->selectRaw('MONTH(created_at) bulan, COUNT(*) total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $bulanLabel = [];
        $dataMasuk = [];
        $dataKeluar = [];

        for ($i = 1; $i <= 12; $i++) {
            $bulanLabel[] = Carbon::create()->month($i)->translatedFormat('M');
            $dataMasuk[] = $mutasiMasuk[$i] ?? 0;
            $dataKeluar[] = $mutasiKeluar[$i] ?? 0;
        }

        $perusahaanList = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('content.dashboard.superadmin', compact(
            'now',
            'totalAset',
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
            'perusahaanId',
            'perusahaanList'
        ));
    }
}


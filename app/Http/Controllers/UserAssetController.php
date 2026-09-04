<?php

namespace App\Http\Controllers;

use App\Models\Maping;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAssetController extends Controller
{
    /**
     * Display a listing of assets assigned to the logged-in employee/user.
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $mapings = collect();
        $peminjamans = collect();

        if ($user->karyawan_id) {
            // Ambil data mapping aset yang dialokasikan untuk karyawan ini
            $mapings = Maping::with([
                'keluar.inventaris.dataAset.kategori',
                'lokasi',
                'perusahaan'
            ])
            ->where('karyawan_id', $user->karyawan_id)
            ->where('status', '!=', 'Batal')
            ->orderBy('id', 'desc')
            ->get();

            // Ambil data peminjaman aset sementara
            $peminjamans = Peminjaman::with([
                'inventaris.dataAset.kategori',
                'perusahaanTujuan'
            ])
            ->where(function ($q) use ($user) {
                $q->where('karyawan_id', $user->karyawan_id)
                  ->orWhere('karyawan_tujuan_id', $user->karyawan_id);
            })
            ->orderBy('id', 'desc')
            ->get();
        }

        // Kalkulasi Matriks Kategori Aset Pegawai
        $categoryMetrics = $this->calculateCategoryMetrics($mapings, $peminjamans);

        return view('content.dashboard.user_asset.index', array_merge([
            'user' => $user,
            'karyawan' => $karyawan,
            'mapings' => $mapings,
            'peminjamans' => $peminjamans,
        ], $categoryMetrics));
    }

    /**
     * Menghitung rincian dan matriks aset per kategori untuk karyawan
     */
    private function calculateCategoryMetrics($mapings, $peminjamans)
    {
        $activeLoans = $peminjamans->where('status', 'dipinjam');
        $totalAset = $mapings->count() + $activeLoans->count();

        $kategoriMap = [];
        $totalLaptop = 0;
        $totalPrinter = 0;
        $totalHp = 0;
        $totalPc = 0;
        $totalLainnya = 0;

        // 1. Proses Aset Mapping (Perangkat Tetap)
        foreach ($mapings as $maping) {
            $catName = $maping->keluar?->inventaris?->dataAset?->kategori?->nama_barang 
                    ?? $maping->keluar?->inventaris?->kategori?->nama_barang 
                    ?? 'Lainnya';
            $catName = trim($catName);
            if ($catName === '') {
                $catName = 'Lainnya';
            }

            $key = strtolower($catName);
            if (!isset($kategoriMap[$key])) {
                $kategoriMap[$key] = [
                    'nama' => $catName,
                    'key' => $key,
                    'total' => 0,
                    'mapping_count' => 0,
                    'pinjam_count' => 0,
                    'icon' => $this->getCategoryIcon($catName),
                    'color' => $this->getCategoryColor($catName),
                    'bg_class' => $this->getCategoryBgClass($catName),
                ];
            }
            $kategoriMap[$key]['total']++;
            $kategoriMap[$key]['mapping_count']++;

            // Hitung metrik kategori utama
            $this->incrementSummaryCounts($key, $totalLaptop, $totalPrinter, $totalHp, $totalPc, $totalLainnya);
        }

        // 2. Proses Peminjaman Aktif (Perangkat Sementara)
        foreach ($activeLoans as $pinjam) {
            $catName = $pinjam->inventaris?->dataAset?->kategori?->nama_barang 
                    ?? $pinjam->inventaris?->kategori?->nama_barang 
                    ?? 'Lainnya';
            $catName = trim($catName);
            if ($catName === '') {
                $catName = 'Lainnya';
            }

            $key = strtolower($catName);
            if (!isset($kategoriMap[$key])) {
                $kategoriMap[$key] = [
                    'nama' => $catName,
                    'key' => $key,
                    'total' => 0,
                    'mapping_count' => 0,
                    'pinjam_count' => 0,
                    'icon' => $this->getCategoryIcon($catName),
                    'color' => $this->getCategoryColor($catName),
                    'bg_class' => $this->getCategoryBgClass($catName),
                ];
            }
            $kategoriMap[$key]['total']++;
            $kategoriMap[$key]['pinjam_count']++;

            // Hitung metrik kategori utama
            $this->incrementSummaryCounts($key, $totalLaptop, $totalPrinter, $totalHp, $totalPc, $totalLainnya);
        }

        // Hitung persentase untuk setiap kategori
        $kategoriBreakdown = collect($kategoriMap)->map(function ($item) use ($totalAset) {
            $item['persentase'] = $totalAset > 0 ? round(($item['total'] / $totalAset) * 100, 1) : 0;
            return (object) $item;
        })->sortByDesc('total')->values();

        return [
            'totalAset' => $totalAset,
            'totalLaptop' => $totalLaptop,
            'totalPrinter' => $totalPrinter,
            'totalHp' => $totalHp,
            'totalPc' => $totalPc,
            'totalLainnya' => $totalLainnya,
            'kategoriBreakdown' => $kategoriBreakdown,
        ];
    }

    /**
     * Mengelompokkan kategori ke total kategori utama
     */
    private function incrementSummaryCounts($key, &$laptop, &$printer, &$hp, &$pc, &$lainnya)
    {
        if (str_contains($key, 'laptop') || str_contains($key, 'notebook') || str_contains($key, 'macbook')) {
            $laptop++;
        } elseif (str_contains($key, 'printer') || str_contains($key, 'scanner') || str_contains($key, 'cetak')) {
            $printer++;
        } elseif (str_contains($key, 'hp') || str_contains($key, 'handphone') || str_contains($key, 'smartphone') || str_contains($key, 'phone') || str_contains($key, 'ponsel') || str_contains($key, 'tablet') || str_contains($key, 'ipad')) {
            $hp++;
        } elseif (str_contains($key, 'pc') || str_contains($key, 'komputer') || str_contains($key, 'desktop') || str_contains($key, 'all in one') || str_contains($key, 'aio')) {
            $pc++;
        } else {
            $lainnya++;
        }
    }

    /**
     * Helper icon berdasarkan nama kategori
     */
    private function getCategoryIcon($catName)
    {
        $k = strtolower($catName);
        if (str_contains($k, 'laptop') || str_contains($k, 'notebook') || str_contains($k, 'macbook')) {
            return 'bi-laptop';
        }
        if (str_contains($k, 'printer') || str_contains($k, 'scanner') || str_contains($k, 'cetak')) {
            return 'bi-printer';
        }
        if (str_contains($k, 'hp') || str_contains($k, 'handphone') || str_contains($k, 'smartphone') || str_contains($k, 'phone') || str_contains($k, 'ponsel') || str_contains($k, 'tablet') || str_contains($k, 'ipad')) {
            return 'bi-phone';
        }
        if (str_contains($k, 'pc') || str_contains($k, 'komputer') || str_contains($k, 'desktop') || str_contains($k, 'aio')) {
            return 'bi-pc-display';
        }
        if (str_contains($k, 'monitor') || str_contains($k, 'display') || str_contains($k, 'screen') || str_contains($k, 'layar') || str_contains($k, 'tv')) {
            return 'bi-display';
        }
        if (str_contains($k, 'network') || str_contains($k, 'jaringan') || str_contains($k, 'router') || str_contains($k, 'switch') || str_contains($k, 'modem') || str_contains($k, 'access point') || str_contains($k, 'wifi')) {
            return 'bi-hdd-network';
        }
        if (str_contains($k, 'keyboard') || str_contains($k, 'mouse') || str_contains($k, 'headset') || str_contains($k, 'headphone') || str_contains($k, 'aksesoris') || str_contains($k, 'audio') || str_contains($k, 'speaker')) {
            return 'bi-headphones';
        }
        return 'bi-box-seam';
    }

    /**
     * Helper warna theme berdasarkan nama kategori
     */
    private function getCategoryColor($catName)
    {
        $k = strtolower($catName);
        if (str_contains($k, 'laptop') || str_contains($k, 'notebook') || str_contains($k, 'macbook')) {
            return 'primary';
        }
        if (str_contains($k, 'printer') || str_contains($k, 'scanner') || str_contains($k, 'cetak')) {
            return 'warning';
        }
        if (str_contains($k, 'hp') || str_contains($k, 'handphone') || str_contains($k, 'smartphone') || str_contains($k, 'phone') || str_contains($k, 'ponsel') || str_contains($k, 'tablet') || str_contains($k, 'ipad')) {
            return 'success';
        }
        if (str_contains($k, 'pc') || str_contains($k, 'komputer') || str_contains($k, 'desktop') || str_contains($k, 'aio')) {
            return 'info';
        }
        if (str_contains($k, 'monitor') || str_contains($k, 'display') || str_contains($k, 'screen') || str_contains($k, 'layar')) {
            return 'primary';
        }
        if (str_contains($k, 'network') || str_contains($k, 'jaringan') || str_contains($k, 'router') || str_contains($k, 'switch')) {
            return 'danger';
        }
        return 'secondary';
    }

    /**
     * Helper background class badge
     */
    private function getCategoryBgClass($catName)
    {
        return 'bg-label-' . $this->getCategoryColor($catName);
    }
}

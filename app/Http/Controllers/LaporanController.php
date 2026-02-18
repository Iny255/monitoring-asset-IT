<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Masuk;
use App\Models\Keluar;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{

    public function stok(Request $request)
    {
        $query = \App\Models\Masuk::with('kategori');

        if ($request->search) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%');
            });
        }

        $stoks = $query->get()->groupBy(function ($item) {
            return $item->kategori->nama_barang . '|' . $item->type . '|' . $item->merek;
        })->map(function ($items) {
            return (object)[
                'kategori' => $items->first()->kategori,
                'type' => $items->first()->type,
                'merek' => $items->first()->merek,
                'stok' => $items->sum('jumlah')
            ];
        })->values();

        return view('content.dashboard.transaksi-masuk.stok', compact('stoks'));
    }
    public function cetakStok(Request $request)
    {
        $stoks = Masuk::with('kategori')
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('kategori', function ($q) use ($request) {
                    $q->where('nama_barang', 'like', '%' . $request->search . '%');
                });
            })
            ->select(
                'id_kategori',
                'type',
                'merek',
                DB::raw('SUM(jumlah) as stok')
            )
            ->groupBy('id_kategori', 'type', 'merek')
            ->orderBy('stok', 'desc')
            ->get();

        return view('content.manager.laporan.cetak-stok', compact('stoks'));
    }

    public function laporanMasuk(Request $request)
    {
        $query = Masuk::with('kategori')->latest();

        // SEARCH
        if ($request->search) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%');
            });
        }

        $masuks = $query->paginate(7)->appends($request->query());

        return view('content.dashboard.transaksi-masuk.index', compact('masuks'));
    }

    public function show($id)
    {
        $masuk = Masuk::with('kategori')->findOrFail($id);

        return view('content.dashboard.transaksi-masuk.show', compact('masuk'));
    }

    public function laporanKeluar(Request $request)
    {
        $query = Keluar::with([
            'masuk.kategori',
            'karyawan'
        ]);

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // ===== KOLOM DI TABEL KELUAR =====
                $q->where('kode_keluar', 'like', "%$search%")
                    ->orWhere('kode_barang', 'like', "%$search%")
                    ->orWhere('warna', 'like', "%$search%");

                // ===== RELASI MASUK =====
                $q->orWhereHas('masuk', function ($m) use ($search) {
                    $m->where('kode_masuk', 'like', "%$search%")
                        ->orWhere('type', 'like', "%$search%")
                        ->orWhere('merek', 'like', "%$search%");
                });

                // ===== RELASI KATEGORI =====
                $q->orWhereHas('masuk.kategori', function ($k) use ($search) {
                    $k->where('nama_barang', 'like', "%$search%");
                });

                // ===== RELASI KARYAWAN =====
                $q->orWhereHas('karyawan', function ($k) use ($search) {
                    $k->where('nama_karyawan', 'like', "%$search%")
                        ->orWhere('divisi', 'like', "%$search%")
                        ->orWhere('perusahaan', 'like', "%$search%");
                });
            });
        }

        $keluars = $query->orderByDesc('id')
            ->paginate(7)
            ->appends($request->query());

        return view('content.dashboard.transaksi-keluar.index', compact('keluars'));
    }

    public function showKeluar($id)
    {
        $keluar = Keluar::with([
            'masuk.kategori',
            'karyawan'
        ])->findOrFail($id);

        return view('content.dashboard.transaksi-keluar.show', compact('keluar'));
    }

    public function laporanPeminjaman(Request $request)
    {
        $query = Peminjaman::with([
            'karyawan',
            'kategori',
            'keluar.masuk.kategori',
            'perusahaan',
            'lokasi'
        ]);

        // ================= SEARCH =================
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // Cari KODE BARANG (dari tabel keluar)
                $q->whereHas('keluar', function ($k) use ($search) {
                    $k->where('kode_barang', 'like', "%{$search}%");
                })

                    // Cari NAMA BARANG (dari kategori lewat masuk)
                    ->orWhereHas('keluar.masuk.kategori', function ($k) use ($search) {
                        $k->where('nama_barang', 'like', "%{$search}%");
                    })

                    // Cari NAMA KARYAWAN
                    ->orWhereHas('karyawan', function ($k) use ($search) {
                        $k->where('nama_karyawan', 'like', "%{$search}%");
                    });
            });
        }

        $peminjamans = $query->latest()->paginate(7)->appends($request->query());

        return view('content.dashboard.peminjaman.index', compact('peminjamans'));
    }

     public function showPeminjaman($id)
    {
         $peminjaman = Peminjaman::with([
            'karyawan',
            'kategori',
            'keluar.masuk.kategori',
            'perusahaan',
            'lokasi'
        ])->findOrFail($id);

        return view('content.dashboard.peminjaman.show', compact('peminjaman'));
    }
}

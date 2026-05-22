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
    $query = Masuk::with(['kategori', 'perusahaan', 'keluars.karyawan']);

    // FILTER ROLE
    if (auth()->user()->role !== 'super_admin') {
      $query->where('perusahaan_id', auth()->user()->id_perusahaan);
    }

    // FILTER PERUSAHAAN SUPER ADMIN
    if (auth()->user()->role == 'super_admin' && $request->perusahaan_id) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    // SEARCH
    if ($request->search) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('type', 'like', "%$search%")
          ->orWhere('merek', 'like', "%$search%")
          ->orWhereHas('kategori', function ($k) use ($search) {
            $k->where('nama_barang', 'like', "%$search%");
          });
      });
    }

    // GROUPING
    $stoks = $query
      ->latest()
      ->get()
      ->groupBy(function ($item) {
        return $item->id_kategori . '-' . $item->type . '-' . $item->merek;
      });

    // SUPER ADMIN
    $perusahaans = auth()->user()->role == 'super_admin' ? \App\Models\Perusahaan::all() : collect();

    return view('content.dashboard.transaksi-masuk.stok', compact('stoks', 'perusahaans'));
  }
  public function historyStok(int $id)
  {
   $user = auth()->user();

    // DATA UTAMA
    $first = Masuk::findOrFail($id);

    // QUERY GROUP
    $query = Masuk::with([
        'kategori',
        'perusahaan',
        'keluars.karyawan'
    ])
        ->where('id_kategori', $first->id_kategori)
        ->where('type', $first->type)
        ->where('merek', $first->merek);

    // FILTER PERUSAHAAN
    if ($user->role !== 'super_admin') {

        $query->where(
            'perusahaan_id',
            $user->id_perusahaan
        );
    }

    // AMBIL GROUP
    $stokGroup = $query->get();

    // SUMMARY
    $stokAwal = $stokGroup->sum('jumlah');

    $totalKeluar = $stokGroup->sum(function ($item) {

        return $item->keluars->sum('jumlah');
    });

    $sisa = $stokAwal - $totalKeluar;

    return view(
        'content.dashboard.transaksi-masuk.history-stok',
        compact(
            'stokGroup',
            'stokAwal',
            'totalKeluar',
            'sisa'
        )
    );
  }
  public function cetakStok(Request $request)
  {
    $stoks = Masuk::with('kategori')
      ->when(auth()->user()->role !== 'super_admin', function ($query) {
        $query->where('perusahaan_id', auth()->user()->id_perusahaan);
      })
      ->when($request->search, function ($query) use ($request) {
        $query->whereHas('kategori', function ($q) use ($request) {
          $q->where('nama_barang', 'like', '%' . $request->search . '%');
        });
      })
      ->select('id_kategori', 'type', 'merek', DB::raw('SUM(jumlah) as stok'))
      ->groupBy('id_kategori', 'type', 'merek')
      ->orderBy('stok', 'desc')
      ->get();

  
  }

  public function laporanMasuk(Request $request)
  {
    // SEARCH
    $query = Masuk::with('kategori')->latest();

    if (auth()->user()->role !== 'super_admin') {
      $query->where('perusahaan_id', auth()->user()->id_perusahaan);
    }

    $masuks = $query->paginate(5)->appends($request->query());

    return view('content.dashboard.transaksi-masuk.index', compact('masuks'));
  }

  public function show(int $id)
  {
    $query = Masuk::with('kategori');

    // 🔥 FILTER PERUSAHAAN
    if (auth()->user()->role !== 'super_admin') {
      $query->where('perusahaan_id', auth()->user()->id_perusahaan);
    }

    $masuk = $query->findOrFail($id);

    return view('content.dashboard.transaksi-masuk.show', compact('masuk'));
  }

  public function laporanKeluar(Request $request)
  {
    $query = Keluar::with(['masuk.kategori', 'karyawan']);
    if (auth()->user()->role !== 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }
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

    $keluars = $query
      ->orderByDesc('id')
      ->paginate(5)
      ->appends($request->query());

    return view('content.dashboard.transaksi-keluar.index', compact('keluars'));
  }

  public function showKeluar(int $id)
  {
    $query = Keluar::with(['masuk.kategori', 'karyawan']);

    // 🔥 FILTER PERUSAHAAN
    if (auth()->user()->role !== 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $keluar = $query->findOrFail($id);

    return view('content.dashboard.transaksi-keluar.show', compact('keluar'));
  }

  public function laporanPeminjaman(Request $request)
  {
    $query = Peminjaman::with(['karyawan', 'kategori', 'keluar.masuk.kategori', 'perusahaan', 'lokasi']);

    // 🔥 FILTER PERUSAHAAN
    if (auth()->user()->role !== 'super_admin') {
      $query->where(function ($q) {
        $q->where('perusahaan_id', auth()->user()->id_perusahaan)->orWhere('tipe_peminjam', 'external'); // 🔥 biar external ikut tampil
      });
    }
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

    $peminjamans = $query
      ->latest()
      ->paginate(5)
      ->appends($request->query());

    return view('content.dashboard.peminjaman.index', compact('peminjamans'));
  }

  public function showPeminjaman(int $id)
  {
    $query = Peminjaman::with(['karyawan', 'kategori', 'keluar.masuk.kategori', 'perusahaan', 'lokasi']);

    // 🔥 FILTER PERUSAHAAN
    if (auth()->user()->role !== 'super_admin') {
      $query->where(function ($q) {
        $q->where('perusahaan_id', auth()->user()->id_perusahaan)->orWhere('tipe_peminjam', 'external'); // 🔥 ini kunci fix
      });
    }

    $peminjaman = $query->findOrFail($id);

    return view('content.dashboard.peminjaman.show', compact('peminjaman'));
  }
}

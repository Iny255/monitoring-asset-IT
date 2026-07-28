<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\HistoryHakAkses;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryHakAksesController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = HistoryHakAkses::with([
      'maping.keluar.karyawan',
      'maping.keluar.inventaris.dataAset.kategori',
      'maping.lokasi',
      'maping.perusahaan',
      'access',
      'user',
    ]);

    /*
        |--------------------------------------------------------------------------
        | FILTER PERUSAHAAN
        |--------------------------------------------------------------------------
        */

    if ($user->role != 'super_admin') {
      $query->whereHas('maping', function ($q) use ($user) {
        $q->where('id_perusahaan', $user->id_perusahaan);
      });
    } elseif ($request->filled('perusahaan_id')) {
      $query->whereHas('maping', function ($q) use ($request) {
        $q->where('id_perusahaan', $request->perusahaan_id);
      });
    }

    /*
        |--------------------------------------------------------------------------
        | FILTER USER ASSET
        |--------------------------------------------------------------------------
        */

    if ($request->filled('karyawan_id')) {
      $query->whereHas('maping.keluar', function ($q) use ($request) {
        $q->where('karyawan_id', $request->karyawan_id);
      });
    }
    //filter jenis
    if ($request->filled('jenis')) {
      $query->whereHas('access', function ($q) use ($request) {
        $q->where('jenis', $request->jenis);
      });
    }

    /*
        |--------------------------------------------------------------------------
        | FILTER HAK AKSES
        |--------------------------------------------------------------------------
        */

    if ($request->filled('access_id')) {
      $query->where('access_id', $request->access_id);
    }

    /*
        |--------------------------------------------------------------------------
        | FILTER AKSI
        |--------------------------------------------------------------------------
        */

    if ($request->filled('aksi')) {
      $query->where('aksi', $request->aksi);
    }

    /*
        |--------------------------------------------------------------------------
        | FILTER TANGGAL
        |--------------------------------------------------------------------------
        */

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
      $query->whereBetween('created_at', [$request->tanggal_awal, $request->tanggal_akhir]);
    }

    /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

    if ($request->filled('search')) {
      $search = trim($request->search);

      $query->where(function ($q) use ($search) {
        $q->whereHas('access', function ($a) use ($search) {
          $a->where('nama_akses', 'like', "%{$search}%");
        })

          ->orWhereHas('maping.keluar.karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('maping.keluar.inventaris', function ($i) use ($search) {
            $i->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
          });
      });
    }

    /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

    $histories = $query
      ->latest()
      ->paginate(10)
      ->appends(request()->query());

    /*
        |--------------------------------------------------------------------------
        | DROPDOWN
        |--------------------------------------------------------------------------
        */

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    $selectedPerusahaanId = $user->role == 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;

    $karyawans = Karyawan::query()
      ->when($selectedPerusahaanId, fn($q) => $q->where('id_perusahaan', $selectedPerusahaanId))
      ->orderBy('nama_karyawan')
      ->get();

    $accesses = Access::where('status', 'aktif')
      ->when($selectedPerusahaanId, fn($q) => $q->where('id_perusahaan', $selectedPerusahaanId))
      ->when($request->filled('jenis'), fn($q) => $q->where('jenis', $request->jenis))
      ->orderBy('nama_akses')
      ->get();

    return view('content.dashboard.history.hak-akses', compact('histories', 'perusahaans', 'karyawans', 'accesses'));
  }
  public function cetak(Request $request)
  {
    $user = auth()->user();

    $query = HistoryHakAkses::with([
      'maping.keluar.inventaris',
      'maping.keluar.karyawan',
      'access',
      'user',
      'maping.perusahaan',
      'maping.lokasi',
    ]);

    /*
    |--------------------------------------------------------------------------
    | PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    $perusahaanId = $request->get('perusahaan_id', $request->get('perusahaan'));
    if ($user->role != 'super_admin') {
      $query->whereHas('maping', function ($q) use ($user) {
        $q->where('id_perusahaan', $user->id_perusahaan);
      });
    } elseif (!empty($perusahaanId)) {
      $query->whereHas('maping', function ($q) use ($perusahaanId) {
        $q->where('id_perusahaan', $perusahaanId);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    if ($request->filled('user')) {
      $keyword = $request->user;

      $query->whereHas('maping.keluar.karyawan', function ($q) use ($keyword) {
        $q->where('nama', 'like', "%{$keyword}%");
      });
    }

    /*
    |--------------------------------------------------------------------------
    | JENIS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('jenis')) {
      $query->whereHas('access', function ($q) use ($request) {
        $q->where('jenis', $request->jenis);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | HAK AKSES
    |--------------------------------------------------------------------------
    */

    if ($request->filled('access_id')) {
      $query->where('access_id', $request->access_id);
    }

    /*
    |--------------------------------------------------------------------------
    | AKTIVITAS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('aksi')) {
      $query->where('aksi', $request->aksi);
    }

    /*
    |--------------------------------------------------------------------------
    | TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_mulai')) {
      $query->whereDate('created_at', '>=', $request->tanggal_mulai);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('created_at', '<=', $request->tanggal_akhir);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->whereHas('access', function ($qq) use ($search) {
          $qq->where('nama_akses', 'like', "%{$search}%");
        })->orWhereHas('maping.keluar.inventaris', function ($qq) use ($search) {
          $qq->where('no_inventaris', 'like', "%{$search}%");
        });
      });
    }

    $histories = $query->latest()->get();

   return view(
    'content.dashboard.history.cetak-history-hakakses',
    compact('histories', 'user')
);
  }

  public function exportExcel(Request $request)
  {
    $user = auth()->user();

    $query = HistoryHakAkses::with([
      'maping.keluar.inventaris.dataAset.kategori',
      'maping.keluar.karyawan',
      'access',
      'user',
      'maping.perusahaan',
      'maping.lokasi',
    ]);

    $perusahaanId = $request->get('perusahaan_id', $request->get('perusahaan'));
    if ($user->role != 'super_admin') {
      $query->whereHas('maping', function ($q) use ($user) {
        $q->where('id_perusahaan', $user->id_perusahaan);
      });
    } elseif (!empty($perusahaanId)) {
      $query->whereHas('maping', function ($q) use ($perusahaanId) {
        $q->where('id_perusahaan', $perusahaanId);
      });
    }

    if ($request->filled('karyawan_id')) {
      $query->whereHas('maping.keluar', function ($q) use ($request) {
        $q->where('karyawan_id', $request->karyawan_id);
      });
    }

    if ($request->filled('jenis')) {
      $query->whereHas('access', function ($q) use ($request) {
        $q->where('jenis', $request->jenis);
      });
    }

    if ($request->filled('access_id')) {
      $query->where('access_id', $request->access_id);
    }

    if ($request->filled('aksi')) {
      $query->where('aksi', $request->aksi);
    }

    $tanggalAwal = $request->get('tanggal_awal', $request->get('tanggal_mulai'));
    if ($tanggalAwal && $request->filled('tanggal_akhir')) {
      $query->whereBetween('created_at', [$tanggalAwal, $request->tanggal_akhir]);
    } elseif ($tanggalAwal) {
      $query->whereDate('created_at', '>=', $tanggalAwal);
    } elseif ($request->filled('tanggal_akhir')) {
      $query->whereDate('created_at', '<=', $request->tanggal_akhir);
    }

    if ($request->filled('search')) {
      $search = trim($request->search);

      $query->where(function ($q) use ($search) {
        $q->whereHas('access', function ($qq) use ($search) {
          $qq->where('nama_akses', 'like', "%{$search}%");
        })->orWhereHas('maping.keluar.inventaris', function ($qq) use ($search) {
          $qq->where('no_inventaris', 'like', "%{$search}%")->orWhere('kode_aset', 'like', "%{$search}%");
        })->orWhereHas('maping.keluar.karyawan', function ($qq) use ($search) {
          $qq->where('nama_karyawan', 'like', "%{$search}%");
        });
      });
    }

    $histories = $query->latest()->get();

    return \Maatwebsite\Excel\Facades\Excel::download(
      new \App\Exports\HistoryHakAksesExport($histories),
      'History_Hak_Akses.xlsx'
    );
  }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Perusahaan;

class HistoryMaintenanceController extends Controller
{
  public function index(Request $request)
  {
    $query = Maintenance::with(['inventaris.dataAset', 'inventaris.perusahaan', 'creator']);

    /*
    |--------------------------------------------------------------------------
    | FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $query->whereHas('inventaris', function ($q) use ($request) {
          $q->where('perusahaan_id', $request->perusahaan_id);
        });
      }
    } else {
      $query->whereHas('inventaris', function ($q) {
        $q->where('perusahaan_id', auth()->user()->id_perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER JENIS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('jenis')) {
      $query->where('jenis', $request->jenis);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER STATUS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER ASAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('asal')) {
      $query->where('asal', $request->asal);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_service', 'like', "%{$search}%")

          ->orWhere('vendor', 'like', "%{$search}%")

          ->orWhereHas('inventaris', function ($qq) use ($search) {
            $qq
              ->where('kode_aset', 'like', "%{$search}%")

              ->orWhere('no_inventaris', 'like', "%{$search}%")

              ->orWhereHas('dataAset', function ($q3) use ($search) {
                $q3->where('nama_barang', 'like', "%{$search}%");
              });
          });
      });
    }

    $laporan = $query
      ->latest()
      ->paginate(10)
      ->appends($request->query());

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    return view('content.dashboard.history-maintenance.index', compact('laporan', 'perusahaans'));
  }
  public function show(Maintenance $maintenance)
  {
    $maintenance->load([
      'inventaris.dataAset.kategori',
      'inventaris.perusahaan',
      'creator',
      'maping.karyawan',
      'maping.lokasi',
      'peminjaman.karyawan',
      'peminjaman.perusahaanTujuan',
      'peminjaman.karyawanTujuan',
    ]);

    return view('content.dashboard.maintenance.show', [
      'maintenance' => $maintenance,
      'backRoute' => route('history.maintenance.index'),
    ]);
  }
  public function cetak(Request $request)
  {
    $query = Maintenance::with(['inventaris.dataAset', 'inventaris.perusahaan', 'creator']);

    /*
    |--------------------------------------------------------------------------
    | FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $query->whereHas('inventaris', function ($q) use ($request) {
          $q->where('perusahaan_id', $request->perusahaan_id);
        });
      }
    } else {
      $query->whereHas('inventaris', function ($q) {
        $q->where('perusahaan_id', auth()->user()->id_perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER JENIS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('jenis')) {
      $query->where('jenis', $request->jenis);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER STATUS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER ASAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('asal')) {
      $query->where('asal', $request->asal);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_service', 'like', "%{$search}%")

          ->orWhere('vendor', 'like', "%{$search}%")

          ->orWhereHas('inventaris', function ($qq) use ($search) {
            $qq
              ->where('kode_aset', 'like', "%{$search}%")

              ->orWhere('no_inventaris', 'like', "%{$search}%")

              ->orWhereHas('dataAset', function ($q3) use ($search) {
                $q3->where('nama_barang', 'like', "%{$search}%");
              });
          });
      });
    }

    $laporan = $query->latest()->get();

    return view('content.dashboard.history-maintenance.cetak', compact('laporan'));
  }

  public function exportExcel(Request $request)
  {
    $query = Maintenance::with(['inventaris.dataAset', 'inventaris.perusahaan', 'creator']);

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $query->whereHas('inventaris', function ($q) use ($request) {
          $q->where('perusahaan_id', $request->perusahaan_id);
        });
      }
    } else {
      $query->whereHas('inventaris', function ($q) {
        $q->where('perusahaan_id', auth()->user()->id_perusahaan);
      });
    }

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
    }

    if ($request->filled('jenis')) {
      $query->where('jenis', $request->jenis);
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('asal')) {
      $query->where('asal', $request->asal);
    }

    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('kode_service', 'like', "%{$search}%")
          ->orWhere('vendor', 'like', "%{$search}%")
          ->orWhereHas('inventaris', function ($qq) use ($search) {
            $qq->where('kode_aset', 'like', "%{$search}%")
              ->orWhere('no_inventaris', 'like', "%{$search}%")
              ->orWhereHas('dataAset', function ($q3) use ($search) {
                $q3->where('nama_barang', 'like', "%{$search}%");
              });
          });
      });
    }

    $laporan = $query->latest()->get();

    return \Maatwebsite\Excel\Facades\Excel::download(
      new \App\Exports\MaintenanceExport($laporan, 'History Servis & Maintenance'),
      'History_Servis_Maintenance.xlsx'
    );
  }
}

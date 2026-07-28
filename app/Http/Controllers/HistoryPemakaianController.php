<?php

namespace App\Http\Controllers;

use App\Models\Keluar;
use App\Models\Perusahaan;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Exports\KeluarExport;
use Maatwebsite\Excel\Facades\Excel;

class HistoryPemakaianController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Keluar::with([
      'inventaris.dataAset.kategori',
      'karyawan',
      'perusahaan',
      'lokasi',
      'user',
      'maping',
    ]);

    // FILTER PERUSAHAAN
    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    // FILTER TANGGAL
    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tgl_keluar', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tgl_keluar', '<=', $request->tanggal_akhir);
    }

    // FILTER KATEGORI
    if ($request->filled('kategori_id')) {
      $query->whereHas('inventaris.dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->kategori_id);
      });
    }

    // FILTER LOKASI
    if ($request->filled('lokasi_id')) {
      $query->where('lokasi_id', $request->lokasi_id);
    }

    // FILTER USER ASET / KARYAWAN
    if ($request->filled('karyawan_id')) {
      $query->where('karyawan_id', $request->karyawan_id);
    }

    // SEARCH
    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->whereHas('inventaris', function ($qq) use ($search) {
          $qq->where('no_inventaris', 'like', "%{$search}%")
            ->orWhere('kode_aset', 'like', "%{$search}%");
        })
        ->orWhereHas('inventaris.dataAset.kategori', function ($qq) use ($search) {
          $qq->where('nama_barang', 'like', "%{$search}%");
        })
        ->orWhereHas('karyawan', function ($qq) use ($search) {
          $qq->where('nama_karyawan', 'like', "%{$search}%");
        })
        ->orWhere('divisi_klr', 'like', "%{$search}%");
      });
    }

    $histories = $query->latest('tgl_keluar')->paginate(15)->withQueryString();

    // Data dropdown untuk Filter
    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    
    $kategorisQuery = Kategori::orderBy('nama_barang');
    if ($user->role != 'super_admin') {
      $kategorisQuery->where('id_perusahaan', $user->id_perusahaan);
    }
    $kategoris = $kategorisQuery->get();

    $lokasisQuery = Lokasi::orderBy('nama_lokasi');
    if ($user->role != 'super_admin') {
      $lokasisQuery->where('id_perusahaan', $user->id_perusahaan);
    }
    $lokasis = $lokasisQuery->get();

    $karyawansQuery = Karyawan::orderBy('nama_karyawan');
    if ($user->role != 'super_admin') {
      $karyawansQuery->where('id_perusahaan', $user->id_perusahaan);
    }
    $karyawans = $karyawansQuery->get();

    return view('content.dashboard.history.pemakaian', compact(
      'histories',
      'perusahaans',
      'kategoris',
      'lokasis',
      'karyawans',
      'user'
    ));
  }

  public function cetak(Request $request)
  {
    $user = auth()->user();

    $query = Keluar::with([
      'inventaris.dataAset.kategori',
      'karyawan',
      'perusahaan',
      'lokasi',
      'user',
      'maping',
    ]);

    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tgl_keluar', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tgl_keluar', '<=', $request->tanggal_akhir);
    }

    if ($request->filled('kategori_id')) {
      $query->whereHas('inventaris.dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->kategori_id);
      });
    }

    if ($request->filled('lokasi_id')) {
      $query->where('lokasi_id', $request->lokasi_id);
    }

    if ($request->filled('karyawan_id')) {
      $query->where('karyawan_id', $request->karyawan_id);
    }

    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->whereHas('inventaris', function ($qq) use ($search) {
          $qq->where('no_inventaris', 'like', "%{$search}%")
            ->orWhere('kode_aset', 'like', "%{$search}%");
        })
        ->orWhereHas('inventaris.dataAset.kategori', function ($qq) use ($search) {
          $qq->where('nama_barang', 'like', "%{$search}%");
        })
        ->orWhereHas('karyawan', function ($qq) use ($search) {
          $qq->where('nama_karyawan', 'like', "%{$search}%");
        })
        ->orWhere('divisi_klr', 'like', "%{$search}%");
      });
    }

    $histories = $query->latest('tgl_keluar')->get();

    return view('content.dashboard.history.cetak-history-pemakaian', compact('histories', 'user'));
  }

  public function exportExcel(Request $request)
  {
    $user = auth()->user();

    $query = Keluar::with([
      'inventaris.dataAset.kategori',
      'karyawan',
      'perusahaan',
      'lokasi',
      'user',
      'maping',
    ]);

    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tgl_keluar', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tgl_keluar', '<=', $request->tanggal_akhir);
    }

    if ($request->filled('kategori_id')) {
      $query->whereHas('inventaris.dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->kategori_id);
      });
    }

    if ($request->filled('lokasi_id')) {
      $query->where('lokasi_id', $request->lokasi_id);
    }

    if ($request->filled('karyawan_id')) {
      $query->where('karyawan_id', $request->karyawan_id);
    }

    $data = $query->latest('tgl_keluar')->get();

    return Excel::download(new KeluarExport($data), 'History_Pemakaian_Aset.xlsx');
  }
}

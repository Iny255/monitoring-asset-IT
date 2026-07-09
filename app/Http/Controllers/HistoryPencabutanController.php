<?php

namespace App\Http\Controllers;

use App\Models\HistoryPencabutan;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryPencabutanController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = HistoryPencabutan::with(['creator', 'perusahaan', 'maping']);

    /*
        |--------------------------------------------------------------------------
        | Perusahaan
        |--------------------------------------------------------------------------
        */

    if ($user->role != 'super_admin') {
      $query->where('id_perusahaan', $user->id_perusahaan);
    } elseif ($request->filled('perusahaan')) {
      $query->where('id_perusahaan', $request->perusahaan);
    }

    /*
        |--------------------------------------------------------------------------
        | Tanggal
        |--------------------------------------------------------------------------
        */

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
      $query->whereBetween('tanggal_pencabutan', [$request->tanggal_awal, $request->tanggal_akhir]);
    }

    /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_aset', 'like', "%{$search}%")
          ->orWhere('no_inventaris', 'like', "%{$search}%")
          ->orWhere('nama_aset', 'like', "%{$search}%")
          ->orWhere('user_lama', 'like', "%{$search}%")
          ->orWhere('lokasi_lama', 'like', "%{$search}%")
          ->orWhere('lokasi_baru', 'like', "%{$search}%");
      });
    }

    $histories = $query
      ->latest()
      ->paginate(15)
      ->appends($request->query());

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    return view('content.dashboard.history.pencabutan', compact('histories', 'perusahaans'));
  }
  public function cetak(Request $request)
  {
    $user = auth()->user();

    $query = HistoryPencabutan::with(['maping.keluar.inventaris', 'maping.keluar.karyawan', 'creator', 'perusahaan']);

    /*
    |--------------------------------------------------------------------------
    | PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if ($user->role != 'super_admin') {
      $query->where('id_perusahaan', $user->id_perusahaan);
    } elseif ($request->filled('perusahaan')) {
      $query->where('id_perusahaan', $request->perusahaan);
    }

    /*
    |--------------------------------------------------------------------------
    | TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_pencabutan', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_pencabutan', '<=', $request->tanggal_akhir);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_aset', 'like', "%{$search}%")
          ->orWhere('no_inventaris', 'like', "%{$search}%")
          ->orWhere('nama_aset', 'like', "%{$search}%")
          ->orWhere('user_lama', 'like', "%{$search}%")
          ->orWhere('lokasi_lama', 'like', "%{$search}%")
          ->orWhere('lokasi_baru', 'like', "%{$search}%")
          ->orWhere('alasan', 'like', "%{$search}%");
      });
    }

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    $histories = $query->latest()->get();

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    return view('content.dashboard.history.cetak-history-pencabutan', compact('histories', 'user'));
  }
}

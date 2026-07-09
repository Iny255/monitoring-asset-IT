<?php

namespace App\Http\Controllers;

use App\Models\HistoryMutasi;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HistoryMutasiController extends Controller
{
  public function index(Request $request)
  {
    $query = HistoryMutasi::with(['creator', 'perusahaanAsal', 'perusahaanTujuan', 'maping'])->orderByDesc(
      'created_at'
    );

    /*
        |--------------------------------------------------------------------------
        | Multi Company
        |--------------------------------------------------------------------------
        */

    if (auth()->user()->role != 'super_admin') {
      $query->where('id_perusahaan_asal', auth()->user()->id_perusahaan);
    }

    /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('nama_aset', 'like', "%{$search}%")
          ->orWhere('kode_aset_lama', 'like', "%{$search}%")
          ->orWhere('kode_aset_baru', 'like', "%{$search}%")
          ->orWhere('user_lama', 'like', "%{$search}%")
          ->orWhere('user_baru', 'like', "%{$search}%");
      });
    }

    /*
        |--------------------------------------------------------------------------
        | Jenis Mutasi
        |--------------------------------------------------------------------------
        */

    if ($request->filled('jenis_mutasi')) {
      $query->where('jenis_mutasi', $request->jenis_mutasi);
    }

    /*
        |--------------------------------------------------------------------------
        | Perusahaan
        |--------------------------------------------------------------------------
        */

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $query->where('id_perusahaan_asal', $request->perusahaan_id);
      }
    }

    /*
        |--------------------------------------------------------------------------
        | Filter Tanggal
        |--------------------------------------------------------------------------
        */

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_mutasi', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_mutasi', '<=', $request->tanggal_akhir);
    }

    /*
        |--------------------------------------------------------------------------
        | Data
        |--------------------------------------------------------------------------
        */

    $mutasis = $query
      ->latest('tanggal_mutasi')
      ->paginate(10)
      ->appends($request->query());

    /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

    $summary = HistoryMutasi::query();

    if (auth()->user()->role != 'super_admin') {
      $summary->where('id_perusahaan_asal', auth()->user()->id_perusahaan);
    }

    $totalMutasi = (clone $summary)->count();

    $internal = (clone $summary)->where('jenis_mutasi', 'internal')->count();

    $antarPerusahaan = (clone $summary)->where('jenis_mutasi', 'antar_perusahaan')->count();

    $bulanIni = (clone $summary)
      ->whereMonth('tanggal_mutasi', Carbon::now()->month)
      ->whereYear('tanggal_mutasi', Carbon::now()->year)
      ->count();

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    return view(
      'content.dashboard.history.mutasi',
      compact('mutasis', 'perusahaans', 'totalMutasi', 'internal', 'antarPerusahaan', 'bulanIni')
    );
  }

  /*
    |--------------------------------------------------------------------------
    | Detail
    |--------------------------------------------------------------------------
    */

  public function show(HistoryMutasi $historyMutasi)
  {
    $historyMutasi->load(['creator', 'maping', 'perusahaanAsal', 'perusahaanTujuan']);

    return view('content.dashboard.history.show-mutasi', compact('historyMutasi'));
  }

  /*
    |--------------------------------------------------------------------------
    | Cetak
    |--------------------------------------------------------------------------
    */

  public function print(HistoryMutasi $historyMutasi)
  {
    $historyMutasi->load(['creator', 'maping', 'perusahaanAsal', 'perusahaanTujuan']);

    return view('content.dashboard.history.cetak-history-mutasi', compact('historyMutasi'));
  }
  //print all
  public function printAll(Request $request)
  {
    $query = HistoryMutasi::with(['creator', 'perusahaanAsal', 'perusahaanTujuan']);

    /*
    |--------------------------------------------------------------------------
    | Multi Company
    |--------------------------------------------------------------------------
    */

    if (auth()->user()->role != 'super_admin') {
      $query->where('id_perusahaan_asal', auth()->user()->id_perusahaan);
    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('nama_aset', 'like', "%{$search}%")
          ->orWhere('kode_aset_lama', 'like', "%{$search}%")
          ->orWhere('kode_aset_baru', 'like', "%{$search}%")
          ->orWhere('user_lama', 'like', "%{$search}%")
          ->orWhere('user_baru', 'like', "%{$search}%");
      });
    }

    if ($request->filled('jenis_mutasi')) {
      $query->where('jenis_mutasi', $request->jenis_mutasi);
    }

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_mutasi', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_mutasi', '<=', $request->tanggal_akhir);
    }

    if (auth()->user()->role == 'super_admin' && $request->filled('perusahaan_id')) {
      $query->where('id_perusahaan_asal', $request->perusahaan_id);
    }

    $mutasis = $query->latest('tanggal_mutasi')->get();

    return view('content.dashboard.history.cetak-history-mutasiall', compact('mutasis'));
  }
}

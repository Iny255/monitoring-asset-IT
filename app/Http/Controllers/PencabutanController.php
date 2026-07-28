<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Maping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\HistoryPencabutan;
use Illuminate\Http\Request;
class PencabutanController extends Controller
{
  public function create(Maping $maping)
  {
    $user = auth()->user();

    if ($user->role != 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }
    if ($maping->status != 'aktif') {
    return redirect()
        ->route('maping.index')
        ->with('error', 'Hak Akses hanya dapat dikelola pada mapping yang berstatus Aktif.');
}

    $maping->load(['perusahaan', 'lokasi', 'keluar.karyawan', 'keluar.inventaris.dataAset.kategori']);

    /*
        |--------------------------------------------------------------------------
        | Lokasi Gudang
        |--------------------------------------------------------------------------
        */

    $lokasis = Lokasi::where('id_perusahaan', $maping->id_perusahaan)
      ->orderBy('nama_lokasi')
      ->get();

    return view('content.dashboard.maping.pencabutan', compact('maping', 'lokasis'));
  }

  /**
   * Simpan
   */
  public function store(Request $request, Maping $maping)
  {
    $request->validate([
      'tanggal_pencabutan' => 'required|date',
      'id_lokasi' => 'required|exists:lokasis,id',
      'alasan' => 'required|string|max:500',
    ]);

    DB::beginTransaction();

    try {
      $maping->load(['keluar.inventaris.dataAset.kategori', 'keluar.karyawan', 'karyawan', 'lokasi', 'perusahaan']);
      // dd($maping->toArray());

      $inventaris = $maping->keluar->inventaris;
      $dataAset = $inventaris->dataAset;

      /*
        |--------------------------------------------------------------------------
        | History
        |--------------------------------------------------------------------------
        
        */
      $lokasiBaru = Lokasi::find($request->id_lokasi);

      HistoryPencabutan::create([
        'inventaris_id' => $inventaris->id,
        'maping_id' => $maping->id,

        'id_perusahaan' => $maping->id_perusahaan,

        'kode_aset' => $inventaris->kode_aset,
        'no_inventaris' => $inventaris->no_inventaris,

        'nama_aset' => optional($dataAset->kategori)->nama_barang,

        // ambil dari mapping aktif
        'lokasi_lama' => optional($maping->lokasi)->nama_lokasi,
        'lokasi_baru' => optional($lokasiBaru)->nama_lokasi,

        // ambil dari mapping aktif
        'user_lama' => optional($maping->karyawan)->nama_karyawan,

        'tanggal_pencabutan' => $request->tanggal_pencabutan,

        'alasan' => $request->alasan,

        'created_by' => auth()->id(),
      ]);

      /*
        |--------------------------------------------------------------------------
        | Update Inventaris
        |--------------------------------------------------------------------------
        */

      $inventaris->update([
        'status' => 'TERSEDIA',
      ]);

      /*
        |--------------------------------------------------------------------------
        | Update Mapping
        |--------------------------------------------------------------------------
        */

      $maping->update([
        'status' => 'selesai',
        'id_lokasi' => $request->id_lokasi,
      ]);

      DB::commit();

      $maping->loadMissing('keluar.inventaris');
      $dataAsetId = $maping->keluar?->inventaris?->data_aset_id;
      $targetUrl = $dataAsetId ? route('history.perjalanan.show', $dataAsetId) : route('history.perjalanan.index');

      return redirect($targetUrl)
        ->with('success', 'Pencabutan berhasil dilakukan.');
    } catch (\Exception $e) {
      DB::rollBack();

      dd($e->getMessage(), $e->getLine(), $e->getFile());
    }
  }
}

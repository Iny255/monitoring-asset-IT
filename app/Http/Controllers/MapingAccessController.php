<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\HistoryHakAkses;
use App\Models\Maping;
use App\Models\MapingAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapingAccessController extends Controller
{
  /**
   * ===========================================================
   * INDEX
   * ===========================================================
   */

  public function index(Maping $maping)
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

    $maping->load([
      'keluar.inventaris.dataAset.kategori',
      'keluar.karyawan',
      'lokasi',
      'perusahaan',
      'mapingAccesses.access',
    ]);

    /*
        |--------------------------------------------------------------------------
        | MASTER ACCESS
        |--------------------------------------------------------------------------
        */
    $user = auth()->user();

    $queryAccess = Access::where('status', 'aktif');

    if ($user->role == 'super_admin') {
      $queryAccess->where('id_perusahaan', $maping->id_perusahaan);
    } else {
      $queryAccess->where('id_perusahaan', $user->id_perusahaan);
    }

    $aplikasis = (clone $queryAccess)
      ->where('kategori', 'Aplikasi')
      ->orderBy('nama_akses')
      ->get();

    $hakAksesPPN = (clone $queryAccess)
      ->where('kategori', 'Hak Akses')
      ->where('jenis', 'PPN')
      ->orderBy('nama_akses')
      ->get();

    $hakAksesNonPPN = (clone $queryAccess)
      ->where('kategori', 'Hak Akses')
      ->where('jenis', 'NON PPN')
      ->orderBy('nama_akses')
      ->get();

    return view('content.dashboard.maping.hak-akses', compact('maping', 'aplikasis', 'hakAksesPPN', 'hakAksesNonPPN'));
  }

  /**
   * ===========================================================
   * STORE
   * ===========================================================
   */

  public function store(Request $request, Maping $maping)
  {
    $request->validate([
      'accesses' => 'required|array',
      'accesses.*' => 'exists:accesses,id',
    ]);

    DB::beginTransaction();

    try {
      foreach ($request->accesses as $accessId) {
        $cek = MapingAccess::where('maping_id', $maping->id)
          ->where('access_id', $accessId)
          ->exists();

        if (!$cek) {
          /*
                    |--------------------------------------------------------------------------
                    | MAPING ACCESS
                    |--------------------------------------------------------------------------
                    */

          MapingAccess::create([
            'maping_id' => $maping->id,
            'access_id' => $accessId,
          ]);

          /*
                    |--------------------------------------------------------------------------
                    | HISTORY
                    |--------------------------------------------------------------------------
                    */

          $access = Access::findOrFail($accessId);

          HistoryHakAkses::create([
            'maping_id' => $maping->id,
            'access_id' => $access->id,
            'nama_akses' => $access->nama_akses,
            'kategori' => $access->kategori,
            'jenis' => $access->jenis,
            'user_id' => auth()->id(),
            'aksi' => 'tambah',
            'keterangan' => 'Menambahkan Hak Akses',
          ]);
        }
      }

      DB::commit();

      return redirect()
        ->route('history.hak-akses.index')
        ->with('success', 'Hak Akses berhasil ditambahkan.');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menambahkan Hak Akses.');
    }
  }

  /**
   * ===========================================================
   * DESTROY
   * ===========================================================
   */

  public function destroy(Maping $maping, MapingAccess $access)
  {
    DB::beginTransaction();

    try {
      /*
            |--------------------------------------------------------------------------
            | HISTORY
            |--------------------------------------------------------------------------
            */
      $access->load('access');

      HistoryHakAkses::create([
        'maping_id' => $access->maping_id,
        'access_id' => $access->access_id,
        'nama_akses' => $access->access->nama_akses,
        'kategori' => $access->access->kategori,
        'jenis' => $access->access->jenis,
        'user_id' => auth()->id(),
        'aksi' => 'hapus',
        'keterangan' => 'Menghapus Hak Akses',
      ]);
      if (auth()->user()->role != 'super_admin' && $access->maping->id_perusahaan != auth()->user()->id_perusahaan) {
        abort(403);
      }
      /*
            |--------------------------------------------------------------------------
            | DELETE
            |--------------------------------------------------------------------------
            */

      $access->delete();

      DB::commit();

      return redirect()
        ->route('history.hak-akses.index')
        ->with('success', 'Hak Akses berhasil dihapus.');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menghapus Hak Akses.');
    }
  }
}

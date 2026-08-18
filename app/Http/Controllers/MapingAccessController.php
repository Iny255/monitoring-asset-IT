<?php

namespace App\Http\Controllers;

use App\Models\HistoryHakAkses;
use App\Models\Maping;
use App\Models\MapingAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class MapingAccessController extends Controller
{
  /**
   * INDEX
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
      'mapingAccesses',
    ]);

    return view('content.dashboard.maping.hak-akses', compact('maping'));
  }

  /**
   * STORE (MANUAL INPUT)
   */
  public function store(Request $request, Maping $maping)
  {
    $request->validate([
      'nama_akses' => 'required|string|max:150',
      'kategori'   => 'required|in:Aplikasi,Hak Akses',
      'jenis'      => 'required|in:Software,PPN,NON PPN',
      'email'      => 'nullable|string|max:150',
      'password'   => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
      $namaAkses = strtoupper(trim($request->nama_akses));
      $emailVal  = $request->filled('email') ? trim($request->email) : null;
      $passEnc   = $request->filled('password') ? Crypt::encryptString(trim($request->password)) : null;

      $mapingAccess = MapingAccess::create([
        'maping_id'  => $maping->id,
        'nama_akses' => $namaAkses,
        'kategori'   => $request->kategori,
        'jenis'      => $request->jenis,
        'email'      => $emailVal,
        'password'   => $passEnc,
      ]);

      HistoryHakAkses::create([
        'maping_id'  => $maping->id,
        'nama_akses' => $mapingAccess->nama_akses,
        'kategori'   => $mapingAccess->kategori,
        'jenis'      => $mapingAccess->jenis,
        'email'      => $emailVal,
        'user_id'    => auth()->id(),
        'aksi'       => 'tambah',
        'keterangan' => 'Menambahkan Hak Akses: ' . $namaAkses . ($emailVal ? ' (' . $emailVal . ')' : ''),
      ]);

      DB::commit();

      return redirect()
        ->route('maping.hak-akses', $maping->id)
        ->with('success', 'Hak Akses / Aplikasi berhasil ditambahkan.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal menambahkan Hak Akses.');
    }
  }

  /**
   * UPDATE
   */
  public function update(Request $request, Maping $maping, MapingAccess $access)
  {
    $user = auth()->user();
    if ($user->role != 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $request->validate([
      'nama_akses' => 'required|string|max:150',
      'kategori'   => 'required|in:Aplikasi,Hak Akses',
      'jenis'      => 'required|in:Software,PPN,NON PPN',
      'email'      => 'nullable|string|max:150',
      'password'   => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
      $namaAkses = strtoupper(trim($request->nama_akses));
      $emailVal  = $request->filled('email') ? trim($request->email) : null;

      $access->nama_akses = $namaAkses;
      $access->kategori   = $request->kategori;
      $access->jenis      = $request->jenis;
      $access->email      = $emailVal;

      if ($request->filled('password')) {
        $access->password = Crypt::encryptString(trim($request->password));
      }

      $access->save();

      HistoryHakAkses::create([
        'maping_id'  => $access->maping_id,
        'access_id'  => $access->access_id,
        'nama_akses' => $access->nama_akses,
        'kategori'   => $access->kategori,
        'jenis'      => $access->jenis,
        'email'      => $emailVal,
        'user_id'    => auth()->id(),
        'aksi'       => 'update',
        'keterangan' => 'Memperbarui Hak Akses: ' . $namaAkses . ($emailVal ? ' (' . $emailVal . ')' : ''),
      ]);

      DB::commit();

      return redirect()
        ->route('maping.hak-akses', $maping->id)
        ->with('success', 'Hak Akses berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal memperbarui Hak Akses.');
    }
  }

  /**
   * BULK UPDATE
   */
  public function bulkUpdate(Request $request, Maping $maping)
  {
    $user = auth()->user();
    if ($user->role != 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $request->validate([
      'target_group' => 'required|in:ppn,nonppn,aplikasi,all',
      'email'        => 'nullable|string|max:255',
      'password'     => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {
      $query = MapingAccess::where('maping_id', $maping->id);

      if ($request->target_group == 'aplikasi') {
        $query->where('kategori', 'Aplikasi');
      } elseif ($request->target_group == 'ppn') {
        $query->where('jenis', 'PPN');
      } elseif ($request->target_group == 'nonppn') {
        $query->where('jenis', 'NON PPN');
      }

      $items = $query->get();

      if ($items->isEmpty()) {
        return back()->with('error', 'Tidak ada hak akses terpasang yang cocok dengan kelompok terpilih.');
      }

      $emailVal      = $request->filled('email') ? trim($request->email) : null;
      $hasEmailInput = $request->filled('email');
      $hasPassInput  = $request->filled('password');
      $passEnc       = $hasPassInput ? Crypt::encryptString(trim($request->password)) : null;

      $count = 0;
      foreach ($items as $item) {
        if ($hasEmailInput) {
          $item->email = $emailVal;
        }
        if ($hasPassInput) {
          $item->password = $passEnc;
        }
        $item->save();

        HistoryHakAkses::create([
          'maping_id'  => $item->maping_id,
          'access_id'  => $item->access_id,
          'nama_akses' => $item->nama_akses,
          'kategori'   => $item->kategori,
          'jenis'      => $item->jenis,
          'email'      => $item->email,
          'user_id'    => auth()->id(),
          'aksi'       => 'update',
          'keterangan' => 'Update Massal Kredensial Hak Akses (' . strtoupper($request->target_group) . ')',
        ]);

        $count++;
      }

      DB::commit();

      return redirect()
        ->route('maping.hak-akses', $maping->id)
        ->with('success', "Berhasil memperbarui kredensial untuk {$count} hak akses sekaligus.");
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal melakukan update massal kredensial.');
    }
  }

  /**
   * DESTROY
   */
  public function destroy(Maping $maping, MapingAccess $access)
  {
    DB::beginTransaction();

    try {
      HistoryHakAkses::create([
        'maping_id'  => $access->maping_id,
        'access_id'  => $access->access_id,
        'nama_akses' => $access->nama_akses,
        'kategori'   => $access->kategori,
        'jenis'      => $access->jenis,
        'email'      => $access->email,
        'user_id'    => auth()->id(),
        'aksi'       => 'hapus',
        'keterangan' => 'Menghapus Hak Akses: ' . $access->nama_akses,
      ]);

      if (auth()->user()->role != 'super_admin' && $access->maping->id_perusahaan != auth()->user()->id_perusahaan) {
        abort(403);
      }

      $access->delete();

      DB::commit();

      return redirect()
        ->route('maping.hak-akses', $maping->id)
        ->with('success', 'Hak Akses berhasil dihapus.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal menghapus Hak Akses.');
    }
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Perusahaan;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $search = $request->search;
    $perusahaanId = $request->perusahaan_id;

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    $isGrouped = ($user->role === 'super_admin' && empty($perusahaanId));

    if ($isGrouped) {
      $query = Karyawan::select(
          'karyawans.kode_karyawan',
          'karyawans.nama_karyawan',
          DB::raw('MIN(karyawans.jabatan) as jabatan'),
          DB::raw('MIN(karyawans.divisi) as divisi'),
          DB::raw('MIN(karyawans.id) as id'),
          DB::raw('COUNT(DISTINCT karyawans.id_perusahaan) as total_perusahaan'),
          DB::raw('GROUP_CONCAT(DISTINCT perusahaans.nama_perusahaan ORDER BY perusahaans.nama_perusahaan ASC SEPARATOR "||") as daftar_perusahaan')
        )
        ->leftJoin('perusahaans', 'perusahaans.id', '=', 'karyawans.id_perusahaan')
        ->groupBy('karyawans.kode_karyawan', 'karyawans.nama_karyawan');

      if ($search) {
        $query->where(function ($q) use ($search) {
          $q->where('karyawans.nama_karyawan', 'like', "%{$search}%")->orWhere('karyawans.kode_karyawan', 'like', "%{$search}%");
        });
      }

      $karyawans = $query
        ->orderBy('karyawans.nama_karyawan', 'asc')
        ->paginate(10)
        ->appends($request->query());
    } else {
      $query = Karyawan::with('perusahaan');

      if ($user->role !== 'super_admin') {
        $query->where('id_perusahaan', $user->id_perusahaan);
      } elseif ($perusahaanId) {
        $query->where('id_perusahaan', $perusahaanId);
      }

      if ($search) {
        $query->where(function ($q) use ($search) {
          $q->where('nama_karyawan', 'like', "%{$search}%")->orWhere('kode_karyawan', 'like', "%{$search}%");
        });
      }

      $karyawans = $query
        ->latest()
        ->paginate(10)
        ->appends($request->query());
    }

    return view('content.dashboard.useraset.index', compact('karyawans', 'perusahaans', 'perusahaanId', 'isGrouped'));
  }

  public function detailPerusahaan(Request $request)
  {
    $request->validate([
      'kode_karyawan' => 'required',
    ]);

    $data = Karyawan::with('perusahaan')
      ->withCount('mapings')
      ->where('kode_karyawan', $request->kode_karyawan)
      ->orderBy('id_perusahaan')
      ->get();

    return response()->json($data);
  }

  /**
   * Store a newly created resource.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    $perusahaanId = $user->role === 'super_admin' ? $request->id_perusahaan : $user->id_perusahaan;

    $validated = $request->validate(
      [
        'kode_karyawan' => [
          'required',
          'string',
          'max:20',
          Rule::unique('karyawans')->where(fn($q) => $q->where('id_perusahaan', $perusahaanId)),
        ],
        'nama_karyawan' => 'required|string|max:100',
        'jabatan' => 'required|string|max:50',
        'divisi' => 'required|string|max:50',
        'id_perusahaan' => $user->role === 'super_admin' ? 'required' : 'nullable',
      ],

      [
        'kode_karyawan.unique' => 'Kode karyawan sudah ada di perusahaan ini.',
      ]
    );
    $validated['nama_karyawan'] = strtoupper($validated['nama_karyawan']);
    $validated['jabatan'] = strtoupper($validated['jabatan']);
    $validated['divisi'] = strtoupper($validated['divisi']);

    $validated['id_perusahaan'] = $perusahaanId;

    Karyawan::create($validated);

    return redirect()
      ->route('useraset.index')
      ->with('success', 'Data berhasil disimpan.');
  }

  /**
   * Update the specified resource.
   */
  public function update(Request $request, string $id)
  {
    $karyawan = Karyawan::findOrFail($id);

    $user = auth()->user();

    $perusahaanId = $user->role === 'super_admin' ? $request->id_perusahaan : $user->id_perusahaan;

    if ($user->role !== 'super_admin' && $karyawan->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $validated = $request->validate([
      'kode_karyawan' => 'required',
      'nama_karyawan' => 'required',
      'jabatan' => 'required',
      'divisi' => 'required',
    ]);

    $validated['kode_karyawan'] = strtoupper($validated['kode_karyawan']);
    $validated['nama_karyawan'] = strtoupper($validated['nama_karyawan']);
    $validated['jabatan'] = strtoupper($validated['jabatan']);
    $validated['divisi'] = strtoupper($validated['divisi']);
    $validated['id_perusahaan'] = $perusahaanId;

    $karyawan->update($validated);

    return redirect()
      ->route('useraset.index')
      ->with('success', 'Data berhasil diperbarui.');
  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(int $id)
  {
    $karyawan = Karyawan::findOrFail($id);

    $user = auth()->user();

    if ($user->role !== 'super_admin' && $karyawan->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    try {
      $karyawan->delete();

      return redirect()
        ->route('useraset.index')
        ->with('success', 'Data berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal menghapus: Karyawan ini masih terikat dengan data pemakaian/peminjaman aset.');
    }
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Perusahaan;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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

    // 🔥 ambil data perusahaan untuk dropdown
    $perusahaans = Perusahaan::all();

    if ($user->role === 'super_admin') {
      $karyawans = Karyawan::with('perusahaan');

      if ($perusahaanId) {
        $karyawans->where('id_perusahaan', $perusahaanId);
      }
    } else {
      $karyawans = Karyawan::with('perusahaan')->where('id_perusahaan', $user->id_perusahaan);
    }

    if ($search) {
      $karyawans->where(function ($query) use ($search) {
        $query->where('nama_karyawan', 'like', "%{$search}%")->orWhere('kode_karyawan', 'like', "%{$search}%");
      });
    }

    $karyawans = $karyawans->latest()->paginate(5);

    return view('content.dashboard.useraset.index', compact('karyawans', 'perusahaans'));
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
  public function update(Request $request, $id)
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

    $karyawan->delete();

    return redirect()
      ->route('useraset.index')
      ->with('success', 'Data berhasil dihapus.');
  }
}

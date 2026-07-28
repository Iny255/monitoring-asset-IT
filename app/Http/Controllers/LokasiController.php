<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LokasiController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    if (!$user) {
      abort(403);
    }

    $search = $request->search;
    $perusahaanId = $request->perusahaan_id;

    $perusahaans = $user->role === 'super_admin' ? Perusahaan::all() : collect();

    if ($user->role === 'super_admin') {
      // ==========================================
      // SUPER ADMIN MEMILIH PERUSAHAAN
      // ==========================================
      if ($perusahaanId) {
        $lokasis = Lokasi::with('perusahaan')->where('id_perusahaan', $perusahaanId);
      } else {
        // ==========================================
        // SUPER ADMIN - SEMUA PERUSAHAAN
        // ==========================================
        $lokasis = Lokasi::select(DB::raw('MIN(id) as id'), 'nama_lokasi')
          ->selectRaw('COUNT(DISTINCT id_perusahaan) as total_perusahaan')
          ->groupBy('nama_lokasi');
      }
    } else {
      // ==========================================
      // PETUGAS
      // ==========================================
      $lokasis = Lokasi::with('perusahaan')->where('id_perusahaan', $user->id_perusahaan);
    }

    // ==========================================
    // SEARCH
    // ==========================================
    if ($search) {
      $lokasis->where('nama_lokasi', 'like', "%{$search}%");
    }

    // ==========================================
    // PAGINATION
    // ==========================================
    if ($user->role === 'super_admin' && !$perusahaanId) {
      $lokasis = $lokasis
        ->orderBy('nama_lokasi')
        ->paginate(10)
        ->appends($request->query());
    } else {
      $lokasis = $lokasis
        ->latest()
        ->paginate(10)
        ->appends($request->query());
    }

    return view('content.dashboard.lokasi.index', compact('lokasis', 'perusahaans', 'perusahaanId'));
  }
  public function detailPerusahaan(Request $request)
  {
    $request->validate([
      'nama_lokasi' => 'required',
    ]);

    $data = Lokasi::with('perusahaan')
      ->where('nama_lokasi', $request->nama_lokasi)
      ->orderBy('id_perusahaan')
      ->get();

    return response()->json($data);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    // 🔥 tentukan perusahaan
    $perusahaanId = $user->role === 'super_admin' ? $request->id_perusahaan : $user->id_perusahaan;

    $validated = $request->validate([
      'nama_lokasi' => 'required|string|max:50',
      'id_perusahaan' => $user->role === 'super_admin' ? 'required' : 'nullable',
    ]);

    $validated['nama_lokasi'] = strtoupper($validated['nama_lokasi']);
    try {
      $validated['id_perusahaan'] = $perusahaanId;

      Lokasi::create($validated);

      return redirect()
        ->route('lokasi.index')
        ->with('success', 'Data lokasi berhasil disimpan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menyimpan data.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Lokasi $lokasi)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Lokasi $lokasi)
  {
    return view('content.dashboard.lokasi.edit', compact('lokasi'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Lokasi $lokasi)
  {
    $user = auth()->user();

    $perusahaanId = $user->role === 'super_admin' ? $request->id_perusahaan : $user->id_perusahaan;

    // 🔒 proteksi data lintas perusahaan
    if ($user->role !== 'super_admin' && $lokasi->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $validated = $request->validate([
      'nama_lokasi' => 'required|string|max:50',
    ]);

    $validated['nama_lokasi'] = strtoupper($validated['nama_lokasi']);
    $validated['id_perusahaan'] = $perusahaanId;

    $lokasi->update($validated);

    return redirect()
      ->route('lokasi.index')
      ->with('success', 'Data lokasi diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(int $id)
  {
    $lokasi = Lokasi::findOrFail($id);
    $user = auth()->user();

    // 🔒 proteksi
    if ($user->role !== 'super_admin' && $lokasi->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $lokasi->delete();

    return back()->with('success', 'Lokasi berhasil dihapus');
  }
}

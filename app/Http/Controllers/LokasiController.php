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
    $isGrouped = ($user->role === 'super_admin' && empty($perusahaanId));

    if ($isGrouped) {
      $query = Lokasi::select(
          'lokasis.nama_lokasi',
          DB::raw('MIN(lokasis.id) as id'),
          DB::raw('COUNT(DISTINCT lokasis.id_perusahaan) as total_perusahaan'),
          DB::raw('GROUP_CONCAT(DISTINCT perusahaans.nama_perusahaan ORDER BY perusahaans.nama_perusahaan ASC SEPARATOR "||") as daftar_perusahaan')
        )
        ->leftJoin('perusahaans', 'perusahaans.id', '=', 'lokasis.id_perusahaan')
        ->groupBy('lokasis.nama_lokasi');

      if ($search) {
        $query->where('lokasis.nama_lokasi', 'like', "%{$search}%");
      }

      $lokasis = $query->orderBy('lokasis.nama_lokasi', 'asc')
        ->paginate(10)
        ->appends($request->query());
    } else {
      $query = Lokasi::with('perusahaan');

      if ($user->role !== 'super_admin') {
        $query->where('id_perusahaan', $user->id_perusahaan);
      } elseif ($perusahaanId) {
        $query->where('id_perusahaan', $perusahaanId);
      }

      if ($search) {
        $query->where('nama_lokasi', 'like', "%{$search}%");
      }

      $lokasis = $query->latest()->paginate(10)->appends($request->query());
    }

    return view('content.dashboard.lokasi.index', compact('lokasis', 'perusahaans', 'perusahaanId', 'isGrouped'));
  }

  public function detailPerusahaan(Request $request)
  {
    $request->validate([
      'nama_lokasi' => 'required',
    ]);

    $data = Lokasi::with('perusahaan')
      ->withCount('maping')
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
      'nama_lokasi' => [
        'required',
        'string',
        'max:50',
        Rule::unique('lokasis')->where(fn($q) => $q->where('id_perusahaan', $perusahaanId)),
      ],
      'id_perusahaan' => $user->role === 'super_admin' ? 'required' : 'nullable',
    ], [
      'nama_lokasi.unique' => 'Nama lokasi sudah terdaftar pada perusahaan yang dipilih.',
    ]);

    $validated['nama_lokasi'] = strtoupper(trim($validated['nama_lokasi']));
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
      'nama_lokasi' => [
        'required',
        'string',
        'max:50',
        Rule::unique('lokasis')
          ->where(fn($q) => $q->where('id_perusahaan', $perusahaanId))
          ->ignore($lokasi->id),
      ],
      'id_perusahaan' => $user->role === 'super_admin' ? 'required' : 'nullable',
    ], [
      'nama_lokasi.unique' => 'Nama lokasi sudah terdaftar pada perusahaan tersebut.',
    ]);

    $validated['nama_lokasi'] = strtoupper(trim($validated['nama_lokasi']));
    $validated['id_perusahaan'] = $perusahaanId;

    try {
      $lokasi->update($validated);

      return redirect()
        ->route('lokasi.index')
        ->with('success', 'Data lokasi diperbarui.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal memperbarui data.');
    }
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

    try {
      $lokasi->delete();

      return back()->with('success', 'Lokasi berhasil dihapus');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal menghapus: Lokasi ini masih digunakan pada data aset atau transaksi.');
    }
  }
}

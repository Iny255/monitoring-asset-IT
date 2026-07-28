<?php

namespace App\Http\Controllers;

use App\Models\DataAset;
use App\Models\Kategori;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataAsetController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $search = $request->search;
    $perusahaanId = $request->perusahaan_id;

    $query = DataAset::with(['kategori', 'perusahaan']);

    // FILTER PERUSAHAAN
    if ($user->role !== 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($perusahaanId) {
      $query->where('perusahaan_id', $perusahaanId);
    }

    // SEARCH
    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('merek', 'like', "%{$search}%")->orWhere('type', 'like', "%{$search}%");
      });
    }

    $dataAsets = $query->latest()->paginate(10)->appends($request->query());

    if ($user->role == 'super_admin') {
      // kosongkan dulu
      $kategoris = collect();
    } else {
      $kategoris = Kategori::where('perusahaan_id', $user->id_perusahaan)
        ->orderBy('nama_barang')
        ->get();
    }

    $perusahaans = Perusahaan::all();

    return view('content.dashboard.data-aset.index', compact('dataAsets', 'kategoris', 'perusahaans'));
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

    $validated = $request->validate([
      'perusahaan_id' => auth()->user()->role === 'super_admin' ? 'required|exists:perusahaans,id' : 'nullable',

      'kategori_id' => 'required|exists:kategoris,id',
      'merek' => 'required|string|max:100',
      'type' => 'required|string|max:100',
      'warna' => 'nullable|string|max:50',
    ]);
    try {
      $perusahaanId = $user->role === 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;
      $kategori = Kategori::findOrFail($request->kategori_id);

      if ($kategori->perusahaan_id != $perusahaanId) {
        return back()->with('error', 'Kategori tidak sesuai perusahaan.');
      }

      DataAset::create([
        'perusahaan_id' => $perusahaanId,

        'kategori_id' => $request->kategori_id,

        'merek' => strtoupper($request->merek),

        'type' => strtoupper($request->type),

        'warna' => $request->warna ? strtoupper($request->warna) : null,
      ]);

      return back()->with('success', 'Data aset berhasil disimpan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menyimpan data aset.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $aset = DataAset::findOrFail($id);

    $request->validate([
      'kategori_id' => 'required|exists:kategoris,id',
      'merek' => 'required|string|max:100',
      'type' => 'required|string|max:100',
      'warna' => 'nullable|string|max:50',
    ]);

    $kategori = Kategori::findOrFail($request->kategori_id);

    if ($kategori->perusahaan_id != $aset->perusahaan_id) {
      return back()->with('error', 'Kategori tidak sesuai perusahaan aset.');
    }

    $aset->update([
      'kategori_id' => $request->kategori_id,
      'merek' => strtoupper($request->merek),
      'type' => strtoupper($request->type),
      'warna' => strtoupper($request->warna),
    ]);

    return back()->with('success', 'Data aset berhasil diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $aset = DataAset::findOrFail($id);

    $aset->delete();

    return back()->with('success', 'Data aset berhasil dihapus.');
  }
  public function getKategori(string $id)
  {
    $kategoris = Kategori::where('perusahaan_id', $id)
      ->orderBy('nama_barang')
      ->get();

    return response()->json($kategoris);
  }
}

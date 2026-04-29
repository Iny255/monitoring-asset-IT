<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = auth()->user();
    $search = $request->input('search');
    $perusahaanId = $request->perusahaan_id;

    // 🔥 QUERY UTAMA + RELASI
    if ($user->role === 'super_admin') {
      $query = \App\Models\Kategori::with('perusahaan');

      // filter perusahaan (optional)
      if ($perusahaanId) {
        $query->where('perusahaan_id', $perusahaanId);
      }
    } else {
      $query = \App\Models\Kategori::with('perusahaan')->where('perusahaan_id', $user->perusahaan->id);
    }

    // 🔍 SEARCH
    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('nama_barang', 'like', '%' . $search . '%')->orWhere('kode_barang', 'like', '%' . $search . '%');
      });
    }

    // PAGINATION
    $kategoris = $query->latest()->paginate(5);

    // 🔥 GENERATE KODE BARANG
    // 🔥 tentukan perusahaan (dari filter atau default)
    if ($user->role === 'super_admin') {
      $perusahaanFix = $perusahaanId;
    } else {
      $perusahaanFix = $user->perusahaan->id;
    }

    // 🔥 generate kode berdasarkan perusahaan
    if ($perusahaanFix) {
      $last = Kategori::where('perusahaan_id', $perusahaanFix)
        ->orderBy('kode_barang', 'desc')
        ->first();

      if ($last && $last->kode_barang) {
        $number = (int) substr($last->kode_barang, 2) + 1;
      } else {
        $number = 1;
      }

      $kodeBarang = 'KD' . str_pad($number, 4, '0', STR_PAD_LEFT);
    } else {
      // kalau belum pilih perusahaan
      $kodeBarang = 'Pilih Perusahaan Dulu';
    }

    // 🔥 AMBIL SEMUA PERUSAHAAN (UNTUK DROPDOWN)
    $perusahaans = \App\Models\Perusahaan::all();

    return view('content.dashboard.kategori.index', compact('kategoris', 'kodeBarang', 'perusahaans'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    $validatedData = $request->validate([
      'kode_barang' => 'required|string|max:10',
      'nama_barang' => 'required|string|max:50',
    ]);

    try {
      // 🔥 SET PERUSAHAAN
      if ($user->role === 'super_admin') {
        $validatedData['perusahaan_id'] = $request->perusahaan_id;
      } else {
        $validatedData['perusahaan_id'] = $user->perusahaan->id;
      }

      Kategori::create($validatedData);

      return redirect('/dashboard/kategori')->with('success', 'Data kategori berhasil disimpan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menyimpan data.');
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Kategori $kategori)
  {
    $validated = $request->validate([
      'nama_barang' => 'required|string|max:50',
    ]);

    try {
      $kategori->update($validated);

      return redirect()
        ->route('kategori.index')
        ->with('success', 'Data berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->with('error', 'Gagal update.');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $kategori = Kategori::findOrFail($id);
    $kategori->delete();

    return back()->with('success', 'Data berhasil dihapus.');
  }
  public function getKode($id)
  {
    $last = Kategori::where('perusahaan_id', $id)
      ->orderBy('kode_barang', 'desc')
      ->first();

    if ($last && $last->kode_barang) {
      $number = (int) substr($last->kode_barang, 2) + 1;
    } else {
      $number = 1;
    }

    $kode = 'KD' . str_pad($number, 4, '0', STR_PAD_LEFT);

    return response()->json([
      'kode' => $kode,
    ]);
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $perusahaan = auth()->user()->perusahaan;
    
    $search = $request->input('search');

    $kategoris = Kategori::where('perusahaan_id', $perusahaan->id)->latest();

    if ($search) {
      $kategoris->where(function ($query) use ($search) {
        $query->where('nama_barang', 'like', '%' . $search . '%')
          ->orWhere('kode_barang', 'like', '%' . $search . '%');
      });
    }

    $kategoris = $kategoris->paginate(5);

    // 🔥 GENERATE KODE BARANG DI INDEX
    $last = Kategori::where('perusahaan_id', $perusahaan->id)
      ->orderBy('kode_barang', 'desc')->first();

    if ($last) {
      $number = (int) substr($last->kode_barang, 2) + 1;
    } else {
      $number = 1;
    }

    $kodeBarang = 'KD' . str_pad($number, 4, '0', STR_PAD_LEFT);

    return view(
      'content.dashboard.kategori.index',
      compact('kategoris', 'kodeBarang')
    );
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $perusahaan = auth()->user()->perusahaan;
    
    $validatedData = $request->validate([
      'kode_barang' => 'required|string|unique:kategoris,kode_barang,NULL,id,perusahaan_id,' . $perusahaan->id,
      'nama_barang' => 'required|string|max:50',
    ]);

    try {
      $validatedData['perusahaan_id'] = $perusahaan->id;
      $kategori = Kategori::create($validatedData);

      return redirect('/dashboard/kategori')->with('success', 'Data kategori barang berhasil disimpan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return redirect('/dashboard/kategori')
       ->with('error', 'Data kategori barang tidak berhasil disimpan.');
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
        ->with('success', 'Data kategori berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->with('error', 'Gagal mengupdate data.');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $kategori = Kategori::findOrFail($id);
    $kategori->delete();

    return redirect()->back()
      ->with('success', 'Kategori Barang berhasil dihapus');
  }
}


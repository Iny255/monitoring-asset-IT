<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
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
    $search = $request->input('search');

    $kategoris = Kategori::latest();

    if ($search) {
      $kategoris = $kategoris->where(function ($query) use ($search) {
        $query->where('nama_barang', 'like', '%' . $search . '%')
          ->orWhere('id', 'like', '%' . $search . '%');
      });
    }

    $kategoris = $kategoris->paginate(6);

    // 🔥 GENERATE KODE BARANG DI INDEX
    $last = Kategori::orderBy('kode_barang', 'desc')->first();

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
   * Show the form for creating a new resource.
   */
  public function create() {}

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'kode_barang' => 'required|string|unique:kategoris,kode_barang',
      'nama_barang' => 'required|string|max:50',
    ]);

    try {
      $kategori = Kategori::create($validatedData);

      if ($kategori) {
        return redirect('/dashboard/kategori')->with('success', 'Data kategori barang berhasil disimpan.');
      } else {
        return redirect('/dashboard/kategori')->with('error', 'Gagal menyimpan data kategori barang.');
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return redirect('/dashboard/kategori')
       ->with('error', 'Data kategori barang tidak berhasil disimpan.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Kategori $kategori)
  {
    //
  }


  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Kategori $kategori)
  {
    return view('content.dashboard.kategori.edit', compact('kategori'));
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

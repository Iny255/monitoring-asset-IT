<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
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
      $query = Kategori::with('perusahaan');

      // filter perusahaan (optional)
      if ($perusahaanId) {
        $query->where('perusahaan_id', $perusahaanId);
      }
    } else {
      $query = Kategori::with('perusahaan')->where('perusahaan_id', $user->id_perusahaan);
    }

    // 🔍 SEARCH (NAMA SAJA)
    if ($search) {
      $query->where('nama_barang', 'like', '%' . $search . '%');
    }

    // PAGINATION
    $kategoris = $query->latest()->paginate(10);

    // 🔥 AMBIL SEMUA PERUSAHAAN (UNTUK DROPDOWN)
    $perusahaans = Perusahaan::all();

    return view('content.dashboard.aset.index', compact('kategoris', 'perusahaans'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    $perusahaanId = $user->role === 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;

    $validatedData = $request->validate(
      [
        'kode_barang' => [
          'required',
          'string',
          'max:10',
          Rule::unique('kategoris')->where(fn($q) => $q->where('perusahaan_id', $perusahaanId)),
        ],

        'nama_barang' => 'required|string|max:50',
      ],
      [
        'kode_barang.unique' => 'Kode barang sudah digunakan pada perusahaan ini.',
      ]
    );

    try {
      $validatedData['kode_barang'] = strtoupper(trim($validatedData['kode_barang']));

      $validatedData['nama_barang'] = strtoupper(trim($validatedData['nama_barang']));

      // SET PERUSAHAAN
      if ($user->role === 'super_admin') {
        $validatedData['perusahaan_id'] = $request->perusahaan_id;
      } else {
        $validatedData['perusahaan_id'] = $user->id_perusahaan;
      }

      Kategori::create($validatedData);

      return redirect()
        ->route('aset.index')
        ->with('success', 'Data aset berhasil disimpan.');
    } catch (\Exception $e) {
      return back()->with('error', $e->getMessage());
    }
  }

  // Update the specified resource in storage.
  public function update(Request $request, int $id)
  {
    $kategori = Kategori::findOrFail($id);

    $validatedData = $request->validate(
      [
        'kode_barang' => [
          'required',
          'string',
          'max:10',
          Rule::unique('kategoris')
            ->where(fn($q) => $q->where('perusahaan_id', $kategori->perusahaan_id))
            ->ignore($kategori->id),
        ],

        'nama_barang' => 'required|string|max:50',
      ],
      [
        'kode_barang.unique' => 'Kode barang sudah digunakan pada perusahaan ini.',
      ]
    );

    $validatedData['kode_barang'] = strtoupper(trim($validatedData['kode_barang']));

    $validatedData['nama_barang'] = strtoupper(trim($validatedData['nama_barang']));

    $kategori->update($validatedData);

    return redirect()
      ->route('aset.index')
      ->with('success', 'Data berhasil diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(int $id)
  {
    $kategori = Kategori::findOrFail($id);
    $kategori->delete();

    return back()->with('success', 'Data berhasil dihapus.');
  }
}

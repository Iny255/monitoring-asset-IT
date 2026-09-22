<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
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
      $query = Kategori::select(
          'kategoris.nama_barang',
          DB::raw('MIN(kategoris.kode_barang) as kode_barang'),
          DB::raw('MIN(kategoris.id) as id'),
          DB::raw('COUNT(DISTINCT kategoris.perusahaan_id) as total_perusahaan'),
          DB::raw('GROUP_CONCAT(DISTINCT perusahaans.nama_perusahaan ORDER BY perusahaans.nama_perusahaan ASC SEPARATOR "||") as daftar_perusahaan')
        )
        ->leftJoin('perusahaans', 'perusahaans.id', '=', 'kategoris.perusahaan_id')
        ->groupBy('kategoris.nama_barang');

      if ($search) {
        $query->where(function ($q) use ($search) {
          $q->where('kategoris.nama_barang', 'like', "%{$search}%")
            ->orWhere('kategoris.kode_barang', 'like', "%{$search}%");
        });
      }

      $kategoris = $query->orderBy('kategoris.nama_barang', 'asc')
        ->paginate(10)
        ->appends($request->query());
    } else {
      $query = Kategori::with('perusahaan');

      if ($user->role !== 'super_admin') {
        $query->where('perusahaan_id', $user->id_perusahaan);
      } elseif ($perusahaanId) {
        $query->where('perusahaan_id', $perusahaanId);
      }

      if ($search) {
        $query->where(function ($q) use ($search) {
          $q->where('nama_barang', 'like', "%{$search}%")
            ->orWhere('kode_barang', 'like', "%{$search}%");
        });
      }

      $kategoris = $query->latest()->paginate(10)->appends($request->query());
    }

    return view('content.dashboard.aset.index', compact('kategoris', 'perusahaans', 'perusahaanId', 'isGrouped'));
  }

  public function detailPerusahaan(Request $request)
  {
    $request->validate([
      'nama_barang' => 'required',
    ]);

    $data = Kategori::with('perusahaan')
      ->withCount('dataAsets')
      ->where('nama_barang', $request->nama_barang)
      ->orderBy('perusahaan_id')
      ->get();

    return response()->json($data);
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
    try {
      $kategori->delete();
      return back()->with('success', 'Data berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal menghapus: Kategori ini masih digunakan pada data merek/tipe atau transaksi aset.');
    }
  }
}

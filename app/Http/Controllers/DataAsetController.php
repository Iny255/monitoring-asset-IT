<?php

namespace App\Http\Controllers;

use App\Models\DataAset;
use App\Models\Kategori;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    $isGrouped = ($user->role === 'super_admin' && empty($perusahaanId));

    if ($isGrouped) {
      $query = DataAset::select(
          'data_asets.merek',
          'data_asets.type',
          DB::raw('MIN(kategoris.nama_barang) as nama_barang'),
          DB::raw('MIN(data_asets.warna) as warna'),
          DB::raw('MIN(data_asets.id) as id'),
          DB::raw('COUNT(DISTINCT data_asets.perusahaan_id) as total_perusahaan'),
          DB::raw('GROUP_CONCAT(DISTINCT perusahaans.nama_perusahaan ORDER BY perusahaans.nama_perusahaan ASC SEPARATOR "||") as daftar_perusahaan')
        )
        ->leftJoin('kategoris', 'kategoris.id', '=', 'data_asets.kategori_id')
        ->leftJoin('perusahaans', 'perusahaans.id', '=', 'data_asets.perusahaan_id')
        ->groupBy('data_asets.merek', 'data_asets.type');

      if ($search) {
        $query->where(function ($q) use ($search) {
          $q->where('data_asets.merek', 'like', "%{$search}%")
            ->orWhere('data_asets.type', 'like', "%{$search}%")
            ->orWhere('kategoris.nama_barang', 'like', "%{$search}%");
        });
      }

      $dataAsets = $query->orderBy('data_asets.merek', 'asc')
        ->paginate(10)
        ->appends($request->query());
    } else {
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
    }

    if ($user->role == 'super_admin') {
      $kategoris = Kategori::with('perusahaan')->orderBy('nama_barang')->get();
    } else {
      $kategoris = Kategori::where('perusahaan_id', $user->id_perusahaan)
        ->orderBy('nama_barang')
        ->get();
    }

    return view('content.dashboard.data-aset.index', compact('dataAsets', 'kategoris', 'perusahaans', 'perusahaanId', 'isGrouped'));
  }

  public function detailPerusahaan(Request $request)
  {
    $request->validate([
      'merek' => 'required',
      'type' => 'required',
    ]);

    $data = DataAset::with(['perusahaan', 'kategori'])
      ->withCount('masuks')
      ->where('merek', $request->merek)
      ->where('type', $request->type)
      ->orderBy('perusahaan_id')
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

    try {
      $aset->delete();
      return back()->with('success', 'Data aset berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal menghapus: Merek & Tipe ini masih digunakan dalam transaksi/inventaris aset.');
    }
  }
  public function getKategori(string $id)
  {
    if ($id === 'all' || $id === '0' || empty($id)) {
      $kategoris = Kategori::select('nama_barang', DB::raw('MIN(id) as id'))
        ->groupBy('nama_barang')
        ->orderBy('nama_barang')
        ->get();
    } else {
      $kategoris = Kategori::where('perusahaan_id', $id)
        ->orderBy('nama_barang')
        ->get();
    }

    return response()->json($kategoris);
  }
}

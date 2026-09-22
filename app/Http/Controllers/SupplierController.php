<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
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
      $query = Supplier::select(
          'suppliers.nama_supplier',
          DB::raw('MIN(suppliers.telepon) as telepon'),
          DB::raw('MIN(suppliers.alamat) as alamat'),
          DB::raw('MIN(suppliers.id) as id'),
          DB::raw('COUNT(DISTINCT suppliers.perusahaan_id) as total_perusahaan'),
          DB::raw('GROUP_CONCAT(DISTINCT perusahaans.nama_perusahaan ORDER BY perusahaans.nama_perusahaan ASC SEPARATOR "||") as daftar_perusahaan')
        )
        ->leftJoin('perusahaans', 'perusahaans.id', '=', 'suppliers.perusahaan_id')
        ->groupBy('suppliers.nama_supplier');

      if ($search) {
        $query->where(function ($q) use ($search) {
          $q->where('suppliers.nama_supplier', 'like', "%{$search}%")
            ->orWhere('suppliers.telepon', 'like', "%{$search}%")
            ->orWhere('suppliers.alamat', 'like', "%{$search}%");
        });
      }

      $suppliers = $query->orderBy('suppliers.nama_supplier', 'asc')
        ->paginate(10)
        ->appends($request->query());
    } else {
      $query = Supplier::with('perusahaan');

      if ($user->role !== 'super_admin') {
        $query->where('perusahaan_id', $user->id_perusahaan);
      } elseif ($perusahaanId) {
        $query->where('perusahaan_id', $perusahaanId);
      }

      if ($search) {
        $query->where(function ($q) use ($search) {
          $q->where('nama_supplier', 'like', "%{$search}%")
            ->orWhere('telepon', 'like', "%{$search}%")
            ->orWhere('alamat', 'like', "%{$search}%");
        });
      }

      $suppliers = $query->latest()->paginate(10)->appends($request->query());
    }

    return view('content.dashboard.supplier.index', compact('suppliers', 'perusahaans', 'perusahaanId', 'isGrouped'));
  }

  public function detailPerusahaan(Request $request)
  {
    $request->validate([
      'nama_supplier' => 'required',
    ]);

    $data = Supplier::with('perusahaan')
      ->withCount('masuks')
      ->where('nama_supplier', $request->nama_supplier)
      ->orderBy('perusahaan_id')
      ->get();

    return response()->json($data);
  }

  /**
   * Store a newly created resource.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    if (!$request->filled('telepon') && $request->filled('no_hp')) {
      $request->merge(['telepon' => $request->no_hp]);
    }

    $validated = $request->validate([
      'nama_supplier' => 'required|max:100',
      'telepon' => 'nullable|max:30',
      'alamat' => 'nullable|max:255',
    ]);

    $validated['nama_supplier'] = strtoupper($validated['nama_supplier']);
    $validated['telepon'] = !empty($validated['telepon']) ? trim($validated['telepon']) : null;
    $validated['alamat'] = !empty($validated['alamat']) ? strtoupper($validated['alamat']) : null;
    try {
      if ($user->role === 'super_admin') {
        $validated['perusahaan_id'] = $request->perusahaan_id;
      } else {
        $validated['perusahaan_id'] = $user->id_perusahaan;
      }

      Supplier::create($validated);

      return redirect()
        ->route('supplier.index')
        ->with('success', 'Supplier berhasil ditambahkan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()
        ->withInput()
        ->with('error', $e->getMessage());
    }
  }

  /**
   * Update the specified resource.
   */
  public function update(Request $request, string $id)
  {
    $user = auth()->user();

    $supplier = Supplier::findOrFail($id);

    if (!$request->filled('telepon') && $request->filled('no_hp')) {
      $request->merge(['telepon' => $request->no_hp]);
    }

    $validated = $request->validate([
      'nama_supplier' => 'required|max:100',
      'telepon' => 'nullable|max:30',
      'alamat' => 'nullable|max:255',
    ]);

    $validated['nama_supplier'] = strtoupper($validated['nama_supplier']);
    $validated['telepon'] = !empty($validated['telepon']) ? trim($validated['telepon']) : null;
    $validated['alamat'] = !empty($validated['alamat']) ? strtoupper($validated['alamat']) : null;

    if ($user->role === 'super_admin' && $request->filled('perusahaan_id')) {
      $validated['perusahaan_id'] = $request->perusahaan_id;
    }

    $supplier->update($validated);

    return redirect()
      ->route('supplier.index')
      ->with('success', 'Supplier berhasil diperbarui.');
  }

  /**
   * Remove the specified resource.
   */
  public function destroy(string $id)
  {
    $supplier = Supplier::findOrFail($id);

    try {
      $supplier->delete();

      return redirect()
        ->route('supplier.index')
        ->with('success', 'Supplier berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal menghapus: Supplier ini masih digunakan pada riwayat penerimaan aset.');
    }
  }
}

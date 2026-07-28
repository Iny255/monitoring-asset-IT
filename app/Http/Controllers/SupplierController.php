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

    if ($user->role === 'super_admin') {
      if ($perusahaanId) {
        // Menampilkan supplier perusahaan tertentu
        $suppliers = Supplier::with('perusahaan')->where('perusahaan_id', $perusahaanId);
      } else {
        // Menampilkan supplier unik semua perusahaan
        $suppliers = Supplier::select(DB::raw('MIN(id) as id'), 'nama_supplier', 'telepon', 'alamat')
          ->selectRaw('COUNT(DISTINCT perusahaan_id) as total_perusahaan')
          ->selectRaw('MAX(created_at) as created_at')
          ->groupBy('nama_supplier', 'telepon', 'alamat');
      }
    } else {
      $suppliers = Supplier::with('perusahaan')->where('perusahaan_id', $user->id_perusahaan);
    }

    if ($search) {
      $suppliers->where(function ($q) use ($search) {
        $q->where('nama_supplier', 'like', "%{$search}%")
          ->orWhere('telepon', 'like', "%{$search}%")
          ->orWhere('alamat', 'like', "%{$search}%");
      });
    }

    $suppliers = $suppliers
      ->latest()
      ->paginate(10)
      ->appends($request->query());

    return view('content.dashboard.supplier.index', compact('suppliers', 'perusahaans', 'perusahaanId'));
  }
  public function detailPerusahaan(Request $request)
  {
    $request->validate([
      'nama_supplier' => 'required',
    ]);

    return Supplier::with('perusahaan')
      ->where('nama_supplier', $request->nama_supplier)
      ->orderBy('perusahaan_id')
      ->get();
  }

  /**
   * Store a newly created resource.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    $validated = $request->validate([
      'nama_supplier' => 'required|max:100',
      'telepon' => 'nullable|max:30',
      'alamat' => 'nullable|max:255',
    ]);

    $validated['nama_supplier'] = strtoupper($validated['nama_supplier']);
    $validated['telepon'] = !empty($validated['telepon']) ? strtoupper($validated['telepon']) : null;
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

    $validated = $request->validate([
      'nama_supplier' => 'required|max:100',
      'telepon' => 'nullable|max:30',
      'alamat' => 'nullable|max:255',
    ]);

    $validated['nama_supplier'] = strtoupper($validated['nama_supplier']);
    $validated['telepon'] = !empty($validated['telepon']) ? strtoupper($validated['telepon']) : null;
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

    $supplier->delete();

    return redirect()
      ->route('supplier.index')
      ->with('success', 'Supplier berhasil dihapus.');
  }
}

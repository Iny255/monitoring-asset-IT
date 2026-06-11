<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    if ($user->role === 'super_admin') {
      $suppliers = Supplier::with('perusahaan');

      if ($perusahaanId) {
        $suppliers->where('perusahaan_id', $perusahaanId);
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

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    return view('content.dashboard.supplier.index', compact('suppliers', 'perusahaans'));
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

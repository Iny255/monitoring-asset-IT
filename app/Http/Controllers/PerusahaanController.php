<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class PerusahaanController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $search = $request->input('search');

    $perusahaans = Perusahaan::latest();

    if ($search) {
      $perusahaans = $perusahaans->where(function ($query) use ($search) {
        $query->where('nama_perusahaan', 'like', '%' . $search . '%')
          ->orWhere('id', 'like', '%' . $search . '%');
      });
    }

    $perusahaans = $perusahaans->paginate(5);

    // 🔥 GENERATE KODE DI INDEX
    $last = Perusahaan::orderBy('kode_perusahaan', 'desc')->first();

    if ($last) {
      $number = (int) substr($last->kode_perusahaan, 2) + 1;
    } else {
      $number = 1;
    }

    $kodePerusahaan = 'PT' . str_pad($number, 4, '0', STR_PAD_LEFT);

    return view(
      'content.dashboard.perusahaan.index',
      compact('perusahaans', 'kodePerusahaan')
    );
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
    $validatedData = $request->validate([
      'kode_perusahaan' => 'required|string|unique:perusahaans,kode_perusahaan',
      'nama_perusahaan' => 'required|string|max:50',
    ]);

    try {
      $perusahaan = Perusahaan::create($validatedData);

      if ($perusahaan) {
        return redirect('/dashboard/perusahaan')->with('success', 'Data perusahaan berhasil disimpan.');
      } else {
        return redirect('/dashboard/perusahaan')->with('error', 'Gagal menyimpan data perusahaan.');
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return redirect('/dashboard/perusahaan')
       ->with('error', 'Data perusahaan tidak berhasil disimpan.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Perusahaan $perusahaan)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Perusahaan $perusahaan)
  {
    return view('content.dashboard.perusahaan.edit', compact('perusahaan'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Perusahaan $perusahaan)
  {
    $validated = $request->validate([
      'nama_perusahaan' => 'required|string|max:50',
    ]);

    try {
      $perusahaan->update($validated);

      return redirect()
        ->route('perusahaan.index')
        ->with('success', 'Data perusahaan berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()->with('error', 'Gagal mengupdate data.');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $perusahaan = Perusahaan::findOrFail($id);
    $perusahaan->delete();

    return redirect()->back()
      ->with('success', 'Perusahaan berhasil dihapus');
  }
}

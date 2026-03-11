<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LokasiController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $search = $request->input('search');

    $lokasis = Lokasi::latest();

    if ($search) {
      $lokasis = $lokasis->where(function ($query) use ($search) {
        $query->where('nama_lokasi', 'like', '%' . $search . '%')
          ->orWhere('id', 'like', '%' . $search . '%');
      });
    }

    $lokasis = $lokasis->paginate(5);

    // 🔥 GENERATE KODE LOKASI DI INDEX
    $last = Lokasi::orderBy('kode_lokasi', 'desc')->first();

    if ($last) {
      $number = (int) substr($last->kode_lokasi, 2) + 1;
    } else {
      $number = 1;
    }

    $kodeLokasi = 'LK' . str_pad($number, 4, '0', STR_PAD_LEFT);

    return view('content.dashboard.lokasi.index', compact('lokasis', 'kodeLokasi'));
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
      'kode_lokasi' => 'required|string|unique:lokasis,kode_lokasi',
      'nama_lokasi' => 'required|string|max:50',
    ]);

    try {
      $lokasi = Lokasi::create($validatedData);

      if ($lokasi) {
        return redirect('/dashboard/lokasi')->with('success', 'Data lokasi berhasil disimpan.');
      } else {
        return redirect('/dashboard/lokasi')->with('error', 'Gagal menyimpan data lokasi.');
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return redirect('/dashboard/lokasi')
        ->with('error', 'Data lokasi tidak berhasil disimpan.');
    }
  }


  /**
   * Display the specified resource.
   */
  public function show(Lokasi $lokasi)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Lokasi $lokasi)
  {
    return view('content.dashboard.lokasi.edit', compact('lokasi'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Lokasi $lokasi)
  {
    $validated = $request->validate([
      'nama_lokasi' => 'required|string|max:50',
    ]);

    try {
      $lokasi->update($validated);

      return redirect()
        ->route('lokasi.index')
        ->with('success', 'Data lokasi diperbarui.');
    } catch (\Exception $e) {
      return back()->with('error', 'Gagal mengupdate data.');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $lokasi = Lokasi::findOrFail($id);
    $lokasi->delete();

    return redirect()->back()
      ->with('success', 'Lokasi berhasil dihapus');
  }
}

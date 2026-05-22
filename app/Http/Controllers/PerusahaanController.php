<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerusahaanController extends Controller
{
  public function __construct()
  {
    $this->middleware(function ($request, $next) {
      if (auth()->user()->role !== 'super_admin') {
        abort(403); // atau 404
      }

      return $next($request);
    });
  }

  public function index(Request $request)
  {
    $search = $request->input('search');

    $perusahaans = Perusahaan::query();

    if ($search) {
      $perusahaans->where(function ($query) use ($search) {
        $query
          ->where('nama_perusahaan', 'like', '%' . $search . '%')
          ->orWhere('kode_perusahaan', 'like', '%' . $search . '%');
      });
    }

    $perusahaans = $perusahaans->latest()->paginate(5);

    // 🔥 Generate next kode for modal
    $last = Perusahaan::orderBy('id', 'desc')->first();
    if ($last && $last->kode_perusahaan) {
      $number = (int) substr($last->kode_perusahaan, 2) + 1;
    } else {
      $number = 1;
    }
    $kodePerusahaan = 'PT' . str_pad($number, 4, '0', STR_PAD_LEFT);

    return view('content.dashboard.perusahaan.index', compact('perusahaans', 'kodePerusahaan'));
  }

  public function store(Request $request)
  {
    // =====================================
    // VALIDASI
    // =====================================

    $validated = $request->validate([
      'nama_perusahaan' => 'required|string|max:50',
    ]);

    try {
      // =====================================
      // UPPERCASE
      // =====================================

      $validated['nama_perusahaan'] = strtoupper($validated['nama_perusahaan']);

      // =====================================
      // AMBIL DATA TERAKHIR
      // =====================================

      $last = Perusahaan::latest('id')->first();

      // =====================================
      // GENERATE KODE
      // =====================================

      if ($last && $last->kode_perusahaan) {
        $number = (int) substr($last->kode_perusahaan, 2) + 1;
      } else {
        $number = 1;
      }

      // =====================================
      // FORMAT KODE
      // =====================================

      $kode = 'PT' . str_pad($number, 4, '0', STR_PAD_LEFT);

      // =====================================
      // SIMPAN
      // =====================================

      Perusahaan::create([
        'kode_perusahaan' => strtoupper($kode),

        'nama_perusahaan' => $validated['nama_perusahaan'],

        // DEFAULT
        'logo' => null,

        'primary_color' => '#007bff',

        'secondary_color' => '#6c757d',
      ]);

      return redirect()
        ->route('perusahaan.index')
        ->with('success', 'Data perusahaan berhasil disimpan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()
        ->withInput()
        ->with('error', 'Gagal menyimpan data perusahaan.');
    }
  }

  public function edit(Perusahaan $perusahaan)
  {
    return view('content.dashboard.perusahaan.edit', compact('perusahaan'));
  }

  public function update(Request $request, Perusahaan $perusahaan)
  {
    // =====================================
    // VALIDASI
    // =====================================

    $validated = $request->validate([
      'nama_perusahaan' => 'required|string|max:50',
    ]);

    try {
      // =====================================
      // UPPERCASE
      // =====================================

      $validated['nama_perusahaan'] = strtoupper($validated['nama_perusahaan']);

      // =====================================
      // UPDATE
      // =====================================

      $perusahaan->update([
        'nama_perusahaan' => $validated['nama_perusahaan'],

        // PERTAHANKAN DATA LAMA
        'logo' => $perusahaan->logo,

        'primary_color' => $perusahaan->primary_color,

        'secondary_color' => $perusahaan->secondary_color,
      ]);

      return redirect()
        ->route('perusahaan.index')
        ->with('success', 'Data berhasil diperbarui');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()
        ->withInput()
        ->with('error', 'Gagal update data');
    }
  }

  public function destroy(int $id)
  {
    Perusahaan::findOrFail($id)->delete();

    return back()->with('success', 'Perusahaan berhasil dihapus');
  }
}

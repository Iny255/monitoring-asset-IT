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
    $search = $request->search;

    $query = Perusahaan::query();

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('nama_perusahaan', 'like', "%{$search}%");
      });
    }

    $perusahaans = $query->latest()->paginate(10);

    $last = Perusahaan::latest('id')->first();

    if ($last) {
      $kodePerusahaan = str_pad(((int) $last->kode_perusahaan) + 1, 2, '0', STR_PAD_LEFT);
    } else {
      $kodePerusahaan = '01';
    }

    return view('content.dashboard.perusahaan.index', compact('perusahaans', 'kodePerusahaan'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'kode_perusahaan' => 'required|max:2|unique:perusahaans,kode_perusahaan',
      'nama_perusahaan' => 'required|max:100',
      'primary_color' => 'required',
      'secondary_color' => 'required',
      'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    Perusahaan::create([
      'kode_perusahaan' => $validated['kode_perusahaan'],
      'nama_perusahaan' => strtoupper($validated['nama_perusahaan']),
      'primary_color' => $validated['primary_color'],
      'secondary_color' => $validated['secondary_color'],
      'logo' => $logoPath ?? null,
    ]);

    return back()->with('success', 'Perusahaan berhasil ditambahkan');
  }

  public function edit(Perusahaan $perusahaan)
  {
    return view('content.dashboard.perusahaan.edit', compact('perusahaan'));
  }

  public function update(Request $request, Perusahaan $perusahaan)
  {
    $validated = $request->validate([
      'nama_perusahaan' => 'required|max:100',

      'primary_color' => 'required',

      'secondary_color' => 'required',
    ]);

    $perusahaan->update([
      'nama_perusahaan' => strtoupper($validated['nama_perusahaan']),

      'primary_color' => $validated['primary_color'],

      'secondary_color' => $validated['secondary_color'],
    ]);

    return back()->with('success', 'Data berhasil diperbarui');
  }

  public function destroy(int $id)
  {
    $perusahaan = Perusahaan::findOrFail($id);

    $perusahaan->update([
      'is_active' => false,
    ]);

    return back()->with('success', 'Perusahaan dinonaktifkan');
  }
}

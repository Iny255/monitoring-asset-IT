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

    $query = Perusahaan::with(['parent', 'cabangs']);

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('nama_perusahaan', 'like', "%{$search}%")
          ->orWhere('kode_perusahaan', 'like', "%{$search}%");
      });
    }

    $perusahaans = $query->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, id')->paginate(15)->appends($request->query());
    $parentPerusahaans = Perusahaan::whereNull('parent_id')->orderBy('nama_perusahaan')->get();

    $last = Perusahaan::latest('id')->first();

    if ($last) {
      $kodePerusahaan = str_pad(((int) $last->kode_perusahaan) + 1, 2, '0', STR_PAD_LEFT);
    } else {
      $kodePerusahaan = '01';
    }

    return view('content.dashboard.perusahaan.index', compact('perusahaans', 'parentPerusahaans', 'kodePerusahaan'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'kode_perusahaan' => 'required|max:10|unique:perusahaans,kode_perusahaan',
      'nama_perusahaan' => 'required|max:100',
      'tipe' => 'required|in:Induk,Cabang',
      'parent_id' => 'nullable|required_if:tipe,Cabang|exists:perusahaans,id',
      'primary_color' => 'required',
      'secondary_color' => 'required',
      'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $logoPath = null;
    if ($request->hasFile('logo')) {
      $logoPath = $request->file('logo')->store('perusahaan', 'public');
    }

    Perusahaan::create([
      'kode_perusahaan' => $validated['kode_perusahaan'],
      'nama_perusahaan' => strtoupper($validated['nama_perusahaan']),
      'tipe' => $validated['tipe'],
      'parent_id' => $validated['tipe'] === 'Cabang' ? $validated['parent_id'] : null,
      'primary_color' => $validated['primary_color'],
      'secondary_color' => $validated['secondary_color'],
      'logo' => $logoPath,
    ]);

    return back()->with('success', 'Perusahaan/Cabang berhasil ditambahkan');
  }

  public function edit(Perusahaan $perusahaan)
  {
    return view('content.dashboard.perusahaan.edit', compact('perusahaan'));
  }

  public function update(Request $request, Perusahaan $perusahaan)
  {
    $validated = $request->validate([
      'nama_perusahaan' => 'required|max:100',
      'tipe' => 'required|in:Induk,Cabang',
      'parent_id' => 'nullable|required_if:tipe,Cabang|exists:perusahaans,id',
      'primary_color' => 'required',
      'secondary_color' => 'required',
      'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = [
      'nama_perusahaan' => strtoupper($validated['nama_perusahaan']),
      'tipe' => $validated['tipe'],
      'parent_id' => $validated['tipe'] === 'Cabang' ? $validated['parent_id'] : null,
      'primary_color' => $validated['primary_color'],
      'secondary_color' => $validated['secondary_color'],
    ];

    if ($request->hasFile('logo')) {
      $data['logo'] = $request->file('logo')->store('perusahaan', 'public');
    }

    $perusahaan->update($data);

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

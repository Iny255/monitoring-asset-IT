<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AccessController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $search = $request->search;
    $kategori = $request->kategori;
    $jenis = $request->jenis;
    $status = $request->status;

    $query = Access::query();

    if ($search) {
      $query->where('nama_akses', 'like', "%{$search}%");
    }

    if ($kategori) {
      $query->where('kategori', $kategori);
    }

    if ($jenis) {
      $query->where('jenis', $jenis);
    }

    if ($status) {
      $query->where('status', $status);
    }

    $accesses = $query
      ->latest()
      ->paginate(10)
      ->appends($request->except('page'));

    return view('content.dashboard.hak_akses.index', compact('accesses'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'kategori' => 'required|in:Aplikasi,Hak Akses',
      'jenis' => 'required|in:Software,PPN,NON PPN',
      'nama_akses' => [
        'required',
        'string',
        'max:150',

        Rule::unique('accesses')->where(function ($query) use ($request) {
          return $query->where('kategori', $request->kategori)->where('jenis', $request->jenis);
        }),
      ],
      'status' => 'required|in:aktif,nonaktif',
    ]);

    // Validasi kombinasi kategori & jenis
    if ($request->kategori == 'Aplikasi' && $request->jenis != 'Software') {
      return back()
        ->withErrors([
          'jenis' => 'Kategori Aplikasi hanya boleh menggunakan jenis Software.',
        ])
        ->withInput();
    }

    if ($request->kategori == 'Hak Akses' && !in_array($request->jenis, ['PPN', 'NON PPN'])) {
      return back()
        ->withErrors([
          'jenis' => 'Kategori Hak Akses hanya boleh menggunakan jenis PPN atau NON PPN.',
        ])
        ->withInput();
    }

    try {
      Access::create([
        'kategori' => $request->kategori,
        'jenis' => $request->jenis,
        'nama_akses' => strtoupper($request->nama_akses),
        'status' => $request->status,
      ]);

      return back()->with('success', 'Hak Akses & Aplikasi berhasil ditambahkan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menyimpan data.');
    }
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */

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
    $access = Access::findOrFail($id);

    $request->validate([
      'kategori' => 'required|in:Aplikasi,Hak Akses',
      'jenis' => 'required|in:Software,PPN,NON PPN',
      'nama_akses' => [
        'required',

        'string',

        'max:150',

        Rule::unique('accesses')
          ->ignore($id)
          ->where(function ($query) use ($request) {
            return $query->where('kategori', $request->kategori)->where('jenis', $request->jenis);
          }),
      ],
      'status' => 'required|in:aktif,nonaktif',
    ]);

    // Validasi kombinasi kategori & jenis
    if ($request->kategori == 'Aplikasi' && $request->jenis != 'Software') {
      return back()
        ->withErrors([
          'jenis' => 'Kategori Aplikasi hanya boleh menggunakan jenis Software.',
        ])
        ->withInput();
    }

    if ($request->kategori == 'Hak Akses' && !in_array($request->jenis, ['PPN', 'NON PPN'])) {
      return back()
        ->withErrors([
          'jenis' => 'Kategori Hak Akses hanya boleh menggunakan jenis PPN atau NON PPN.',
        ])
        ->withInput();
    }

    try {
      $access->update([
        'kategori' => $request->kategori,
        'jenis' => $request->jenis,
        'nama_akses' => strtoupper($request->nama_akses),
        'status' => $request->status,
      ]);

      return back()->with('success', 'Hak Akses & Aplikasi berhasil diperbarui.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal memperbarui data.');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      $access = Access::findOrFail($id);

      $access->delete();

      return back()->with('success', 'Hak Akses & Aplikasi berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menghapus data.');
    }
  }
}

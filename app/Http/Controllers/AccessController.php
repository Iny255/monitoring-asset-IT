<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Perusahaan;
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
    $user = auth()->user();

    $query = Access::with('perusahaan');

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    if ($user->role == 'super_admin') {
      if ($request->filled('perusahaan')) {
        $query->where('id_perusahaan', $request->perusahaan);
      }
    } /*
    |--------------------------------------------------------------------------
    | PETUGAS
    |--------------------------------------------------------------------------
    */ else {
      $query->where('id_perusahaan', $user->id_perusahaan);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    if ($request->filled('jenis')) {
      $query->where('jenis', $request->jenis);
    }

    if ($request->filled('kategori')) {
      $query->where('kategori', $request->kategori);
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('nama_akses', 'like', "%{$search}%")->orWhere('keterangan', 'like', "%{$search}%");
      });
    }

    $accesses = $query
      ->orderBy('nama_akses')
      ->paginate(15, ['*'], 'page', request()->input('page', 1))
      ->appends(request()->query());

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    return view('content.dashboard.hak_akses.index', compact('accesses', 'perusahaans'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    $rules = [
      'kategori' => 'required|in:Aplikasi,Hak Akses',
      'jenis' => 'required|in:Software,PPN,NON PPN',
      'status' => 'required|in:aktif,nonaktif',
      'nama_akses' => ['required', 'string', 'max:150'],
    ];

    if ($user->role == 'super_admin') {
      $rules['id_perusahaan'] = 'required|exists:perusahaans,id';
    }

    $request->validate($rules);

    /*
    |--------------------------------------------------------------------------
    | VALIDASI KATEGORI
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    $idPerusahaan = $user->role == 'super_admin' ? $request->id_perusahaan : $user->id_perusahaan;

    /*
    |--------------------------------------------------------------------------
    | DUPLIKAT
    |--------------------------------------------------------------------------
    */

    $cek = Access::where('id_perusahaan', $idPerusahaan)
      ->where('kategori', $request->kategori)
      ->where('jenis', $request->jenis)
      ->where('nama_akses', strtoupper($request->nama_akses))
      ->exists();

    if ($cek) {
      return back()
        ->withInput()
        ->withErrors([
          'nama_akses' => 'Hak Akses sudah ada pada perusahaan tersebut.',
        ]);
    }

    try {
      Access::create([
        'id_perusahaan' => $idPerusahaan,
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
    $user = auth()->user();
    if ($user->role == 'super_admin') {
      $request->validate([
        'id_perusahaan' => 'required|exists:perusahaans,id',
      ]);
    }

    if ($user->role != 'super_admin' && $access->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $request->validate([
      'kategori' => ['required', 'in:Aplikasi,Hak Akses'],
      'jenis' => ['required', 'in:Software,PPN,NON PPN'],
      'nama_akses' => ['required', 'string', 'max:150'],
      'status' => ['required', 'in:aktif,nonaktif'],
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
    $idPerusahaan = $user->role == 'super_admin' ? $request->id_perusahaan : $user->id_perusahaan;

    $cek = Access::where('id_perusahaan', $idPerusahaan)
      ->where('kategori', $request->kategori)
      ->where('jenis', $request->jenis)
      ->where('nama_akses', strtoupper($request->nama_akses))
      ->where('id', '!=', $access->id)
      ->exists();

    if ($cek) {
      return back()
        ->withInput()
        ->withErrors([
          'nama_akses' => 'Hak Akses sudah ada pada perusahaan tersebut.',
        ]);
    }

    try {
      $access->update([
        'id_perusahaan' => $idPerusahaan,
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

      $user = auth()->user();

      if ($user->role != 'super_admin' && $access->id_perusahaan != $user->id_perusahaan) {
        abort(403);
      }

      $access->delete();

      return back()->with('success', 'Hak Akses & Aplikasi berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menghapus data.');
    }
  }
  public function filterJenis(Request $request, $jenis)
  {
    try {
      $user = auth()->user();

      $query = Access::where('jenis', $jenis)->where('status', 'aktif');

      if ($user->role == 'super_admin') {
        if ($request->filled('perusahaan')) {
          $query->where('id_perusahaan', $request->perusahaan);
        } else {
          return response()->json([]);
        }
      } else {
        $query->where('id_perusahaan', $user->id_perusahaan);
      }

      return response()->json($query->orderBy('nama_akses')->get(['id', 'nama_akses']));
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return response()->json([], 500);
    }
  }
}

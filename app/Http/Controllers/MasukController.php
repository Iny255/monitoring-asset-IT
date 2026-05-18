<?php

namespace App\Http\Controllers;

use App\Models\Masuk;
use App\Models\Kategori;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
class MasukController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();

    if (!$user) {
      abort(403);
    }

    $search = $request->search;
    $perusahaanId = $request->perusahaan_id;

    // 🔥 FIX: wajib ada ini
    $perusahaans = $user->role === 'super_admin' ? \App\Models\Perusahaan::all() : collect();

    $masuks = Masuk::with(['kategori', 'perusahaan']);

    // 🔥 FILTER ROLE
    if ($user->role !== 'super_admin') {
      $masuks->where('perusahaan_id', $user->id_perusahaan);
    } else {
      if ($perusahaanId) {
        $masuks->where('perusahaan_id', $perusahaanId);
      }
    }

    // 🔍 SEARCH
    if ($search) {
      $masuks->where(function ($query) use ($search) {
        $query->where('kode_masuk', 'like', "%{$search}%")->orWhereHas('kategori', function ($q) use ($search) {
          $q->where('nama_barang', 'like', "%{$search}%");
        });
      });
    }

    $masuks = $masuks
      ->latest()
      ->paginate(5)
      ->appends($request->query());

    // 🔥 FIX: kirim ke view
    return view('content.dashboard.transaksi-masuk.index', compact('masuks', 'perusahaans'));
  }

  public function create()
  {
    $user = auth()->user();

    if ($user->role === 'super_admin') {
      // 🔥 ambil semua perusahaan
      $perusahaans = Perusahaan::all();

      $kategoris = collect(); // kosong dulu
      $kodeMasuk = null;
    } else {
      $perusahaanId = $user->id_perusahaan;

      $perusahaans = [];

      $kategoris = Kategori::where('perusahaan_id', $perusahaanId)->get();

      $last = Masuk::where('perusahaan_id', $perusahaanId)
        ->orderBy('id', 'desc')
        ->first();

      $number = $last ? (int) substr($last->kode_masuk, 4) + 1 : 1;

      $kodeMasuk = 'MSK-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    return view('content.dashboard.transaksi-masuk.create', compact('kategoris', 'kodeMasuk', 'perusahaans'));
  }

  public function store(Request $request)
  {
    $user = auth()->user();

    // 🔥 tentukan perusahaan
    $perusahaanId = $user->role === 'super_admin' ? $request->id_perusahaan : $user->id_perusahaan;

    $validated = $request->validate([
      'kode_masuk' => ['required', Rule::unique('masuks')->where(fn($q) => $q->where('perusahaan_id', $perusahaanId))],
      'id_kategori' => 'required',
      'type' => 'required|string|max:100',
      'merek' => 'required|string|max:100',
      'jumlah' => 'required|integer|min:1',
      'tgl_beli' => 'required|date',
      'garansi' => 'required|integer',
      'supplier' => 'required|string|max:100',
      'harga' => 'required',
      'gambar' => 'nullable|image|max:2048',
      'id_perusahaan' => $user->role === 'super_admin' ? 'required' : 'nullable',
    ]);

    try {
      $validated['kode_masuk'] = strtoupper($validated['kode_masuk']);

      $validated['type'] = strtoupper($validated['type']);

      $validated['merek'] = strtoupper($validated['merek']);

      $validated['supplier'] = strtoupper($validated['supplier']);

      // 🔥 upload gambar
      if ($request->hasFile('gambar')) {
        $validated['gambar'] = $request->file('gambar')->store('masuk', 'public');
      }

      $validated['perusahaan_id'] = $perusahaanId;

      Masuk::create($validated);

      return redirect()
        ->route('transaksi-masuk.index')
        ->with('success', 'Data berhasil disimpan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menyimpan data.');
    }
  }

  public function show($id)
  {
    $query = Masuk::with('kategori');

    // 🔥 FILTER PERUSAHAAN
    if (auth()->user()->role !== 'super_admin') {
      $query->where('perusahaan_id', auth()->user()->id_perusahaan);
    }

    $masuk = $query->findOrFail($id);

    return view('content.dashboard.transaksi-masuk.show', compact('masuk'));
  }

  public function edit(Masuk $masuk)
  {
    $user = auth()->user();

    // 🔥 SUPER ADMIN
    if ($user->role === 'super_admin') {
      // ambil kategori berdasarkan perusahaan data yg diedit
      $kategoris = Kategori::where('perusahaan_id', $masuk->perusahaan_id)->get();

      // ambil semua perusahaan untuk dropdown
      $perusahaans = Perusahaan::all();
    } else {
      // 🔥 USER BIASA
      $kategoris = Kategori::where('perusahaan_id', $user->id_perusahaan)->get();

      $perusahaans = [];
    }

    return view('content.dashboard.transaksi-masuk.edit', compact('masuk', 'kategoris', 'perusahaans'));
  }

  public function update(Request $request, Masuk $masuk)
  {
    $validated = $request->validate([
      'id_kategori' => 'required|exists:kategoris,id',
      'type' => 'required|string|max:100',
      'merek' => 'required|string|max:100',
      'jumlah' => 'required|integer',
      'tgl_beli' => 'required|date',
      'supplier' => 'required|string|max:100',
      'garansi' => 'required|integer',
      'harga' => 'required|numeric',
      'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('gambar')) {
      if ($masuk->gambar) {
        Storage::delete('public/' . $masuk->gambar);
      }

      $validated['gambar'] = $request->file('gambar')->store('masuk', 'public');
    }

    try {
      $validated['type'] = strtoupper($validated['type']);

      $validated['merek'] = strtoupper($validated['merek']);

      $validated['supplier'] = strtoupper($validated['supplier']);

      // =====================================
      // FORMAT HARGA
      // =====================================

      $validated['harga'] = str_replace('.', '', $validated['harga']);

      $masuk->update($validated);

      return redirect()
        ->route('transaksi-masuk.index')
        ->with('success', 'Data berhasil diupdate');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal update data');
    }
  }

  public function destroy(Masuk $masuk)
  {
    try {
      if ($masuk->gambar) {
        Storage::delete('public/' . $masuk->gambar);
      }

      $masuk->delete();

      return redirect()
        ->route('transaksi-masuk.index')
        ->with('success', 'Data berhasil dihapus');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal hapus data');
    }
  }

  public function download(Masuk $masuk)
  {
    if ($masuk->gambar && Storage::exists('public/' . $masuk->gambar)) {
      return Storage::download('public/' . $masuk->gambar);
    }

    abort(404, 'File tidak ditemukan');
  }

  public function stok(Request $request)
  {
    $user = auth()->user();

    $search = $request->search;

    $query = Masuk::with(['kategori', 'perusahaan'])->where('jumlah', '>', 0);

    // 🔥 ROLE
    if ($user->role !== 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } else {
      // FILTER PERUSAHAAN
      if ($request->perusahaan_id) {
        $query->where('perusahaan_id', $request->perusahaan_id);
      }
    }

    // 🔍 SEARCH
    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('type', 'like', "%{$search}%")
          ->orWhere('merek', 'like', "%{$search}%")
          ->orWhereHas('kategori', function ($k) use ($search) {
            $k->where('nama_barang', 'like', "%{$search}%");
          });
      });
    }

    $stoks = $query->latest()->get();

    // 🔥 FILTER DROPDOWN
    $perusahaans =
      $user->role === 'super_admin' ? \App\Models\Perusahaan::orderBy('nama_perusahaan')->get() : collect();

    return view('content.dashboard.transaksi-masuk.stok', compact('stoks', 'perusahaans'));
  }
  public function getKode($id)
  {
    $last = Masuk::where('perusahaan_id', $id)
      ->orderBy('id', 'desc')
      ->first();

    $number = $last ? (int) substr($last->kode_masuk, 4) + 1 : 1;

    $kode = 'MSK-' . str_pad($number, 5, '0', STR_PAD_LEFT);

    return response()->json([
      'kode' => $kode,
    ]);
  }
  public function getKategori($id)
  {
    $kategoris = \App\Models\Kategori::where('perusahaan_id', $id)->get();

    return response()->json($kategoris);
  }
}

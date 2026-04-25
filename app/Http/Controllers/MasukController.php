<?php

namespace App\Http\Controllers;

use App\Models\Masuk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
class MasukController extends Controller
{
  public function index(Request $request)
  {
    $perusahaan = auth()->user()->perusahaan;
    $search = $request->input('search');

    $masuks = Masuk::with('kategori')
      ->where('perusahaan_id', $perusahaan->id) // ✅ FIX
      ->latest();

    if ($search) {
      $masuks->where(function ($query) use ($search) {
        $query->where('kode_masuk', 'like', "%{$search}%")->orWhereHas('kategori', function ($q) use ($search) {
          $q->where('nama_barang', 'like', "%{$search}%");
        });
      });
    }

    $masuks = $masuks->paginate(10);

    return view('content.dashboard.transaksi-masuk.index', compact('masuks'));
  }

  public function create()
  {
    $perusahaan = auth()->user()->perusahaan;

    $kategoris = Kategori::where('perusahaan_id', $perusahaan->id)->get();

    $last = Masuk::where('perusahaan_id', $perusahaan->id)
      ->orderBy('id', 'desc')
      ->first();

    $number = $last ? ((int) substr($last->kode_masuk, 4)) + 1 : 1;

    $kodeMasuk = 'MSK-' . str_pad($number, 5, '0', STR_PAD_LEFT);

    return view('content.dashboard.transaksi-masuk.create', compact('kategoris', 'kodeMasuk'));
  }

  public function store(Request $request)
  {
    $perusahaan = auth()->user()->perusahaan;

    $validated = $request->validate([
      'kode_masuk' => [
        'required',
        Rule::unique('masuks')->where(fn($q) => $q->where('perusahaan_id', auth()->user()->id_perusahaan)),
      ],
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

    // ✅ FIX UTAMA
    $validated['perusahaan_id'] = $perusahaan->id;

    if ($request->hasFile('gambar')) {
      $validated['gambar'] = $request->file('gambar')->store('masuk', 'public');
    }

    try {
      Masuk::create($validated);

      return redirect()
        ->route('transaksi-masuk.index')
        ->with('success', 'Data berhasil disimpan');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return back()->with('error', 'Gagal menyimpan data');
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
    $perusahaan = auth()->user()->perusahaan;

    $kategoris = Kategori::where('perusahaan_id', $perusahaan->id)->get(); // ✅ FIX

    return view('content.dashboard.transaksi-masuk.edit', compact('masuk', 'kategoris'));
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
    $perusahaan = auth()->user()->perusahaan;
    $search = $request->search;

    $stoks = Masuk::with('kategori')
      ->where('perusahaan_id', $perusahaan->id)
      ->where('jumlah', '>', 0); // hanya tampil yang masih ada stok

    if ($search) {
      $stoks->whereHas('kategori', function ($q) use ($search) {
        $q->where('nama_barang', 'like', "%{$search}%");
      });
    }

    $stoks = $stoks->get();

    return view('content.dashboard.transaksi-masuk.stok', compact('stoks'));
  }
}

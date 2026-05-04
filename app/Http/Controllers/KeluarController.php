<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Keluar;
use App\Models\Masuk;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
class KeluarController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Keluar::with(['masuk.kategori', 'karyawan', 'perusahaan']);

    // 🔥 FIX ROLE
    if ($user->role !== 'super_admin') {
      $query->where('id_perusahaan', $user->id_perusahaan);
    } else {
      // optional filter perusahaan
      if ($request->perusahaan_id) {
        $query->where('id_perusahaan', $request->perusahaan_id);
      }
    }

    // 🔍 SEARCH
    if ($request->search) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_keluar', 'like', "%$search%")
          ->orWhere('kode_barang', 'like', "%$search%")
          ->orWhere('warna', 'like', "%$search%")
          ->orWhereHas('masuk', function ($m) use ($search) {
            $m->where('kode_masuk', 'like', "%$search%")
              ->orWhere('type', 'like', "%$search%")
              ->orWhere('merek', 'like', "%$search%");
          })
          ->orWhereHas('masuk.kategori', function ($k) use ($search) {
            $k->where('nama_barang', 'like', "%$search%");
          })
          ->orWhereHas('karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%$search%");
          });
      });
    }

    $keluars = $query
      ->latest()
      ->paginate(5)
      ->appends($request->query());

    // 🔥 TAMBAHAN (untuk filter dropdown)
    $perusahaans = $user->role === 'super_admin' ? \App\Models\Perusahaan::all() : collect();

    return view('content.dashboard.transaksi-keluar.index', compact('keluars', 'perusahaans'));
  }

  private function generateKodeKeluar()
  {
    $tahun = Carbon::now()->year;

    $last = Keluar::whereYear('created_at', $tahun)
      ->where('id_perusahaan', auth()->user()->id_perusahaan)
      ->orderBy('id', 'desc')
      ->first();

    if ($last) {
      $lastNumber = (int) substr($last->kode_keluar, -4);
      $nextNumber = $lastNumber + 1;
    } else {
      $nextNumber = 1;
    }

    return 'KLR-' . $tahun . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $kodeKeluar = $this->generateKodeKeluar();
    $karyawans = Karyawan::orderBy('nama_karyawan')->get();

    return view('content.dashboard.transaksi-keluar.create', compact('kodeKeluar', 'karyawans'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $perusahaan = auth()->user()->perusahaan;
    $request->validate([
      'kode_keluar' => [
        'required',
        Rule::unique('keluars')->where(fn($q) => $q->where('id_perusahaan', auth()->user()->id_perusahaan)),
      ],
      'id_masuk' => 'required|exists:masuks,id',
      'kode_barang' => [
        'required',
        Rule::unique('keluars')->where(fn($q) => $q->where('id_perusahaan', auth()->user()->id_perusahaan)),
      ],
      'jumlah' => 'required|integer|min:1',
      'keterangan' => 'required|string|max:100',
      'warna' => 'required|string|max:50',
      'no_inventaris' => 'required|string|max:50',
      'jenis_penerima' => 'required|in:Perorangan,Perdivisi',
    ]);

    // PERORANGAN
    if ($request->jenis_penerima == 'Perorangan') {
      $request->validate([
        'id_karyawan' => 'required|exists:karyawans,id',
      ]);
    }

    // DIVISI
    if ($request->jenis_penerima == 'Perdivisi') {
      $request->validate([
        'divisi_klr' => 'required',
        'perusahaan_klr' => 'required',
      ]);
    }

    DB::beginTransaction();

    try {
      // 🔒 LOCK STOK
      $masuk = Masuk::where('id', $request->id_masuk)
        ->lockForUpdate()
        ->firstOrFail();

      // ❌ VALIDASI STOK
      if ($request->jumlah > $masuk->jumlah) {
        DB::rollBack();
        return back()
          ->withInput()
          ->with('error', 'Jumlah keluar melebihi stok tersedia!');
      }

      // ✅ SIMPAN
      $keluar = Keluar::create([
        'kode_keluar' => $request->kode_keluar,
        'id_masuk' => $request->id_masuk,
        'kode_barang' => $request->kode_barang,
        'jumlah' => $request->jumlah,
        'jenis_penerima' => $request->jenis_penerima,
        'id_perusahaan' => $perusahaan->id,
        // PERORANGAN
        'id_karyawan' => $request->jenis_penerima == 'Perorangan' ? $request->id_karyawan : null,

        // DIVISI MANUAL
        'divisi_klr' => $request->jenis_penerima == 'Perdivisi' ? $request->divisi_klr : null,

        'perusahaan_klr' =>
          $request->jenis_penerima == 'Perdivisi' ? auth()->user()->perusahaan->nama_perusahaan : null,

        'keterangan' => $request->keterangan,
        'warna' => $request->warna,
        'no_inventaris' => $request->no_inventaris,
      ]);

      // ➖ KURANGI STOK
      $masuk->decrement('jumlah', $request->jumlah);

      DB::commit();

      return redirect()
        ->route('transaksi-keluar.index')
        ->with('success', 'Transaksi keluar berhasil disimpan');
    } catch (\Exception $e) {
      DB::rollBack();
      // dd($e->getMessage());
      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan, silakan ulangi');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show($id)
  {
    $perusahaanId = auth()->user()->perusahaan->id;

    $keluar = Keluar::with(['masuk.kategori', 'karyawan'])
      ->where('id', $id)
      ->where('id_perusahaan', $perusahaanId)
      ->whereHas('masuk', function ($q) use ($perusahaanId) {
        $q->where('perusahaan_id', $perusahaanId);
      })
      ->firstOrFail();

    return view('content.dashboard.transaksi-keluar.show', compact('keluar'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit($id)
  {
    $query = Keluar::with(['masuk.kategori', 'karyawan']);

    if (auth()->user()->role !== 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $keluar = $query->findOrFail($id);

    return view('content.dashboard.transaksi-keluar.edit', compact('keluar'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id)
  {
    $perusahaan = auth()->user()->perusahaan;
    $request->validate([
      'id_masuk' => 'required|exists:masuks,id',
      'kode_barang' => [
        'required',
        Rule::unique('keluars')
          ->where(fn($q) => $q->where('id_perusahaan', auth()->user()->id_perusahaan))
          ->ignore($id),
      ],
      'jumlah' => 'required|integer|min:1',
      'keterangan' => 'required|max:50',
      'warna' => 'required|max:50',
      'no_inventaris' => 'required|max:50',
      'jenis_penerima' => 'required|in:Perorangan,Perdivisi',
    ]);

    // PERORANGAN
    if ($request->jenis_penerima == 'Perorangan') {
      $request->validate([
        'id_karyawan' => 'required|exists:karyawans,id',
      ]);
    }

    // PERDIVISI
    if ($request->jenis_penerima == 'Perdivisi') {
      $request->validate([
        'divisi_klr' => 'required',
        'perusahaan_klr' => 'required',
      ]);
    }

    DB::beginTransaction();

    try {
      $keluar = Keluar::findOrFail($id);

      // 🔄 KEMBALIKAN STOK LAMA
      $masukLama = Masuk::lockForUpdate()->find($keluar->id_masuk);
      $masukLama->increment('jumlah', $keluar->jumlah);

      // 🔒 LOCK STOK BARU
      $masukBaru = Masuk::lockForUpdate()->findOrFail($request->id_masuk);

      if ($request->jumlah > $masukBaru->jumlah) {
        DB::rollBack();
        return back()->with('error', 'Jumlah keluar melebihi stok!');
      }

      // ✏️ UPDATE DATA
      $keluar->update([
        'id_masuk' => $request->id_masuk,
        'kode_barang' => $request->kode_barang,
        'jumlah' => $request->jumlah,
        'jenis_penerima' => $request->jenis_penerima,
        'id_perusahaan' => $perusahaan->id,
        'id_karyawan' => $request->jenis_penerima == 'Perorangan' ? $request->id_karyawan : null,

        'divisi_klr' => $request->jenis_penerima == 'Perdivisi' ? $request->divisi_klr : null,

        'perusahaan_klr' =>
          $request->jenis_penerima == 'Perdivisi' ? auth()->user()->perusahaan->nama_perusahaan : null,

        'keterangan' => $request->keterangan,
        'warna' => $request->warna,
        'no_inventaris' => $request->no_inventaris,
      ]);

      // ➖ KURANGI STOK BARU
      $masukBaru->decrement('jumlah', $request->jumlah);

      DB::commit();

      return redirect()
        ->route('transaksi-keluar.index')
        ->with('success', 'Transaksi keluar berhasil diperbarui');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->with('error', 'Terjadi kesalahan');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    DB::beginTransaction();

    try {
      $query = Keluar::with('masuk');

      if (auth()->user()->role !== 'super_admin') {
        $query->where('id_perusahaan', auth()->user()->id_perusahaan);
      }

      $keluar = $query->findOrFail($id);

      $masuk = Masuk::where('id', $keluar->id_masuk)
        ->lockForUpdate()
        ->first();

      if ($masuk) {
        $masuk->increment('jumlah', $keluar->jumlah);
      }

      $keluar->delete();

      DB::commit();

      return back()->with('success', 'Data berhasil dihapus');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->with('error', 'Gagal hapus data');
    }
  }
  public function getMasukByKode($kode)
  {
    $masuk = Masuk::with('kategori')
      ->where('kode_masuk', $kode)
      ->where('id_perusahaan', auth()->user()->id_perusahaan)
      ->first();

    if (!$masuk) {
      return response()->json(['status' => false]);
    }

    return response()->json([
      'status' => true,
      'data' => [
        'id_masuk' => $masuk->id,
        'nama_barang' => $masuk->kategori->nama_barang ?? '-',
        'type' => $masuk->type,
        'merek' => $masuk->merek,
        'tgl_beli' => $masuk->tgl_beli,
        'stok' => $masuk->jumlah,
      ],
    ]);
  }
  public function autofillByKodeMasuk(Request $request)
  {
    $masuk = Masuk::with('kategori') // 🔥 WAJIB
      ->where('kode_masuk', $request->kode_masuk)
      ->where('perusahaan_id', auth()->user()->id_perusahaan)
      ->first();

    if (!$masuk) {
      return response()->json([
        'status' => false,
        'message' => 'Data tidak ditemukan',
      ]);
    }

    return response()->json([
      'status' => true,
      'data' => [
        'id_masuk' => $masuk->id,
        'nama_barang' => optional($masuk->kategori)->nama_barang ?? '-', // 🔥 AMAN
        'type' => $masuk->type ?? '-',
        'merek' => $masuk->merek ?? '-',
        'tgl_beli' => $masuk->tgl_beli ?? null,
      ],
    ]);
  }

  public function getKaryawanByNama(Request $request)
  {
    $request->validate([
      'nama_karyawan' => 'required',
    ]);

    $user = auth()->user();

    $karyawan = Karyawan::with('perusahaan')
      ->where('nama_karyawan', $request->nama_karyawan)
      ->where('id_perusahaan', $user->id_perusahaan) // 🔥 FIX DI SINI
      ->first();

    if (!$karyawan) {
      return response()->json(['status' => false]);
    }

    return response()->json([
      'status' => true,
      'data' => [
        'id' => $karyawan->id,
        'divisi' => $karyawan->divisi,
        'perusahaan' => $karyawan->perusahaan->nama_perusahaan ?? '-',
      ],
    ]);
  }
}

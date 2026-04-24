<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Kategori;
use App\Models\Keluar;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $query = Peminjaman::with(['karyawan', 'kategori', 'keluar.masuk.kategori', 'perusahaan', 'lokasi']);

    // ================= SEARCH =================
    if ($request->search) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        // Cari KODE BARANG (dari tabel keluar)
        $q->whereHas('keluar', function ($k) use ($search) {
          $k->where('kode_barang', 'like', "%{$search}%");
        })

          // Cari NAMA BARANG (dari kategori lewat masuk)
          ->orWhereHas('keluar.masuk.kategori', function ($k) use ($search) {
            $k->where('nama_barang', 'like', "%{$search}%");
          })

          // Cari NAMA KARYAWAN
          ->orWhereHas('karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          });
      });
    }

    // ================= ORDER & PAGINATION =================
    $peminjamans = $query->orderBy('created_at', 'desc')->paginate(5);

    return view('content.dashboard.peminjaman.index', compact('peminjamans'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $keluars = Keluar::orderBy('kode_barang')->get();
    $kategoris = Kategori::orderBy('nama_barang')->get();
    $karyawans = Karyawan::orderBy('nama_karyawan')->get();
    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    $lokasis = Lokasi::orderBy('nama_lokasi')->get();

    return view(
      'content.dashboard.peminjaman.create',
      compact('keluars', 'kategoris', 'karyawans', 'perusahaans', 'lokasis')
    );
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $data = $request->all();

    // 🔥 mapping tipe
    $data['tipe_peminjam'] = $request->jenis_perusahaan;

    // ================= AMBIL KATEGORI =================
    $keluar = Keluar::with('masuk.kategori')->find($request->keluar_id);

    if (!$keluar || !$keluar->masuk || !$keluar->masuk->kategori) {
      return back()->with('error', 'Kategori tidak ditemukan dari barang');
    }

    $data['kategori_id'] = $keluar->masuk->kategori->id;

    // ================= TIPE =================
    if ($request->jenis_perusahaan == 'external') {
      $data['karyawan_id'] = null;
      $data['perusahaan_id'] = null;
      $data['lokasi_id'] = null;
    } else {
      $data['karyawan_id'] = $request->karyawan_id;
      $data['perusahaan_id'] = $request->perusahaan_id;
      $data['lokasi_id'] = $request->lokasi_id;

      // 🔥 kosongkan external biar bersih
      $data['nama_eksternal'] = null;
      $data['perusahaan_eksternal'] = null;
      $data['lokasi_manual'] = null;
    }

    Peminjaman::create($data);

    return redirect()
      ->route('peminjaman.index')
      ->with('success', 'Data berhasil disimpan');
  }

  /**
   * Display the specified resource.
   */
  public function show($id)
  {
    $peminjaman = Peminjaman::with(['keluar.masuk.kategori', 'karyawan', 'perusahaan', 'lokasi'])->findOrFail($id);

    return view('content.dashboard.peminjaman.show', compact('peminjaman'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit($id)
  {
    $peminjaman = Peminjaman::with(['keluar.masuk.kategori', 'karyawan', 'perusahaan', 'lokasi'])->findOrFail($id);

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    $lokasis = Lokasi::orderBy('nama_lokasi')->get();

    return view('content.dashboard.peminjaman.edit', compact('peminjaman', 'perusahaans', 'lokasis'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id)
  {
    $peminjaman = Peminjaman::findOrFail($id);

    // 🔥 mapping tipe
    $tipe = $request->jenis_perusahaan;

    // ================= VALIDASI DINAMIS =================
    if ($tipe == 'external') {
      $request->validate([
        'keluar_id' => 'required|exists:keluars,id',
        'tanggal_pinjam' => 'required|date',
        'tanggal_rencana_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        'status' => 'required|in:Dipinjam,Dikembalikan',

        'nama_eksternal' => 'required|string|max:255',
        'perusahaan_eksternal' => 'required|string|max:255',
        'lokasi_manual' => 'required|string|max:255',
      ]);
    } else {
      $request->validate([
        'keluar_id' => 'required|exists:keluars,id',
        'karyawan_id' => 'required|exists:karyawans,id',
        'perusahaan_id' => 'required|exists:perusahaans,id',
        'lokasi_id' => 'required|exists:lokasis,id',
        'tanggal_pinjam' => 'required|date',
        'tanggal_rencana_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        'status' => 'required|in:Dipinjam,Dikembalikan',
      ]);
    }

    // ================= AMBIL KATEGORI =================
    $keluar = Keluar::with('masuk.kategori')->find($request->keluar_id);

    if (!$keluar || !$keluar->masuk || !$keluar->masuk->kategori) {
      return back()->with('error', 'Kategori tidak ditemukan dari barang');
    }

    // ================= DATA UPDATE =================
    $dataUpdate = [
      'keluar_id' => $request->keluar_id,
      'kategori_id' => $keluar->masuk->kategori->id,
      'tanggal_pinjam' => $request->tanggal_pinjam,
      'tanggal_rencana_kembali' => $request->tanggal_rencana_kembali,
      'status' => $request->status,
      'keperluan' => $request->keperluan,
      'catatan' => $request->catatan,
      'tipe_peminjam' => $tipe, // 🔥 penting
    ];

    // ================= TIPE =================
    if ($tipe == 'external') {
      $dataUpdate['karyawan_id'] = null;
      $dataUpdate['perusahaan_id'] = null;
      $dataUpdate['lokasi_id'] = null;

      $dataUpdate['nama_eksternal'] = $request->nama_eksternal;
      $dataUpdate['perusahaan_eksternal'] = $request->perusahaan_eksternal;
      $dataUpdate['lokasi_manual'] = $request->lokasi_manual;
    } else {
      $dataUpdate['karyawan_id'] = $request->karyawan_id;
      $dataUpdate['perusahaan_id'] = $request->perusahaan_id;
      $dataUpdate['lokasi_id'] = $request->lokasi_id;

      // 🔥 bersihkan data external
      $dataUpdate['nama_eksternal'] = null;
      $dataUpdate['perusahaan_eksternal'] = null;
      $dataUpdate['lokasi_manual'] = null;
    }

    // ================= STATUS =================
    if ($request->status === 'Dikembalikan') {
      $dataUpdate['tanggal_kembali'] = now();
    } else {
      $dataUpdate['tanggal_kembali'] = null;
    }

    // ================= UPDATE =================
    $peminjaman->update($dataUpdate);

    return redirect()
      ->route('peminjaman.index')
      ->with('success', 'Data peminjaman berhasil diupdate');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $peminjaman = Peminjaman::findOrFail($id);
    $peminjaman->delete();

    return redirect()
      ->route('peminjaman.index')
      ->with('success', 'Data peminjaman berhasil dihapus');
  }

  public function getNamaBarang($kode)
  {
    $data = Keluar::with('masuk.kategori')
      ->where('kode_barang', $kode)
      ->first();

    if (!$data || !$data->masuk || !$data->masuk->kategori) {
      return response()->json([
        'status' => 'not_found',
      ]);
    }

    return response()->json([
      'status' => 'ok',
      'nama_barang' => $data->masuk->kategori->nama_barang,
      'keluar_id' => $data->id,
    ]);
  }

  public function searchKaryawan(Request $request)
  {
    $keyword = $request->q;

    $data = Karyawan::where('nama_karyawan', 'like', "%$keyword%")
      ->limit(10)
      ->get(['id', 'nama_karyawan']);

    return response()->json($data);
  }
  public function cekStatus($kode)
  {
    // Cari barang dari tabel keluar
    $keluar = Keluar::where('kode_barang', $kode)->first();

    if (!$keluar) {
      return response()->json([
        'dipinjam' => false,
      ]);
    }

    // Cek apakah masih dipinjam (belum dikembalikan)
    $dipinjam = Peminjaman::where('keluar_id', $keluar->id)
      ->whereNull('tanggal_kembali') // BELUM DIKEMBALIKAN
      ->exists();

    return response()->json([
      'dipinjam' => $dipinjam,
    ]);
  }
}

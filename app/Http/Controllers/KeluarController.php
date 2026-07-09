<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Keluar;
use App\Models\Masuk;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Inventaris;
use App\Models\Kategori;
use App\Models\Lokasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class KeluarController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Keluar::with(['inventaris.dataAset.kategori', 'karyawan', 'perusahaan']);

    // FILTER PERUSAHAAN
    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->perusahaan_id) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    // FILTER TANGGAL
    if ($request->tanggal_awal) {
      $query->whereDate('tgl_keluar', '>=', $request->tanggal_awal);
    }

    if ($request->tanggal_akhir) {
      $query->whereDate('tgl_keluar', '<=', $request->tanggal_akhir);
    }
    // FILTER KATEGORI
    if ($request->filled('kategori_id')) {
      $query->whereHas('inventaris.dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->kategori_id);
      });
    }

    // SEARCH
    if ($request->search) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->whereHas('inventaris', function ($i) use ($search) {
          $i->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
        })

          ->orWhereHas('karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          })

          ->orWhere('divisi_klr', 'like', "%{$search}%");
      });
    }

    $keluars = $query
      ->latest()
      ->paginate(10)
      ->appends($request->query());

    $perusahaans = Perusahaan::all();
    $kategoris = Kategori::orderBy('nama_barang')->get();

    return view('content.dashboard.transaksi-keluar.index', compact('keluars', 'perusahaans', 'kategoris'));
  }
  public function cetak(Request $request)
  {
    $user = auth()->user();

    $query = Keluar::with(['inventaris.dataAset.kategori', 'inventaris.perusahaan', 'karyawan', 'lokasi']);

    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->perusahaan_id) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    if ($request->tanggal_awal) {
      $query->whereDate('tgl_keluar', '>=', $request->tanggal_awal);
    }

    if ($request->tanggal_akhir) {
      $query->whereDate('tgl_keluar', '<=', $request->tanggal_akhir);
    }
    // FILTER KATEGORI
    if ($request->filled('kategori_id')) {
      $query->whereHas('inventaris.dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->kategori_id);
      });
    }

    $keluars = $query->orderBy('tgl_keluar')->get();

    $namaPerusahaan = 'SEMBILAN GROUP';

    if ($user->role == 'super_admin') {
      if ($request->perusahaan_id) {
        $namaPerusahaan = Perusahaan::find($request->perusahaan_id)?->nama_perusahaan;
      }
    } else {
      $namaPerusahaan = Perusahaan::find($user->id_perusahaan)?->nama_perusahaan;
    }

    return view('content.dashboard.transaksi-keluar.cetak', compact('keluars', 'namaPerusahaan'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $user = auth()->user();

    $perusahaans = $user->role == 'super_admin' ? Perusahaan::orderBy('nama_perusahaan')->get() : collect();

    return view('content.dashboard.transaksi-keluar.create', compact('perusahaans'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    $perusahaanId = $user->role == 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;

    $request->validate([
      'inventaris_id' => 'required|exists:inventaris,id',

      'tgl_keluar' => 'required|date',

      'jenis_penerima' => 'required|in:Perorangan,Perdivisi',

      'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:1048',
      'lokasi_id' => 'required|exists:lokasis,id',
    ]);

    if ($request->jenis_penerima == 'Perorangan') {
      $request->validate([
        'karyawan_id' => 'required|exists:karyawans,id',
      ]);
    }

    if ($request->jenis_penerima == 'Perdivisi') {
      $request->validate([
        'divisi_klr' => 'required',
      ]);
    }

    DB::beginTransaction();

    try {
      $inventaris = Inventaris::where('perusahaan_id', $perusahaanId)
        ->where('status', 'TERSEDIA')
        ->findOrFail($request->inventaris_id);

      $gambar = null;

      if ($request->hasFile('gambar')) {
        $gambar = $request->file('gambar')->store('transaksi-keluar', 'public');
      }

      Keluar::create([
        'inventaris_id' => $inventaris->id,

        'perusahaan_id' => $perusahaanId,

        'karyawan_id' => $request->jenis_penerima == 'Perorangan' ? $request->karyawan_id : null,
        'lokasi_id' => $request->lokasi_id,

        'tgl_keluar' => $request->tgl_keluar,

        'jenis_penerima' => $request->jenis_penerima,

        'divisi_klr' => $request->jenis_penerima == 'Perdivisi' ? strtoupper($request->divisi_klr) : null,

        'perusahaan_klr' => $request->jenis_penerima == 'Perdivisi' ? strtoupper($request->perusahaan_klr) : null,

        'gambar' => $gambar,
        'created_by' => auth()->id(),
      ]);

      $inventaris->update([
        'status' => 'DIPAKAI',
      ]);

      DB::commit();

      return redirect()
        ->route('transaksi-keluar.index')
        ->with('success', 'Pemakaian aset berhasil disimpan');
    } catch (\Exception $e) {
      DB::rollBack();

      dd($e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(int $id)
  {
    $keluar = Keluar::with(['inventaris.dataAset.kategori', 'inventaris.perusahaan', 'karyawan', 'lokasi'])->findOrFail(
      $id
    );
    return view('content.dashboard.transaksi-keluar.show', compact('keluar'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(int $id)
  {
    $user = auth()->user();

    $query = Keluar::with(['inventaris.dataAset.kategori', 'karyawan', 'perusahaan', 'lokasi']);

    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    }

    $keluar = $query->findOrFail($id);

    $perusahaans = $user->role == 'super_admin' ? Perusahaan::all() : collect();
    if ($user->role == 'super_admin') {
      $lokasis = Lokasi::orderBy('nama_lokasi')->get();
    } else {
      $lokasis = Lokasi::where('id_perusahaan', $user->id_perusahaan)
        ->orderBy('nama_lokasi')
        ->get();
    }

    return view('content.dashboard.transaksi-keluar.edit', compact('keluar', 'perusahaans', 'lokasis'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $user = auth()->user();

    $perusahaanId = $user->role === 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;

    $request->validate([
      'tgl_keluar' => 'required|date',
      'jenis_penerima' => 'required|in:Perorangan,Perdivisi',
      'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // PERORANGAN
    if ($request->jenis_penerima == 'Perorangan') {
      $request->validate([
        'karyawan_id' => 'required|exists:karyawans,id',
      ]);
    }

    // PERDIVISI
    if ($request->jenis_penerima == 'Perdivisi') {
      $request->validate([
        'divisi_klr' => 'required|string|max:255',
      ]);
    }

    DB::beginTransaction();

    try {
      $keluar = Keluar::where('id', $id)
        ->where('perusahaan_id', $perusahaanId)
        ->firstOrFail();

      $namaPerusahaan = Perusahaan::find($perusahaanId)?->nama_perusahaan;

      // Upload gambar baru
      if ($request->hasFile('gambar')) {
        if ($keluar->gambar) {
          Storage::disk('public')->delete($keluar->gambar);
        }

        $gambar = $request->file('gambar')->store('keluar', 'public');
      } else {
        $gambar = $keluar->gambar;
      }

      $keluar->update([
        'tgl_keluar' => $request->tgl_keluar,

        'jenis_penerima' => $request->jenis_penerima,

        'karyawan_id' => $request->jenis_penerima == 'Perorangan' ? $request->karyawan_id : null,
        'lokasi_id' => $request->lokasi_id,

        'divisi_klr' => $request->jenis_penerima == 'Perdivisi' ? strtoupper($request->divisi_klr) : null,

        'perusahaan_klr' => $request->jenis_penerima == 'Perdivisi' ? strtoupper($namaPerusahaan) : null,

        'gambar' => $gambar,
      ]);

      DB::commit();

      return redirect()
        ->route('transaksi-keluar.index')
        ->with('success', 'Data pemakaian aset berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error($e->getMessage());

      return back()
        ->withInput()
        ->with('error', $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(int $id)
  {
    DB::beginTransaction();

    try {
      $keluar = Keluar::findOrFail($id);

      $keluar->inventaris->update([
        'status' => 'TERSEDIA',
      ]);

      if ($keluar->gambar && Storage::disk('public')->exists($keluar->gambar)) {
        Storage::disk('public')->delete($keluar->gambar);
      }

      $keluar->delete();

      DB::commit();

      return back()->with('success', 'Data berhasil dihapus');
    } catch (\Exception $e) {
      DB::rollBack();

      return back()->with('error', $e->getMessage());
    }
  }

  public function getKaryawan(Request $request)
  {
    $query = Karyawan::query();

    if (auth()->user()->role == 'super_admin') {
      if ($request->perusahaan_id) {
        $query->where('id_perusahaan', $request->perusahaan_id);
      }
    } else {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $query->where(function ($q) use ($request) {
      $q->where('nama_karyawan', 'like', '%' . $request->keyword . '%')->orWhere(
        'kode_karyawan',
        'like',
        '%' . $request->keyword . '%'
      );
    });

    $data = $query
      ->select('id', 'kode_karyawan', 'nama_karyawan', 'divisi')
      ->limit(10)
      ->get();

    return response()->json($data);
  }

  public function getKategori(int $perusahaanId)
  {
    return response()->json(
      Kategori::where('perusahaan_id', $perusahaanId)
        ->orderBy('nama_barang')
        ->get()
    );
  }
  public function getInventaris(Request $request)
  {
    $query = Inventaris::with(['dataAset.kategori'])->where('status', 'TERSEDIA');

    if (auth()->user()->role == 'super_admin') {
      $query->where('perusahaan_id', $request->perusahaan_id);
    } else {
      $query->where('perusahaan_id', auth()->user()->id_perusahaan);
    }

    $query->whereHas('dataAset', function ($q) use ($request) {
      $q->where('kategori_id', $request->kategori_id);
    });

    return response()->json($query->orderBy('kode_aset')->get());
  }
  public function getInventarisDetail(string $id)
  {
    $inventaris = Inventaris::with(['dataAset.kategori'])->findOrFail($id);

    return response()->json([
      'kategori' => $inventaris->dataAset->kategori->nama_barang,

      'merek' => $inventaris->dataAset->merek,

      'type' => $inventaris->dataAset->type,

      'kode_aset' => $inventaris->kode_aset,

      'no_inventaris' => $inventaris->no_inventaris,
    ]);
  }
  public function getLokasi($perusahaan)
  {
    return Lokasi::where('id_perusahaan', $perusahaan)
      ->orderBy('nama_lokasi')
      ->get();
  }
}

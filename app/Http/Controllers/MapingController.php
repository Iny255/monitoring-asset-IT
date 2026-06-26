<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Maping;
use App\Models\Lokasi;
use App\Models\Keluar;
use App\Models\Perusahaan;
use App\Models\Kategori;
use App\Models\Karyawan;
use App\Models\MutasiMaping;
use App\Models\Pencabutan;
use App\Models\Access;
use App\Models\MapingAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
class MapingController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Maping::with(['keluar.inventaris.kategori', 'keluar.karyawan', 'lokasi', 'perusahaan']);

    /*
    |--------------------------------------------------------------------------
    | FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if ($user->role != 'super_admin') {
      $query->where('id_perusahaan', $user->id_perusahaan);
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('id_perusahaan', $request->perusahaan_id);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER LOKASI
    |--------------------------------------------------------------------------
    */

    if ($request->filled('lokasi_id')) {
      $query->where('id_lokasi', $request->lokasi_id);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER STATUS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER USER ASET
    |--------------------------------------------------------------------------
    */

    if ($request->filled('karyawan_id')) {
      $query->whereHas('keluar', function ($q) use ($request) {
        $q->where('karyawan_id', $request->karyawan_id);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER KATEGORI
    |--------------------------------------------------------------------------
    */

    if ($request->filled('kategori_id')) {
      $query->whereHas('keluar.inventaris', function ($q) use ($request) {
        $q->where('kategori_id', $request->kategori_id);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER RENTANG TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
      $query->whereBetween('tanggal_digunakan', [$request->tanggal_awal, $request->tanggal_akhir]);
    } elseif ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_digunakan', '>=', $request->tanggal_awal);
    } elseif ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_digunakan', '<=', $request->tanggal_akhir);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    /*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

    if ($request->filled('search')) {
      $search = trim($request->search);

      $query->where(function ($q) use ($search) {
        // Mapping
        $q->where('processor', 'like', "%{$search}%")
          ->orWhere('ram', 'like', "%{$search}%")
          ->orWhere('device_id', 'like', "%{$search}%")
          ->orWhere('produk_id', 'like', "%{$search}%")
          ->orWhere('system', 'like', "%{$search}%")
          ->orWhere('version', 'like', "%{$search}%")
          ->orWhere('catatan', 'like', "%{$search}%")

          // Pemakaian
          ->orWhereHas('keluar', function ($k) use ($search) {
            $k->where('kode_inventaris', 'like', "%{$search}%")->orWhere('serial_number', 'like', "%{$search}%");
          })

          // User Aset
          ->orWhereHas('keluar.karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          })

          // Data Aset
          ->orWhereHas('keluar.inventaris', function ($a) use ($search) {
            $a->where('merek', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%")
              ->orWhere('kode_inventaris', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%");
          })

          // Kategori
          ->orWhereHas('keluar.inventaris.kategori', function ($k) use ($search) {
            $k->where('nama_barang', 'like', "%{$search}%");
          })

          // Lokasi
          ->orWhereHas('lokasi', function ($l) use ($search) {
            $l->where('nama_lokasi', 'like', "%{$search}%");
          });
      });
    }

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    $mapings = $query
      ->latest()
      ->paginate(10)
      ->appends(request()->query());

    /*
    |--------------------------------------------------------------------------
    | DROPDOWN
    |--------------------------------------------------------------------------
    */

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    $lokasis =
      $user->role == 'super_admin'
        ? Lokasi::orderBy('nama_lokasi')->get()
        : Lokasi::where('id_perusahaan', $user->id_perusahaan)
          ->orderBy('nama_lokasi')
          ->get();

    $karyawans =
      $user->role == 'super_admin'
        ? Karyawan::orderBy('nama_karyawan')->get()
        : Karyawan::where('id_perusahaan', $user->id_perusahaan)
          ->orderBy('nama_karyawan')
          ->get();

    $kategoris =
      $user->role == 'super_admin'
        ? Kategori::orderBy('nama_barang')->get()
        : Kategori::where('perusahaan_id', $user->id_perusahaan)
          ->orderBy('nama_barang')
          ->get();

    $keluars = Keluar::doesntHave('maping')
      ->when($user->role != 'super_admin', fn($q) => $q->where('perusahaan_id', $user->id_perusahaan))
      ->get();

    return view(
      'content.dashboard.maping.index',
      compact('mapings', 'perusahaans', 'lokasis', 'karyawans', 'kategoris', 'keluars')
    );
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if ($user->role == 'super_admin') {
      $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

      $lokasis = collect();
    } else {
      $perusahaans = collect();

      $lokasis = Lokasi::where('id_perusahaan', $user->id_perusahaan)
        ->orderBy('nama_lokasi')
        ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | MASTER ACCESS
    |--------------------------------------------------------------------------
    */

    $aplikasis = Access::where('kategori', 'Aplikasi')
      ->where('jenis', 'Software')
      ->where('status', 'aktif')
      ->orderBy('nama_akses')
      ->get();

    $hakAksesPPN = Access::where('kategori', 'Hak Akses')
      ->where('jenis', 'PPN')
      ->where('status', 'aktif')
      ->orderBy('nama_akses')
      ->get();

    $hakAksesNonPPN = Access::where('kategori', 'Hak Akses')
      ->where('jenis', 'NON PPN')
      ->where('status', 'aktif')
      ->orderBy('nama_akses')
      ->get();

    return view(
      'content.dashboard.maping.create',
      compact('perusahaans', 'lokasis', 'aplikasis', 'hakAksesPPN', 'hakAksesNonPPN')
    );
  }

  public function getKategori(Request $request)
  {
    return response()->json(Kategori::orderBy('nama_barang')->get());
  }
  public function getAset(Request $request)
  {
    $query = Keluar::with(['inventaris.dataAset.kategori', 'karyawan']);

    if ($request->id_perusahaan) {
      $query->where('perusahaan_id', $request->id_perusahaan);
    }

    $query->whereHas('inventaris.dataAset', function ($q) use ($request) {
      $q->where('kategori_id', $request->id_kategori);
    });

    $used = Maping::pluck('id_keluar');

    if ($request->current_keluar) {
      $used = $used->reject(fn($id) => $id == $request->current_keluar);
    }

    $query->whereNotIn('id', $used);

    return response()->json($query->get());
  }
  public function getDetailAset($id)
  {
    $keluar = Keluar::with(['inventaris.dataAset.kategori', 'karyawan'])->findOrFail($id);

    return response()->json([
      'id_keluar' => $keluar->id,
      'kode_aset' => $keluar->inventaris->kode_aset ?? '-',
      'nama_barang' => $keluar->inventaris->dataAset->kategori->nama_barang ?? '-',
      'type' => $keluar->inventaris->dataAset->type ?? '-',
      'merek' => $keluar->inventaris->dataAset->merek ?? '-',
      'warna' => $keluar->inventaris->dataAset->warna ?? '-',
      'nama_karyawan' => $keluar->karyawan->nama_karyawan ?? '-',
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    $validated = $request->validate([
      'id_keluar' => 'required|exists:keluars,id',
      'id_lokasi' => 'required|exists:lokasis,id',

      'processor' => 'nullable|string|max:100',
      'ram' => 'nullable|string|max:20',

      'device_id' => 'nullable|string|max:100',
      'produk_id' => 'nullable|string|max:100',

      'system' => 'nullable|string|max:50',
      'version' => 'nullable|string|max:50',

      'instal_on' => 'nullable|date',

      'tanggal_digunakan' => 'required|date',

      'catatan' => 'nullable|string',
      'accesses' => 'nullable|array',
      'accesses.*' => 'exists:accesses,id',
    ]);

    try {
      // Tentukan perusahaan
      $perusahaanId =
        $user->role == 'super_admin' ? Keluar::findOrFail($request->id_keluar)->id_perusahaan : $user->id_perusahaan;

      // Pastikan inventaris belum pernah dimapping
      $cekMaping = Maping::where('id_keluar', $request->id_keluar)->exists();

      if ($cekMaping) {
        return back()->with('error', 'Inventaris tersebut sudah pernah dimapping.');
      }

      // Cek Device ID
      if ($request->filled('device_id')) {
        $device = Maping::where('device_id', $request->device_id)
          ->where('id_perusahaan', $perusahaanId)
          ->exists();

        if ($device) {
          return back()->with('error', 'Device ID sudah digunakan.');
        }
      }

      // Cek Product ID
      if ($request->filled('produk_id')) {
        $produk = Maping::where('produk_id', $request->produk_id)
          ->where('id_perusahaan', $perusahaanId)
          ->exists();

        if ($produk) {
          return back()->with('error', 'Product ID sudah digunakan.');
        }
      }

      $maping = Maping::create([
        'id_keluar' => $request->id_keluar,

        'id_lokasi' => $request->id_lokasi,

        'id_perusahaan' => $perusahaanId,

        'processor' => strtoupper($request->processor),

        'ram' => strtoupper($request->ram),

        'device_id' => strtoupper($request->device_id),

        'produk_id' => strtoupper($request->produk_id),

        'system' => strtoupper($request->system),

        'version' => strtoupper($request->version),

        'instal_on' => $request->instal_on,

        'tanggal_digunakan' => $request->tanggal_digunakan,

        'catatan' => $request->catatan,

        'status' => 'dipakai',
      ]);
      // ===============================
      // SIMPAN HAK AKSES & APLIKASI
      // ===============================

      if ($request->filled('accesses')) {
        foreach ($request->accesses as $accessId) {
          MapingAccess::create([
            'maping_id' => $maping->id,

            'access_id' => $accessId,
          ]);
        }
      }

      return redirect()
        ->route('maping.index')
        ->with('success', 'Mapping aset berhasil disimpan.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menyimpan mapping aset.');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(int $id)
  {
    $query = Maping::with(['lokasi', 'perusahaan', 'keluar.karyawan', 'keluar.inventaris.dataAset.kategori']);

    if (auth()->user()->role != 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $maping = $query->findOrFail($id);

    return view('content.dashboard.maping.show', compact('maping'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(int $id)
  {
    $user = auth()->user();

    $maping = Maping::with([
      'keluar.inventaris.dataAset.kategori',
      'keluar.karyawan',
      'lokasi',
      'perusahaan',
    ])->findOrFail($id);

    if ($user->role !== 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $lokasis = Lokasi::where('id_perusahaan', $maping->id_perusahaan)->get();

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    $kategoris = Kategori::orderBy('nama_barang')->get();

    return view('content.dashboard.maping.edit', compact('maping', 'lokasis', 'perusahaans', 'kategoris'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $maping = Maping::findOrFail($id);

    $validated = $request->validate([
      'id_lokasi' => 'required|exists:lokasis,id',

      'processor' => 'nullable|string|max:100',

      'ram' => 'nullable|string|max:20',

      'device_id' => 'nullable|string|max:100',

      'produk_id' => 'nullable|string|max:100',

      'system' => 'nullable|string|max:50',

      'version' => 'nullable|string|max:50',

      'instal_on' => 'nullable|date',

      'tanggal_digunakan' => 'required|date',

      'catatan' => 'nullable|string',
    ]);

    try {
      // ==========================
      // VALIDASI DEVICE ID
      // ==========================

      if ($request->filled('device_id')) {
        $cekDevice = Maping::where('device_id', $request->device_id)

          ->where('id_perusahaan', $maping->id_perusahaan)

          ->where('id', '!=', $maping->id)

          ->exists();

        if ($cekDevice) {
          return back()->with('error', 'Device ID sudah digunakan.');
        }
      }

      // ==========================
      // VALIDASI PRODUCT ID
      // ==========================

      if ($request->filled('produk_id')) {
        $cekProduk = Maping::where('produk_id', $request->produk_id)

          ->where('id_perusahaan', $maping->id_perusahaan)

          ->where('id', '!=', $maping->id)

          ->exists();

        if ($cekProduk) {
          return back()->with('error', 'Product ID sudah digunakan.');
        }
      }

      // ==========================
      // UPDATE
      // ==========================

      $maping->update([
        'id_lokasi' => $request->id_lokasi,

        'processor' => strtoupper($request->processor),

        'ram' => strtoupper($request->ram),

        'device_id' => strtoupper($request->device_id),

        'produk_id' => strtoupper($request->produk_id),

        'system' => strtoupper($request->system),

        'version' => strtoupper($request->version),

        'instal_on' => $request->instal_on,

        'tanggal_digunakan' => $request->tanggal_digunakan,

        'catatan' => $request->catatan,
      ]);

      return back()->with('success', 'Mapping aset berhasil diperbarui.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal memperbarui mapping aset.');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(int $id)
  {
    try {
      $maping = Maping::findOrFail($id);

      /*
        |--------------------------------------------------------------------------
        | CEK APAKAH SUDAH PERNAH DIGUNAKAN TRANSAKSI
        |--------------------------------------------------------------------------
        */

      // Sudah pernah mutasi
      if ($maping->mutasiMapings()->exists()) {
        return back()->with('error', 'Mapping tidak dapat dihapus karena sudah memiliki riwayat mutasi.');
      }

      /*
        |--------------------------------------------------------------------------
        | HAPUS
        |--------------------------------------------------------------------------
        */

      $maping->delete();

      return back()->with('success', 'Mapping berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menghapus mapping.');
    }
  }

  public function print(Request $request)
  {
    $user = auth()->user();

    $query = Maping::with([
      'lokasi',
      'perusahaan',
      'keluar.karyawan',
      'keluar.inventaris.masuk',
      'keluar.inventaris.dataAset.kategori',
    ])->where('status', $request->get('status', 'aktif'));

    // =====================================
    // PETUGAS HANYA DATA PERUSAHAAN SENDIRI
    // =====================================

    if ($user->role !== 'super_admin') {
      $query->where('id_perusahaan', $user->id_perusahaan);
    }

    // =====================================
    // FILTER LOKASI
    // =====================================

    if ($request->filled('lokasi')) {
      $query->where('id_lokasi', $request->lokasi);
    }

    // =====================================
    // FILTER PERUSAHAAN
    // =====================================

    if ($user->role === 'super_admin' && $request->filled('perusahaan')) {
      $query->where('id_perusahaan', $request->perusahaan);
    }

    // =====================================
    // FILTER TAHUN PEMBELIAN
    // =====================================

    if ($request->filled('tahun')) {
      $query->whereHas('keluar.inventaris.masuk', function ($q) use ($request) {
        $q->whereYear('tanggal_pembelian', $request->tahun);
      });
    }

    // =====================================
    // FILTER KATEGORI BARANG
    // =====================================

    if ($request->filled('barang')) {
      $query->whereHas('keluar.inventaris.dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->barang);
      });
    }

    // =====================================
    // FILTER MEREK
    // =====================================

    if ($request->filled('merek')) {
      $query->whereHas('keluar.inventaris.dataAset', function ($q) use ($request) {
        $q->where('merek', 'like', '%' . $request->merek . '%');
      });
    }

    // =====================================
    // FILTER TYPE
    // =====================================

    if ($request->filled('type')) {
      $query->whereHas('keluar.inventaris.dataAset', function ($q) use ($request) {
        $q->where('type', 'like', '%' . $request->type . '%');
      });
    }

    // =====================================
    // SEARCH GLOBAL
    // =====================================

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('processor', 'like', "%{$search}%")
          ->orWhere('device_id', 'like', "%{$search}%")
          ->orWhere('produk_id', 'like', "%{$search}%")
          ->orWhere('system', 'like', "%{$search}%")
          ->orWhere('version', 'like', "%{$search}%")

          ->orWhereHas('lokasi', function ($sub) use ($search) {
            $sub->where('nama_lokasi', 'like', "%{$search}%");
          })

          ->orWhereHas('perusahaan', function ($sub) use ($search) {
            $sub->where('nama_perusahaan', 'like', "%{$search}%");
          })

          ->orWhereHas('keluar.karyawan', function ($sub) use ($search) {
            $sub->where('nama_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('keluar.inventaris', function ($sub) use ($search) {
            $sub
              ->where('kode_aset', 'like', "%{$search}%")

              ->orWhere('no_inventaris', 'like', "%{$search}%");
          })

          ->orWhereHas('keluar.inventaris.dataAset.kategori', function ($sub) use ($search) {
            $sub->where('nama_barang', 'like', "%{$search}%");
          });
      });
    }

    // =====================================
    // GET DATA
    // =====================================

    $mapings = $query->orderBy('id', 'desc')->get();

    // =====================================
    // NAMA PERUSAHAAN
    // =====================================

    if ($user->role === 'super_admin') {
      $namaPerusahaan = $request->filled('perusahaan')
        ? optional(Perusahaan::find($request->perusahaan))->nama_perusahaan
        : 'SEMBILAN GROUP';
    } else {
      $namaPerusahaan = $user->perusahaan->nama_perusahaan ?? 'Perusahaan';
    }

    return view('content.dashboard.maping.print', compact('mapings', 'namaPerusahaan'));
  }

  public function getLokasiByPerusahaan(int $id)
  {
    $lokasis = Lokasi::where('id_perusahaan', $id)
      ->orderBy('nama_lokasi')
      ->get();

    return response()->json($lokasis);
  }
  public function mutasiForm(int $id)
  {
    $maping = Maping::with([
      'keluar.karyawan',
      'keluar.inventaris.dataAset.kategori',
      'lokasi',
      'perusahaan',
    ])->findOrFail($id);

    $lokasi = Lokasi::orderBy('nama_lokasi')->get();

    $user = auth()->user();

    if ($user->role === 'super_admin') {
      $perusahaan = Perusahaan::orderBy('nama_perusahaan')->get();

      $modePerusahaan = 'select';
    } else {
      $perusahaan = $user->perusahaan;

      $modePerusahaan = 'fixed';
    }

    return view('content.dashboard.maping.mutasi', compact('maping', 'lokasi', 'perusahaan', 'modePerusahaan'));
  }

  public function mutasiStore(Request $request, int $id)
  {
    $request->validate([
      'ke_lokasi' => 'required|exists:lokasis,id',

      'ke_perusahaan' => 'required|exists:perusahaans,id',

      'tanggal_mutasi' => 'required|date',

      'ke_karyawan' => 'nullable|exists:karyawans,id',
    ]);

    $maping = Maping::with(['keluar'])->findOrFail($id);

    DB::beginTransaction();

    try {
      MutasiMaping::create([
        'id_maping' => $maping->id,

        'id_perusahaan' => $request->ke_perusahaan,

        // =====================
        // DARI
        // =====================

        'dari_lokasi' => $maping->id_lokasi,

        'dari_perusahaan' => $maping->id_perusahaan,

        'dari_karyawan' => optional($maping->keluar)->karyawan_id,

        'dari_aplikasi' => $maping->aplikasi,

        'dari_data_ppn' => $maping->data_p,

        'dari_data_non_ppn' => $maping->data_n,

        // =====================
        // KE
        // =====================

        'ke_lokasi' => $request->ke_lokasi,

        'ke_perusahaan' => $request->ke_perusahaan,

        'ke_karyawan' => $request->ke_karyawan,

        'ke_aplikasi' => strtoupper($request->ke_aplikasi ?? ''),

        'ke_data_ppn' => strtoupper($request->ke_data_ppn ?? ''),

        'ke_data_non_ppn' => strtoupper($request->ke_data_non_ppn ?? ''),

        // =====================
        // META
        // =====================

        'tanggal_mutasi' => $request->tanggal_mutasi,

        'keterangan' => strtoupper($request->keterangan ?? ''),

        'created_by' => auth()->id(),
      ]);

      // =====================
      // UPDATE MAPING
      // =====================

      $maping->update([
        'id_lokasi' => $request->ke_lokasi,

        'id_perusahaan' => $request->ke_perusahaan,

        'aplikasi' => strtoupper($request->ke_aplikasi ?? ''),

        'data_p' => strtoupper($request->ke_data_ppn ?? ''),

        'data_n' => strtoupper($request->ke_data_non_ppn ?? ''),
      ]);

      // =====================
      // UPDATE USER ASET
      // =====================

      if ($maping->keluar && $request->filled('ke_karyawan')) {
        $maping->keluar->update([
          'karyawan_id' => $request->ke_karyawan,
        ]);
      }

      DB::commit();

      return redirect()
        ->route('maping.historyGlobal')
        ->with('success', 'Mutasi berhasil disimpan');
    } catch (\Exception $e) {
      DB::rollBack();

      return back()
        ->withInput()
        ->with('error', $e->getMessage());
    }
  }
  public function searchKaryawan(Request $request)
  {
    $keyword = $request->q;
    $perusahaanId = $request->perusahaan_id;

    $karyawan = Karyawan::query()

      ->when($perusahaanId, function ($q) use ($perusahaanId) {
        $q->where('id_perusahaan', $perusahaanId);
      })

      ->when($keyword, function ($q) use ($keyword) {
        $q->where(function ($sub) use ($keyword) {
          $sub
            ->where('kode_karyawan', 'like', "%{$keyword}%")
            ->orWhere('nama_karyawan', 'like', "%{$keyword}%")
            ->orWhere('divisi', 'like', "%{$keyword}%");
        });
      })

      ->orderBy('nama_karyawan')
      ->limit(15)

      ->get(['id', 'kode_karyawan', 'nama_karyawan', 'divisi']);

    return response()->json($karyawan);
  }

  public function historyGlobal(Request $request)
  {
    $query = Maping::with(['keluar.masuk.kategori', 'perusahaan'])->whereHas('mutasiMapings');

    // =====================================
    // FILTER SUPER ADMIN
    // =====================================

    if (auth()->user()->role === 'super_admin') {
      if ($request->perusahaan) {
        $query->where('id_perusahaan', $request->perusahaan);
      }

      $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    } else {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);

      $perusahaans = [];
    }

    // =====================================
    // SEARCH BARANG
    // =====================================

    if ($request->search) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->whereHas('keluar', function ($k) use ($search) {
          $k->where('kode_barang', 'like', "%{$search}%");
        })->orWhereHas('keluar.masuk.kategori', function ($k) use ($search) {
          $k->where('nama_barang', 'like', "%{$search}%");
        });
      });
    }

    // =====================================
    // PAGINATION
    // =====================================

    $mapings = $query
      ->latest()
      ->paginate(5)
      ->appends($request->query());

    return view('content.dashboard.maping.history_global', compact('mapings', 'perusahaans'));
  }

  public function destroyMutasi(int $id)
  {
    try {
      $mutasi = MutasiMaping::findOrFail($id);

      $mutasi->delete();

      return response()->json([
        'success' => true,
        'message' => 'History mutasi berhasil dihapus',
      ]);
    } catch (\Exception $e) {
      return response()->json(
        [
          'success' => false,
          'message' => $e->getMessage(),
        ],
        500
      );
    }
  }
  public function cabut(Request $request, int $id)
  {
    $validated = $request->validate([
      'tanggal_cabut' => 'required|date',
      'kondisi' => 'required|in:Baik,Rusak',
      'alasan' => 'required|string',
      'alasan_lainnya' => 'nullable|string|max:255',
    ]);

    $maping = Maping::with(['keluar.karyawan'])->findOrFail($id);

    DB::beginTransaction();

    try {
      // =====================================
      // ALASAN
      // =====================================

      $alasan = strtoupper($validated['alasan']);

      // jika pilih lain-lain
      if ($validated['alasan'] === 'LAIN-LAIN') {
        $alasan = strtoupper($request->alasan_lainnya);
      }

      // =====================================
      // SIMPAN PENCABUTAN
      // =====================================

      Pencabutan::create([
        'id_maping' => $maping->id,

        'id_keluar' => $maping->id_keluar,

        'id_lokasi' => $maping->id_lokasi,

        'id_perusahaan' => $maping->id_perusahaan,

        'id_karyawan' => optional($maping->keluar)->id_karyawan,

        'tanggal_cabut' => $validated['tanggal_cabut'],

        'kondisi' => strtoupper($validated['kondisi']),

        'alasan' => $alasan,
      ]);

      // =====================================
      // UPDATE STATUS MAPING
      // =====================================

      $maping->update([
        'status' => 'dicabut',
      ]);

      DB::commit();

      return redirect()
        ->route('maping.historyCabut')
        ->with('success', 'Inventaris berhasil dicabut');
    } catch (\Exception $e) {
      DB::rollBack();

      return back()
        ->withInput()
        ->with('error', $e->getMessage());
    }
  }
  public function historyCabut(Request $request)
  {
    $query = Pencabutan::with(['keluar.masuk.kategori', 'lokasi', 'perusahaan', 'karyawan']);

    // =========================
    // ROLE
    // =========================
    if (auth()->user()->role != 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    // =========================
    // FILTER PERUSAHAAN
    // =========================
    if ($request->filled('perusahaan')) {
      $query->where('id_perusahaan', $request->perusahaan);
    }

    // =========================
    // SEARCH
    // =========================
    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->whereHas('keluar', function ($k) use ($search) {
          $k->where('kode_barang', 'like', "%{$search}%");
        })->orWhereHas('keluar.masuk.kategori', function ($k) use ($search) {
          $k->where('nama_barang', 'like', "%{$search}%");
        });
      });
    }

    // =========================
    // DATA
    // =========================
    $pencabutans = $query
      ->latest()
      ->paginate(5)
      ->appends($request->query());

    // =========================
    // DATA PERUSAHAAN
    // =========================
    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    return view('content.dashboard.maping.history_cabut', compact('pencabutans', 'perusahaans'));
  }
  public function hapusCabut(int $id)
  {
    $pencabutan = Pencabutan::findOrFail($id);

    $pencabutan->delete();

    return back()->with('success', 'History pencabutan berhasil dihapus');
  }
  public function historyUser(int $id)
  {
    // dd(auth()->user()->role);
    $query = MutasiMaping::with([
      'dariLokasi',
      'keLokasi',
      'dariPerusahaan',
      'kePerusahaan',
      'dariKaryawan',
      'keKaryawan',
      'maping', // 🔥 WAJIB
    ])->where('id_maping', $id);

    // 🔥 FILTER PERUSAHAAN
    if (auth()->user()->role !== 'super_admin') {
      $query->whereHas('maping', function ($q) {
        $q->where('id_perusahaan', auth()->user()->id_perusahaan);
      });
    }

    $histories = $query->orderBy('tanggal_mutasi', 'desc')->get();

    return view('content.dashboard.maping.history_user', compact('histories'));
  }
  public function detailAjax(int $id)
  {
    $query = Maping::with(['lokasi', 'perusahaan', 'keluar.masuk.kategori', 'keluar.karyawan']);

    if (auth()->user()->role !== 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $maping = $query->findOrFail($id);

    return view('content.dashboard.maping.detail_ajax', compact('maping'));
  }
  public function publicShow(int $id)
  {
    $maping = Maping::with([
      'perusahaan',
      'lokasi',
      'keluar.karyawan',
      'keluar.inventaris.dataAset.kategori',
    ])->findOrFail($id);

    return view('content.dashboard.maping.public_show', compact('maping'));
  }
}

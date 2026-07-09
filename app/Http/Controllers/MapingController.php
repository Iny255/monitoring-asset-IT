<?php

namespace App\Http\Controllers;

use App\Models\Maping;
use App\Models\Lokasi;
use App\Models\Keluar;
use App\Models\Perusahaan;
use App\Models\Kategori;
use App\Models\Karyawan;
use App\Models\Access;
use App\Models\Inventaris;
use App\Models\MapingAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\MapingExport;
use Maatwebsite\Excel\Facades\Excel;

class MapingController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Maping::with([
      'lokasi',
      'perusahaan',
      'karyawan',
      'keluar.inventaris.masuk',
      'keluar.inventaris.dataAset.kategori',
    ]);

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
    } else {
      // Default tidak menampilkan selesai
      $query->where('status', '!=', 'selesai');
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER USER ASET
    |--------------------------------------------------------------------------
    */

    if ($request->filled('karyawan_id')) {
      $query->where('karyawan_id', $request->karyawan_id);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER KATEGORI
    |--------------------------------------------------------------------------
    */

    if ($request->filled('kategori_id')) {
      $query->whereHas('keluar.inventaris.dataAset', function ($q) use ($request) {
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

          // Inventaris
          ->orWhereHas('keluar.inventaris', function ($q) use ($search) {
            $q->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
          })

          // User
          ->orWhereHas('karyawan', function ($q) use ($search) {
            $q->where('nama_karyawan', 'like', "%{$search}%");
          })

          // Data Aset
          ->orWhereHas('keluar.inventaris.dataAset', function ($q) use ($search) {
            $q->where('merek', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%")
              ->orWhere('warna', 'like', "%{$search}%");
          })

          // Kategori
          ->orWhereHas('keluar.inventaris.dataAset.kategori', function ($q) use ($search) {
            $q->where('nama_barang', 'like', "%{$search}%");
          })

          // Lokasi
          ->orWhereHas('lokasi', function ($q) use ($search) {
            $q->where('nama_lokasi', 'like', "%{$search}%");
          });
      });
    }
    if ($request->filled('processor')) {
      $query->where('processor', 'like', '%' . trim($request->processor) . '%');
    }

    if ($request->filled('ram')) {
      $query->where('ram', 'like', '%' . trim($request->ram) . '%');
    }

    if ($request->filled('system')) {
      $query->where('system', 'like', '%' . trim($request->system) . '%');
    }

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */
    $mapings = $query
      ->latest()
      ->paginate(10)
      ->appends($request->query());
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

    return view('content.dashboard.maping.create', compact('perusahaans', 'lokasis'));
  }

  public function getKategori(Request $request)
  {
    $query = Kategori::orderBy('nama_barang');

    if ($request->filled('id_perusahaan')) {
      $query->where('perusahaan_id', $request->id_perusahaan);
    }

    return response()->json($query->get());
  }
  public function getAset(Request $request)
  {
    $query = Inventaris::with([
      'dataAset.kategori',
      'keluarTerakhir.karyawan',
      'keluarTerakhir.lokasi',
      'keluarTerakhir.maping',
    ]);

    if ($request->filled('id_perusahaan')) {
      $query->where('perusahaan_id', $request->id_perusahaan);
    }

    $query->whereHas('dataAset', function ($q) use ($request) {
      $q->where('kategori_id', $request->id_kategori);
    });

    $query->where('status', 'DIPAKAI');

    $query->whereHas('keluarTerakhir');

    $query->whereDoesntHave('keluarTerakhir.maping', function ($q) {
      $q->whereIn('status', ['aktif', 'dipinjam', 'maintenance', 'servis']);
    });

    return response()->json(
      $query->get()->map(function ($inventaris) {
        $keluar = $inventaris->keluarTerakhir;

        if ($keluar->jenis_penerima == 'Perorangan') {
          $userAset = optional($keluar->karyawan)->nama_karyawan;
        } else {
          $userAset = $keluar->divisi_klr;
        }

        return [
          'id_keluar' => $keluar->id,

          'kode_aset' => $inventaris->kode_aset,

          'nama_barang' => optional($inventaris->dataAset->kategori)->nama_barang,

          'user_aset' => $userAset,

          'lokasi' => optional($keluar->lokasi)->nama_lokasi,

          'jenis_penerima' => $keluar->jenis_penerima,
        ];
      })
    );
  }
  public function getDetailAset($id)
  {
    $keluar = Keluar::with(['inventaris.dataAset.kategori', 'karyawan', 'lokasi', 'inventaris'])->findOrFail($id);
    /*
|--------------------------------------------------------------------------
| USER ASET
|--------------------------------------------------------------------------
*/

    $userAset = null;

    if ($keluar->jenis_penerima == 'Perorangan') {
      $userAset = optional($keluar->karyawan)->nama_karyawan;
    } else {
      $userAset = $keluar->divisi_klr;
    }

    return response()->json([
      'id_keluar' => $keluar->id,

      'kode_aset' => optional($keluar->inventaris)->kode_aset,

      'nama_barang' => optional(optional($keluar->inventaris)->dataAset->kategori)->nama_barang,

      'type' => optional($keluar->inventaris->dataAset)->type,

      'merek' => optional($keluar->inventaris->dataAset)->merek,

      'warna' => optional($keluar->inventaris->dataAset)->warna,

      'user_aset' => $userAset,

      'lokasi_id' => $keluar->lokasi_id,

      'nama_lokasi' => optional($keluar->lokasi)->nama_lokasi,

      'jenis_penerima' => $keluar->jenis_penerima,

      'divisi_klr' => $keluar->divisi_klr,

      'perusahaan_klr' => $keluar->perusahaan_klr,
    ]);
  }
  public function getAccess(Request $request)
  {
    $request->validate([
      'id_perusahaan' => 'required|exists:perusahaans,id',
    ]);

    return response()->json([
      'aplikasis' => Access::where('id_perusahaan', $request->id_perusahaan)
        ->where('kategori', 'Aplikasi')
        ->where('jenis', 'Software')
        ->where('status', 'aktif')
        ->orderBy('nama_akses')
        ->get(),

      'hakAksesPPN' => Access::where('id_perusahaan', $request->id_perusahaan)
        ->where('kategori', 'Hak Akses')
        ->where('jenis', 'PPN')
        ->where('status', 'aktif')
        ->orderBy('nama_akses')
        ->get(),

      'hakAksesNonPPN' => Access::where('id_perusahaan', $request->id_perusahaan)
        ->where('kategori', 'Hak Akses')
        ->where('jenis', 'NON PPN')
        ->where('status', 'aktif')
        ->orderBy('nama_akses')
        ->get(),
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
      // Ambil data transaksi keluar
      $keluar = Keluar::with('inventaris')->findOrFail($request->id_keluar);

      // Tentukan perusahaan
      $perusahaanId = $user->role == 'super_admin' ? $keluar->id_perusahaan : $user->id_perusahaan;

      // Pastikan inventaris belum pernah dimapping
      $cekMaping = Maping::where('id_keluar', $request->id_keluar)->exists();

      if ($cekMaping) {
        return back()->with('error', 'Inventaris tersebut sudah pernah dimapping.');
      }

      // Cek Device ID
      if ($request->filled('device_id')) {
        $device = Maping::where('device_id', $request->device_id)
          ->where('id_perusahaan', $perusahaanId)
          ->whereIn('status', ['aktif', 'dipinjam', 'servis', 'maintenance'])
          ->exists();

        if ($device) {
          return back()
            ->withInput()
            ->withErrors([
              'device_id' => 'Device ID sudah digunakan oleh aset yang masih aktif.',
            ]);
        }
      }

      // Cek Product ID
      if ($request->filled('produk_id')) {
        $produk = Maping::where('produk_id', $request->produk_id)
          ->where('id_perusahaan', $perusahaanId)
          ->whereIn('status', ['aktif', 'dipinjam', 'servis', 'maintenance'])
          ->exists();

        if ($produk) {
          return back()
            ->withInput()
            ->withErrors([
              'produk_id' => 'Product ID sudah digunakan oleh aset yang masih aktif.',
            ]);
        }
      }

      $maping = Maping::create([
        'id_keluar' => $request->id_keluar,
        'id_lokasi' => $request->id_lokasi,
        'id_perusahaan' => $perusahaanId,

        // =========================
        // PENERIMA SAAT INI
        // =========================
        'karyawan_id' => $keluar->jenis_penerima == 'Perorangan' ? $keluar->karyawan_id : null,

        'jenis_penerima' => $keluar->jenis_penerima,

        'divisi' => $keluar->jenis_penerima == 'Perdivisi' ? $keluar->divisi_klr : null,

        // =========================
        // INFORMASI DEVICE
        // =========================
        'processor' => strtoupper($request->processor),
        'ram' => strtoupper($request->ram),

        'device_id' => strtoupper($request->device_id),
        'produk_id' => strtoupper($request->produk_id),

        'system' => strtoupper($request->system),
        'version' => strtoupper($request->version),

        'instal_on' => $request->instal_on,

        'tanggal_digunakan' => $request->tanggal_digunakan,

        'catatan' => $request->catatan,

        'status' => 'aktif',
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
    $query = Maping::with([
      'lokasi',
      'perusahaan',
      'karyawan',
      'keluar.inventaris.dataAset.kategori',
      'mapingAccesses.access',
    ]);

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
      'keluar',
      'karyawan',
      'lokasi',
      'perusahaan',
    ])->findOrFail($id);

    if ($user->role != 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    $lokasis = Lokasi::where('id_perusahaan', $maping->id_perusahaan)
      ->orderBy('nama_lokasi')
      ->get();

    if (auth()->user()->role == 'super_admin') {
      $kategoris = Kategori::where('perusahaan_id', $maping->perusahaan_id)
        ->orderBy('nama_barang')
        ->get();
    } else {
      $kategoris = Kategori::where('perusahaan_id', auth()->user()->perusahaan_id)
        ->orderBy('nama_barang')
        ->get();
    }
    return view('content.dashboard.maping.edit', compact('maping', 'perusahaans', 'lokasis', 'kategoris'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $maping = Maping::findOrFail($id);

    $request->validate([
      'id_lokasi' => 'required|exists:lokasis,id',

      'processor' => 'nullable|string|max:100',
      'ram' => 'nullable|string|max:20',

      'device_id' => 'nullable|string|max:100',
      'produk_id' => 'nullable|string|max:100',

      'system' => 'nullable|string|max:100',
      'version' => 'nullable|string|max:100',

      'instal_on' => 'nullable|date',
      'tanggal_digunakan' => 'required|date',

      'catatan' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
      /*
        |--------------------------------------------------------------------------
        | VALIDASI DEVICE ID
        |--------------------------------------------------------------------------
        */

      if ($request->filled('device_id')) {
        $cek = Maping::where('device_id', $request->device_id)
          ->where('id_perusahaan', $maping->id_perusahaan)
          ->where('id', '!=', $maping->id)
          ->exists();

        if ($cek) {
          return back()
            ->withInput()
            ->with('error', 'Device ID sudah digunakan.');
        }
      }

      /*
        |--------------------------------------------------------------------------
        | VALIDASI PRODUCT ID
        |--------------------------------------------------------------------------
        */

      if ($request->filled('produk_id')) {
        $cek = Maping::where('produk_id', $request->produk_id)
          ->where('id_perusahaan', $maping->id_perusahaan)
          ->where('id', '!=', $maping->id)
          ->exists();

        if ($cek) {
          return back()
            ->withInput()
            ->with('error', 'Product ID sudah digunakan.');
        }
      }

      /*
        |--------------------------------------------------------------------------
        | UPDATE MAPING
        |--------------------------------------------------------------------------
        */

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

      DB::commit();

      return redirect()
        ->route('maping.index')
        ->with('success', 'Mapping berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error($e);

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
      'karyawan',
      'keluar.inventaris.masuk',
      'keluar.inventaris.dataAset.kategori',
      'mapingAccesses.access',
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

          ->orWhereHas('karyawan', function ($sub) use ($search) {
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
    foreach ($mapings as $maping) {
      $maping->aplikasis = $maping->mapingAccesses->filter(function ($item) {
        return $item->access && $item->access->kategori == 'Aplikasi';
      });

      $maping->hakAksesPPN = $maping->mapingAccesses->filter(function ($item) {
        return $item->access && $item->access->kategori == 'Hak Akses' && $item->access->jenis == 'PPN';
      });

      $maping->hakAksesNonPPN = $maping->mapingAccesses->filter(function ($item) {
        return $item->access && $item->access->kategori == 'Hak Akses' && $item->access->jenis == 'NON PPN';
      });
    }

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

  public function publicShow(int $id)
  {
    $maping = Maping::with(['perusahaan', 'lokasi', 'karyawan', 'keluar.inventaris.dataAset.kategori'])->findOrFail(
      $id
    );

    return view('content.dashboard.maping.public_show', compact('maping'));
  }
  public function exportExcel(Request $request)
  {
    $user = auth()->user();

    // Query sama seperti print()
    $query = Maping::with([
      'lokasi',
      'perusahaan',
      'karyawan',
      'keluar.inventaris.masuk',
      'keluar.inventaris.dataAset.kategori',
      'mapingAccesses.access',
    ]);

    if ($user->role != 'super_admin') {
      $query->where('id_perusahaan', $user->id_perusahaan);
    }

    // tambahkan filter yang sama seperti method print()

    $mapings = $query->get();

    foreach ($mapings as $maping) {
      $maping->aplikasis = $maping->mapingAccesses->where('access.kategori', 'Aplikasi');

      $maping->hakAksesPPN = $maping->mapingAccesses
        ->where('access.kategori', 'Hak Akses')
        ->where('access.jenis', 'PPN');

      $maping->hakAksesNonPPN = $maping->mapingAccesses
        ->where('access.kategori', 'Hak Akses')
        ->where('access.jenis', 'NON PPN');
    }

    $namaPerusahaan = $user->role == 'super_admin' ? 'SEMBILAN GROUP' : $user->perusahaan->nama_perusahaan;

    return Excel::download(new MapingExport($mapings, $namaPerusahaan), 'Laporan Mapping.xlsx');
  }
}

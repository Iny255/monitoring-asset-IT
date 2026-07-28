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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Exports\MapingExport;
use Maatwebsite\Excel\Facades\Excel;

class MapingController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  private function buildMapingQuery(Request $request)
  {
    $user = auth()->user();

    $query = Maping::with([
      'lokasi',
      'perusahaan',
      'karyawan',
      'keluar.inventaris.masuk',
      'keluar.inventaris.dataAset.kategori',
      'mapingAccesses.access',
    ]);

    // FILTER PERUSAHAAN
    $perusahaanId = $request->get('perusahaan_id', $request->get('perusahaan'));
    if ($user->role != 'super_admin') {
      $query->where('id_perusahaan', $user->id_perusahaan);
    } elseif (!empty($perusahaanId)) {
      $query->where('id_perusahaan', $perusahaanId);
    }

    // FILTER LOKASI
    $lokasiId = $request->get('lokasi_id', $request->get('lokasi'));
    if (!empty($lokasiId)) {
      $query->where('id_lokasi', $lokasiId);
    }

    // FILTER STATUS
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    // FILTER USER ASET
    if ($request->filled('karyawan_id')) {
      $query->where('karyawan_id', $request->karyawan_id);
    }

    // FILTER KATEGORI
    $kategoriId = $request->get('kategori_id', $request->get('barang'));
    if (!empty($kategoriId)) {
      $query->whereHas('keluar.inventaris.dataAset', function ($q) use ($kategoriId) {
        $q->where('kategori_id', $kategoriId);
      });
    }

    // FILTER RENTANG TANGGAL
    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
      $query->whereBetween('tanggal_digunakan', [$request->tanggal_awal, $request->tanggal_akhir]);
    } elseif ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_digunakan', '>=', $request->tanggal_awal);
    } elseif ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_digunakan', '<=', $request->tanggal_akhir);
    }

    // FILTER TAHUN PEMBELIAN
    if ($request->filled('tahun')) {
      $query->whereHas('keluar.inventaris.masuk', function ($q) use ($request) {
        $q->whereYear('tanggal_pembelian', $request->tahun);
      });
    }

    // FILTER MEREK
    if ($request->filled('merek')) {
      $query->whereHas('keluar.inventaris.dataAset', function ($q) use ($request) {
        $q->where('merek', 'like', '%' . $request->merek . '%');
      });
    }

    // FILTER TYPE
    if ($request->filled('type')) {
      $query->whereHas('keluar.inventaris.dataAset', function ($q) use ($request) {
        $q->where('type', 'like', '%' . $request->type . '%');
      });
    }

    // SPEC FILTERS
    if ($request->filled('processor')) {
      $query->where('processor', 'like', '%' . trim($request->processor) . '%');
    }

    if ($request->filled('ram')) {
      $query->where('ram', 'like', '%' . trim($request->ram) . '%');
    }

    if ($request->filled('system')) {
      $query->where('system', 'like', '%' . trim($request->system) . '%');
    }

    // SEARCH GLOBAL
    if ($request->filled('search')) {
      $search = trim($request->search);

      $query->where(function ($q) use ($search) {
        $q->where('processor', 'like', "%{$search}%")
          ->orWhere('ram', 'like', "%{$search}%")
          ->orWhere('device_id', 'like', "%{$search}%")
          ->orWhere('produk_id', 'like', "%{$search}%")
          ->orWhere('system', 'like', "%{$search}%")
          ->orWhere('version', 'like', "%{$search}%")
          ->orWhere('catatan', 'like', "%{$search}%")

          ->orWhereHas('keluar.inventaris', function ($sub) use ($search) {
            $sub->where('kode_aset', 'like', "%{$search}%")
              ->orWhere('no_inventaris', 'like', "%{$search}%");
          })

          ->orWhereHas('karyawan', function ($sub) use ($search) {
            $sub->where('nama_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('keluar.inventaris.dataAset', function ($sub) use ($search) {
            $sub->where('merek', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%")
              ->orWhere('warna', 'like', "%{$search}%");
          })

          ->orWhereHas('keluar.inventaris.dataAset.kategori', function ($sub) use ($search) {
            $sub->where('nama_barang', 'like', "%{$search}%");
          })

          ->orWhereHas('lokasi', function ($sub) use ($search) {
            $sub->where('nama_lokasi', 'like', "%{$search}%");
          })

          ->orWhereHas('perusahaan', function ($sub) use ($search) {
            $sub->where('nama_perusahaan', 'like', "%{$search}%");
          });
      });
    }

    return $query;
  }

  public function index(Request $request)
  {
    $user = auth()->user();
    $query = $this->buildMapingQuery($request);

    if (!$request->filled('status')) {
      $query->where('status', '!=', 'selesai');
    }

    $mapings = $query
      ->latest()
      ->paginate(10)
      ->appends($request->query());

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    $selectedPerusahaanId = $user->role == 'super_admin' ? $request->get('perusahaan_id') : $user->id_perusahaan;

    $lokasis = Lokasi::query()
      ->when($selectedPerusahaanId, fn($q) => $q->where('id_perusahaan', $selectedPerusahaanId))
      ->orderBy('nama_lokasi')
      ->get();

    $karyawans = Karyawan::query()
      ->when($selectedPerusahaanId, fn($q) => $q->where('id_perusahaan', $selectedPerusahaanId))
      ->orderBy('nama_karyawan')
      ->get();

    $kategoris = Kategori::query()
      ->when($selectedPerusahaanId, fn($q) => $q->where('perusahaan_id', $selectedPerusahaanId))
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
  public function pemakaian(Request $request)
  {
    $user = auth()->user();

    $query = Inventaris::with(['dataAset.kategori', 'perusahaan', 'keluarTerakhir.karyawan', 'keluarTerakhir.lokasi']);

    // FILTER PERUSAHAAN
    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    // FILTER KATEGORI
    if ($request->filled('kategori_id')) {
      $query->whereHas('dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->kategori_id);
      });
    }

    // SEARCH
    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        $q->where('no_inventaris', 'like', "%{$search}%")
          ->orWhere('kode_aset', 'like', "%{$search}%")
          ->orWhereHas('dataAset.kategori', function ($qq) use ($search) {
            $qq->where('nama_barang', 'like', "%{$search}%");
          })
          ->orWhereHas('dataAset', function ($qq) use ($search) {
            $qq->where('merek', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%");
          });
      });
    }

    $inventarisAll = $query->get();

    // Grouping per Data Aset
    $stoks = $inventarisAll
      ->groupBy('data_aset_id')
      ->map(function ($items) {
        $first = $items->first();

        // Ambil daftar pemakai yang sedang memakai unit (DIPAKAI)
        $pemakai = collect();
        foreach ($items as $inv) {
          if ($inv->status == 'DIPAKAI' && $inv->keluarTerakhir) {
            if ($inv->keluarTerakhir->jenis_penerima == 'Perorangan') {
              $pemakai->push($inv->keluarTerakhir->karyawan?->nama_karyawan);
            } else {
              $pemakai->push($inv->keluarTerakhir->divisi_klr);
            }
          }
        }

        return (object) [
          'data_aset_id' => $first->data_aset_id,
          'perusahaan' => $first->perusahaan,
          'kategori' => $first->dataAset?->kategori,
          'merek' => $first->dataAset?->merek,
          'type' => $first->dataAset?->type,
          'total_aset' => $items->count(),
          'tersedia' => $items->where('status', 'TERSEDIA')->count(),
          'dipakai' => $items->where('status', 'DIPAKAI')->count(),
          'dipinjam' => $items->where('status', 'DIPINJAM')->count(),
          'rusak' => $items->where('status', 'RUSAK')->count(),
          'afkir' => $items->where('status', 'AFKIR')->count(),
          'pemakai' => $pemakai->filter()->unique(),
          'inventaris' => $items,
        ];
      })
      ->values();

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    $selectedPerusahaanId = $user->role == 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;
    $kategoris = Kategori::query()
      ->when($selectedPerusahaanId, fn($q) => $q->where('perusahaan_id', $selectedPerusahaanId))
      ->orderBy('nama_barang')
      ->get();

    return view('content.dashboard.maping.pemakaian', compact(
      'stoks',
      'perusahaans',
      'kategoris',
      'user'
    ));
  }

  public function create(Request $request)
  {
    $user = auth()->user();

    $perusahaanId = $user->role == 'super_admin' ? ($request->perusahaan_id ?? null) : $user->id_perusahaan;

    $perusahaans = $user->role == 'super_admin'
      ? Perusahaan::orderBy('nama_perusahaan')->get()
      : collect();

    $lokasis = Lokasi::when($perusahaanId, fn($q) => $q->where('id_perusahaan', $perusahaanId))
      ->orderBy('nama_lokasi')
      ->get();

    $karyawans = Karyawan::when($perusahaanId, fn($q) => $q->where('id_perusahaan', $perusahaanId))
      ->orderBy('nama_karyawan')
      ->get();

    $kategoris = Kategori::when($perusahaanId, fn($q) => $q->where('perusahaan_id', $perusahaanId))
      ->orderBy('nama_barang')
      ->get();

    $inventarisAvailable = Inventaris::with(['dataAset.kategori'])
      ->where('status', 'TERSEDIA')
      ->where('is_transfer', false)
      ->when($perusahaanId, fn($q) => $q->where('perusahaan_id', $perusahaanId))
      ->when($request->filled('data_aset_id'), fn($q) => $q->where('data_aset_id', $request->data_aset_id))
      ->orderBy('kode_aset')
      ->get();

    $selectedDataAsetId = $request->data_aset_id;

    return view('content.dashboard.maping.create', compact(
      'perusahaans',
      'lokasis',
      'karyawans',
      'kategoris',
      'inventarisAvailable',
      'selectedDataAsetId'
    ));
  }

  public function getKategori(Request $request)
  {
    $user = auth()->user();
    $query = Kategori::orderBy('nama_barang');

    $perusahaanId = $request->id_perusahaan;
    if ($user->role != 'super_admin') {
      $perusahaanId = $user->id_perusahaan;
    }

    if ($perusahaanId) {
      $query->where('perusahaan_id', $perusahaanId);
    }

    return response()->json($query->get());
  }

  public function getAvailableInventaris(Request $request)
  {
    $user = auth()->user();

    $query = Inventaris::with('dataAset.kategori')
      ->where('status', 'TERSEDIA')
      ->where('is_transfer', false);

    $perusahaanId = $request->id_perusahaan;
    if ($user->role != 'super_admin') {
      $perusahaanId = $user->id_perusahaan;
    }

    if ($perusahaanId) {
      $query->where('perusahaan_id', $perusahaanId);
    }

    if ($request->filled('id_kategori')) {
      $query->whereHas('dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->id_kategori);
      });
    }

    $items = $query->orderBy('kode_aset')->get()->map(function ($item) {
      return [
        'id' => $item->id,
        'no_inventaris' => $item->no_inventaris,
        'kode_aset' => $item->kode_aset,
        'nama_barang' => $item->dataAset?->kategori?->nama_barang ?? '-',
        'merek' => $item->dataAset?->merek ?? '-',
        'type' => $item->dataAset?->type ?? '-',
      ];
    });

    return response()->json($items);
  }

  public function searchKaryawan(Request $request)
  {
    $user = auth()->user();
    $keyword = $request->q ?? $request->keyword ?? $request->search;
    $perusahaanId = $request->id_perusahaan ?? $request->perusahaan_id;

    if ($user->role != 'super_admin') {
      $perusahaanId = $user->id_perusahaan;
    }

    $karyawan = Karyawan::query()
      ->when($perusahaanId, function ($q) use ($perusahaanId) {
        $q->where('id_perusahaan', $perusahaanId);
      })
      ->when($keyword, function ($q) use ($keyword) {
        $q->where(function ($sub) use ($keyword) {
          $sub->where('kode_karyawan', 'like', "%{$keyword}%")
            ->orWhere('nama_karyawan', 'like', "%{$keyword}%")
            ->orWhere('divisi', 'like', "%{$keyword}%");
        });
      })
      ->orderBy('nama_karyawan')
      ->limit(15)
      ->get(['id', 'kode_karyawan', 'nama_karyawan', 'divisi']);

    return response()->json($karyawan);
  }
  public function getAset(Request $request)
  {
    $query = Inventaris::with([
      'dataAset.kategori',
      'keluarTerakhir.karyawan',
      'keluarTerakhir.lokasi',
      'keluarTerakhir.maping',
    ]);

    /*
    |--------------------------------------------------------------------------
    | FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */
    if ($request->filled('id_perusahaan')) {
      $query->where('perusahaan_id', $request->id_perusahaan);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER KATEGORI
    |--------------------------------------------------------------------------
    */
    if ($request->filled('id_kategori')) {
      $query->whereHas('dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->id_kategori);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | HANYA ASET YANG SUDAH DIGUNAKAN
    |--------------------------------------------------------------------------
    */
    $query->where('status', 'DIPAKAI');

    /*
    |--------------------------------------------------------------------------
    | INVENTARIS YANG BELUM PINDAH PERUSAHAAN
    |--------------------------------------------------------------------------
    */
    $query->where('is_transfer', false);

    /*
    |--------------------------------------------------------------------------
    | HARUS SUDAH PERNAH KELUAR
    |--------------------------------------------------------------------------
    */
    $query->whereHas('keluarTerakhir');

   

    /*
|--------------------------------------------------------------------------
| TIDAK SEDANG SERVICE
|--------------------------------------------------------------------------
*/

    $query->availableForMapping();
 
    $inventaris = $query
      ->orderBy('kode_aset')
      ->get()
      ->map(function ($item) {
        return [
          'id_keluar' => optional($item->keluarTerakhir)->id,

          'kode_aset' => $item->kode_aset,
        ];
      });

    return response()->json($inventaris);
  }
  public function getDetailAset(string $id)
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
      'inventaris_id' => 'nullable|exists:inventaris,id',
      'id_keluar' => 'nullable|exists:keluars,id',
      'id_lokasi' => 'required|exists:lokasis,id',
      'jenis_penerima' => 'nullable|in:Perorangan,Per Divisi',
      'karyawan_id' => 'nullable|exists:karyawans,id',
      'divisi' => 'nullable|string|max:100',
      'processor' => 'nullable|string|max:100',
      'ram' => 'nullable|string|max:20',
      'device_id' => 'nullable|string|max:100',
      'produk_id' => 'nullable|string|max:100',
      'system' => 'nullable|string|max:50',
      'version' => 'nullable|string|max:50',
      'instal_on' => 'nullable|date',
      'tanggal_digunakan' => 'required|date',
      'catatan' => 'nullable|string',
      'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
      'accesses' => 'nullable|array',
      'accesses.*' => 'exists:accesses,id',
    ]);

    DB::beginTransaction();

    try {
      $gambarPath = null;
      if ($request->hasFile('gambar')) {
        $gambarPath = $request->file('gambar')->store('transaksi-keluar', 'public');
      }

      if ($request->filled('id_keluar')) {
        $keluar = Keluar::with('inventaris')->findOrFail($request->id_keluar);
        $inventaris = $keluar->inventaris;
        if ($gambarPath) {
          $keluar->update(['gambar' => $gambarPath]);
        }
      } else {
        if (!$request->filled('inventaris_id')) {
          return back()->withInput()->with('error', 'Silakan pilih unit inventaris yang akan dialokasikan.');
        }

        $inventaris = Inventaris::findOrFail($request->inventaris_id);

        $perusahaanIdInput = $user->role == 'super_admin' ? ($request->id_perusahaan ?? $inventaris->perusahaan_id) : $user->id_perusahaan;

        // 1. Buat record Transaksi Keluar otomatis
        $keluar = Keluar::create([
          'inventaris_id' => $inventaris->id,
          'perusahaan_id' => $perusahaanIdInput,
          'karyawan_id' => $request->jenis_penerima == 'Perorangan' ? $request->karyawan_id : null,
          'lokasi_id' => $request->id_lokasi,
          'tgl_keluar' => $request->tanggal_digunakan,
          'jenis_penerima' => $request->jenis_penerima ?? 'Perorangan',
          'divisi_klr' => $request->jenis_penerima == 'Per Divisi' ? $request->divisi : null,
          'gambar' => $gambarPath,
          'created_by' => $user->id,
        ]);

        // 2. Ubah status inventaris menjadi DIPAKAI
        $inventaris->update(['status' => 'DIPAKAI']);
      }

      $perusahaanId = $user->role == 'super_admin' ? ($request->id_perusahaan ?? $keluar->perusahaan_id ?? $keluar->id_perusahaan) : $user->id_perusahaan;

      // Cek apakah id_keluar sudah pernah dimapping
      $cekMaping = Maping::where('id_keluar', $keluar->id)->exists();
      if ($cekMaping) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Inventaris tersebut sudah pernah dimapping.');
      }

      // Cek Device ID & Product ID unik jika diisi
      if ($request->filled('device_id')) {
        $deviceExists = Maping::where('device_id', strtoupper($request->device_id))
          ->where('id_perusahaan', $perusahaanId)
          ->whereIn('status', ['aktif', 'dipinjam', 'servis', 'maintenance'])
          ->exists();

        if ($deviceExists) {
          DB::rollBack();
          return back()->withInput()->withErrors(['device_id' => 'Device ID sudah digunakan oleh aset aktif lainnya.']);
        }
      }

      if ($request->filled('produk_id')) {
        $produkExists = Maping::where('produk_id', strtoupper($request->produk_id))
          ->where('id_perusahaan', $perusahaanId)
          ->whereIn('status', ['aktif', 'dipinjam', 'servis', 'maintenance'])
          ->exists();

        if ($produkExists) {
          DB::rollBack();
          return back()->withInput()->withErrors(['produk_id' => 'Product ID sudah digunakan oleh aset aktif lainnya.']);
        }
      }

      $jenisPenerima = $request->jenis_penerima ?? $keluar->jenis_penerima ?? 'Perorangan';
      $karyawanId = $jenisPenerima == 'Perorangan' ? ($request->karyawan_id ?? $keluar->karyawan_id) : null;
      $divisi = $jenisPenerima == 'Per Divisi' ? ($request->divisi ?? $keluar->divisi_klr) : null;

      $maping = Maping::create([
        'uuid' => (string) Str::uuid(),
        'id_keluar' => $keluar->id,
        'id_lokasi' => $request->id_lokasi,
        'id_perusahaan' => $perusahaanId,
        'karyawan_id' => $karyawanId,
        'jenis_penerima' => $jenisPenerima,
        'divisi' => $divisi,
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

      if ($request->filled('accesses')) {
        foreach ($request->accesses as $accessId) {
          MapingAccess::create([
            'maping_id' => $maping->id,
            'access_id' => $accessId,
          ]);
        }
      }

      DB::commit();

      $maping->loadMissing('keluar.inventaris');
      $dataAsetId = $maping->keluar?->inventaris?->data_aset_id;
      $targetUrl = $dataAsetId ? route('history.perjalanan.show', $dataAsetId) : route('history.perjalanan.index');

      return redirect($targetUrl)
        ->with('success', 'Mapping Aset berhasil disimpan dan barang telah dikeluarkan dari stok.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Error store mapping: ' . $e->getMessage());
      return back()->withInput()->with('error', 'Gagal menyimpan Mapping Aset: ' . $e->getMessage());
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
      'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
    ]);

    DB::beginTransaction();

    try {
      if ($request->hasFile('gambar') && $maping->keluar) {
        if ($maping->keluar->gambar && Storage::disk('public')->exists($maping->keluar->gambar)) {
          Storage::disk('public')->delete($maping->keluar->gambar);
        }
        $gambarPath = $request->file('gambar')->store('transaksi-keluar', 'public');
        $maping->keluar->update(['gambar' => $gambarPath]);
      }
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

      return redirect()
        ->route('maping.index')
        ->with('success', 'Mapping berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menghapus mapping.');
    }
  }

  public function print(Request $request)
  {
    $user = auth()->user();

    $query = $this->buildMapingQuery($request);

    if (!$request->filled('status')) {
      $query->where('status', 'aktif');
    }

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

    $perusahaanId = $request->get('perusahaan_id', $request->get('perusahaan'));
    if ($user->role === 'super_admin') {
      $namaPerusahaan = !empty($perusahaanId)
        ? optional(Perusahaan::find($perusahaanId))->nama_perusahaan
        : 'SEMBILAN GROUP';
    } else {
      $namaPerusahaan = $user->perusahaan->nama_perusahaan ?? 'Perusahaan';
    }

    return view('content.dashboard.maping.print', compact('mapings', 'namaPerusahaan'));
  }

  public function getFilterOptionsByPerusahaan(Request $request)
  {
    $perusahaanId = $request->get('perusahaan_id');

    $lokasis = Lokasi::query()
      ->when($perusahaanId, fn($q) => $q->where('id_perusahaan', $perusahaanId))
      ->orderBy('nama_lokasi')
      ->get(['id', 'nama_lokasi']);

    $kategoris = Kategori::query()
      ->when($perusahaanId, fn($q) => $q->where('perusahaan_id', $perusahaanId))
      ->orderBy('nama_barang')
      ->get(['id', 'nama_barang']);

    return response()->json([
      'lokasis' => $lokasis,
      'kategoris' => $kategoris,
    ]);
  }

  public function getLokasiByPerusahaan(int $id)
  {
    $lokasis = Lokasi::where('id_perusahaan', $id)
      ->orderBy('nama_lokasi')
      ->get();

    return response()->json($lokasis);
  }

  public function publicShow(string $identifier)
  {
    $query = Maping::withoutGlobalScopes()->with([
      'perusahaan',
      'lokasi',
      'karyawan',
      'keluar.inventaris.dataAset.kategori',
    ]);

    if (Str::isUuid($identifier)) {
      $maping = $query->where('uuid', $identifier)->firstOrFail();
    } else {
      $maping = $query->where('id', $identifier)->firstOrFail();
    }

    return view('content.dashboard.maping.public_show', compact('maping'));
  }

  public function exportExcel(Request $request)
  {
    $user = auth()->user();

    $query = $this->buildMapingQuery($request);

    if (!$request->filled('status')) {
      $query->where('status', 'aktif');
    }

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

    $perusahaanId = $request->get('perusahaan_id', $request->get('perusahaan'));
    $namaPerusahaan = $user->role == 'super_admin'
      ? (!empty($perusahaanId) ? optional(Perusahaan::find($perusahaanId))->nama_perusahaan : 'SEMBILAN GROUP')
      : ($user->perusahaan->nama_perusahaan ?? 'Perusahaan');

    return Excel::download(new MapingExport($mapings, $namaPerusahaan), 'Laporan Mapping.xlsx');
  }
}

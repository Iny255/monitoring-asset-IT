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
use App\Models\ChecklistRuangan;
use App\Models\ChecklistDevice;
use App\Models\ChecklistDeviceItem;
use App\Models\ChecklistItem;
use App\Models\ChecklistJadwalRutin;
use App\Models\Peminjaman;
use Illuminate\Support\Carbon;
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

    // FILTER RENTANG TANGGAL TRANSAKSI (tanggal_digunakan / tgl_keluar / created_at)
    if ($request->filled('tanggal_awal') || $request->filled('tanggal_akhir')) {
      $query->where(function ($q) use ($request) {
        $q->where(function ($sub) use ($request) {
          $sub->whereNotNull('tanggal_digunakan');
          if ($request->filled('tanggal_awal')) {
            $sub->whereDate('tanggal_digunakan', '>=', $request->tanggal_awal);
          }
          if ($request->filled('tanggal_akhir')) {
            $sub->whereDate('tanggal_digunakan', '<=', $request->tanggal_akhir);
          }
        })->orWhere(function ($sub) use ($request) {
          $sub->whereNull('tanggal_digunakan')
            ->whereHas('keluar', function ($kq) use ($request) {
              if ($request->filled('tanggal_awal')) {
                $kq->whereDate('tgl_keluar', '>=', $request->tanggal_awal);
              }
              if ($request->filled('tanggal_akhir')) {
                $kq->whereDate('tgl_keluar', '<=', $request->tanggal_akhir);
              }
            });
        })->orWhere(function ($sub) use ($request) {
          $sub->whereNull('tanggal_digunakan')
            ->whereDoesntHave('keluar')
            ->where(function ($cq) use ($request) {
              if ($request->filled('tanggal_awal')) {
                $cq->whereDate('created_at', '>=', $request->tanggal_awal);
              }
              if ($request->filled('tanggal_akhir')) {
                $cq->whereDate('created_at', '<=', $request->tanggal_akhir);
              }
            });
        });
      });
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
      'jenis_penerima' => 'nullable|in:Perorangan,Per Divisi,Perdivisi',
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

      $rawJenisInput = $request->jenis_penerima;
      $jenisPenerimaDb = in_array($rawJenisInput, ['Per Divisi', 'Perdivisi']) ? 'Perdivisi' : 'Perorangan';

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
          'karyawan_id' => $jenisPenerimaDb == 'Perorangan' ? $request->karyawan_id : null,
          'lokasi_id' => $request->id_lokasi,
          'tgl_keluar' => $request->tanggal_digunakan,
          'jenis_penerima' => $jenisPenerimaDb,
          'divisi_klr' => $jenisPenerimaDb == 'Perdivisi' ? $request->divisi : null,
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

      // Cek Device ID & Product ID unik jika diisi (abaikan placeholder umum)
      $placeholders = ['-', 'N/A', 'NA', 'NONE', 'TIDAK ADA', '0'];

      if ($request->filled('device_id')) {
        $cleanDeviceId = strtoupper(trim($request->device_id));
        if (!in_array($cleanDeviceId, $placeholders)) {
          $deviceExists = Maping::where('device_id', $cleanDeviceId)
            ->where('id_perusahaan', $perusahaanId)
            ->whereIn('status', ['aktif', 'dipinjam', 'servis', 'maintenance'])
            ->exists();

          if ($deviceExists) {
            DB::rollBack();
            return back()->withInput()->withErrors(['device_id' => 'Device ID sudah digunakan oleh aset aktif lainnya.']);
          }
        }
      }

      if ($request->filled('produk_id')) {
        $cleanProdukId = strtoupper(trim($request->produk_id));
        if (!in_array($cleanProdukId, $placeholders)) {
          $produkExists = Maping::where('produk_id', $cleanProdukId)
            ->where('id_perusahaan', $perusahaanId)
            ->whereIn('status', ['aktif', 'dipinjam', 'servis', 'maintenance'])
            ->exists();

          if ($produkExists) {
            DB::rollBack();
            return back()->withInput()->withErrors(['produk_id' => 'Product ID sudah digunakan oleh aset aktif lainnya.']);
          }
        }
      }

      $jenisPenerimaRaw = $request->jenis_penerima ?? $keluar->jenis_penerima ?? 'Perorangan';
      $jenisPenerimaFinal = in_array($jenisPenerimaRaw, ['Per Divisi', 'Perdivisi']) ? 'Perdivisi' : 'Perorangan';

      $karyawanId = $jenisPenerimaFinal == 'Perorangan' ? ($request->karyawan_id ?? $keluar->karyawan_id) : null;
      $divisi = $jenisPenerimaFinal == 'Perdivisi' ? ($request->divisi ?? $keluar->divisi_klr) : null;

      $maping = Maping::create([
        'uuid' => (string) Str::uuid(),
        'id_keluar' => $keluar->id,
        'id_lokasi' => $request->id_lokasi,
        'id_perusahaan' => $perusahaanId,
        'karyawan_id' => $karyawanId,
        'jenis_penerima' => $jenisPenerimaFinal,
        'divisi' => $divisi,
        'processor' => $request->filled('processor') ? strtoupper(trim($request->processor)) : null,
        'ram' => $request->filled('ram') ? strtoupper(trim($request->ram)) : null,
        'device_id' => $request->filled('device_id') ? strtoupper(trim($request->device_id)) : null,
        'produk_id' => $request->filled('produk_id') ? strtoupper(trim($request->produk_id)) : null,
        'system' => $request->filled('system') ? strtoupper(trim($request->system)) : null,
        'version' => $request->filled('version') ? strtoupper(trim($request->version)) : null,
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

      return redirect()
        ->route('maping.index')
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
      'keluar.inventaris.masuk',
      'mapingAccesses.access',
    ]);

    if (auth()->user()->role != 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $maping = $query->findOrFail($id);

    // Ambil status checklist terkini untuk perangkat ini
    $latestChecklist = ChecklistDevice::where('maping_id', $maping->id)
      ->with(['checklistRuangan.lokasi', 'checkedBy', 'items'])
      ->orderByDesc('checked_at')
      ->orderByDesc('id')
      ->first();

    // Cek apakah ada jadwal checklist hari ini
    $todayChecklist = ChecklistDevice::where('maping_id', $maping->id)
      ->whereHas('checklistRuangan', function ($q) {
        $q->whereDate('tanggal_pemeriksaan', date('Y-m-d'))
          ->orWhere(function ($sub) {
            $sub->whereNull('tanggal_pemeriksaan')
                ->whereDate('tanggal_cek', date('Y-m-d'));
          });
      })
      ->with(['checklistRuangan.lokasi', 'checkedBy', 'items'])
      ->first();

    // Ambil 5 riwayat checklist terakhir
    $checklistHistory = ChecklistDevice::where('maping_id', $maping->id)
      ->where('status_device', '!=', 'belum_dicek')
      ->with(['checklistRuangan.lokasi', 'checkedBy', 'items'])
      ->orderByDesc('checked_at')
      ->take(5)
      ->get();

    // Master item checklist
    $masterItems = ChecklistItem::where('is_active', true)
      ->where(function ($q) use ($maping) {
        $q->whereNull('id_perusahaan')
          ->orWhere('id_perusahaan', $maping->id_perusahaan);
      })
      ->orderBy('urutan')
      ->get();

    // Ruangan checklist terkait untuk tombol navigasi kembali
    $ruanganId = request('ruangan_id');
    $checklistRuangan = null;
    if ($ruanganId) {
      $checklistRuangan = ChecklistRuangan::withoutGlobalScopes()->with('lokasi')->find($ruanganId);
    }
    if (!$checklistRuangan && $todayChecklist) {
      $checklistRuangan = $todayChecklist->checklistRuangan;
    }
    if (!$checklistRuangan && $latestChecklist) {
      $checklistRuangan = $latestChecklist->checklistRuangan;
    }

    return view('content.dashboard.maping.show', compact(
      'maping',
      'latestChecklist',
      'todayChecklist',
      'checklistHistory',
      'masterItems',
      'checklistRuangan'
    ));
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

    $perusahaanId = $maping->id_perusahaan;

    $perusahaans = $user->role == 'super_admin'
      ? Perusahaan::orderBy('nama_perusahaan')->get()
      : collect();

    $lokasis = Lokasi::where('id_perusahaan', $perusahaanId)
      ->orderBy('nama_lokasi')
      ->get();

    $karyawans = Karyawan::where('id_perusahaan', $perusahaanId)
      ->orderBy('nama_karyawan')
      ->get();

    $kategoris = Kategori::where('perusahaan_id', $perusahaanId)
      ->orderBy('nama_barang')
      ->get();

    // Unit inventaris saat ini
    $currentInventaris = $maping->keluar?->inventaris;

    // Unit inventaris yang tersedia jika ingin tukar unit (status TERSEDIA dari perusahaan yang sama)
    $availableInventaris = Inventaris::with(['dataAset.kategori'])
      ->where('perusahaan_id', $perusahaanId)
      ->where('is_transfer', false)
      ->where('status', 'TERSEDIA')
      ->orderBy('kode_aset')
      ->get();

    return view('content.dashboard.maping.edit', compact(
      'maping',
      'perusahaans',
      'lokasis',
      'karyawans',
      'kategoris',
      'currentInventaris',
      'availableInventaris'
    ));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $user = auth()->user();
    $maping = Maping::with(['keluar.inventaris'])->findOrFail($id);

    if ($user->role != 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    $request->validate([
      'id_lokasi' => 'required|exists:lokasis,id',
      'jenis_penerima' => 'required|in:Perorangan,Per Divisi,Perdivisi',
      'karyawan_id' => 'nullable|exists:karyawans,id',
      'divisi' => 'nullable|string|max:100',
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
      'inventaris_id' => 'nullable|exists:inventaris,id',
    ]);

    if ($request->jenis_penerima === 'Perorangan' && !$request->filled('karyawan_id')) {
      return back()->withInput()->with('error', 'Silakan pilih karyawan penerima aset.');
    }

    if (in_array($request->jenis_penerima, ['Per Divisi', 'Perdivisi']) && !$request->filled('divisi')) {
      return back()->withInput()->with('error', 'Silakan isi nama divisi penerima aset.');
    }

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
        | VALIDASI DEVICE ID & PRODUCT ID (KECUALIKAN MAPPING INI & DATA TIDAK BERUBAH)
        |--------------------------------------------------------------------------
        */
      $placeholders = ['-', 'N/A', 'NA', 'NONE', 'TIDAK ADA', '0'];
      $currentInventarisId = $maping->keluar?->inventaris_id;
      $targetInventarisId = $request->filled('inventaris_id') ? $request->inventaris_id : $currentInventarisId;

      if ($request->filled('device_id')) {
        $newDeviceId = strtoupper(trim($request->device_id));
        $oldDeviceId = strtoupper(trim($maping->device_id ?? ''));

        // Hanya validasi jika Device ID diubah dan bukan placeholder umum
        if ($newDeviceId !== $oldDeviceId && !in_array($newDeviceId, $placeholders)) {
          $cek = Maping::where('device_id', $newDeviceId)
            ->where('id_perusahaan', $maping->id_perusahaan)
            ->where('id', '!=', $maping->id)
            ->whereIn('status', ['aktif', 'dipinjam', 'servis', 'maintenance'])
            ->when($targetInventarisId, function ($q) use ($targetInventarisId) {
              $q->whereDoesntHave('keluar', function ($sub) use ($targetInventarisId) {
                $sub->where('inventaris_id', $targetInventarisId);
              });
            })
            ->exists();

          if ($cek) {
            return back()
              ->withInput()
              ->with('error', 'Device ID sudah digunakan oleh aset aktif lainnya.')
              ->withErrors(['device_id' => 'Device ID sudah digunakan oleh aset aktif lainnya.']);
          }
        }
      }

      if ($request->filled('produk_id')) {
        $newProdukId = strtoupper(trim($request->produk_id));
        $oldProdukId = strtoupper(trim($maping->produk_id ?? ''));

        // Hanya validasi jika Product ID diubah dan bukan placeholder umum
        if ($newProdukId !== $oldProdukId && !in_array($newProdukId, $placeholders)) {
          $cek = Maping::where('produk_id', $newProdukId)
            ->where('id_perusahaan', $maping->id_perusahaan)
            ->where('id', '!=', $maping->id)
            ->whereIn('status', ['aktif', 'dipinjam', 'servis', 'maintenance'])
            ->when($targetInventarisId, function ($q) use ($targetInventarisId) {
              $q->whereDoesntHave('keluar', function ($sub) use ($targetInventarisId) {
                $sub->where('inventaris_id', $targetInventarisId);
              });
            })
            ->exists();

          if ($cek) {
            return back()
              ->withInput()
              ->with('error', 'Product ID sudah digunakan oleh aset aktif lainnya.')
              ->withErrors(['produk_id' => 'Product ID sudah digunakan oleh aset aktif lainnya.']);
          }
        }
      }

      $rawJenisInput = $request->jenis_penerima;
      $jenisPenerimaDb = in_array($rawJenisInput, ['Per Divisi', 'Perdivisi']) ? 'Perdivisi' : 'Perorangan';
      $karyawanId = $jenisPenerimaDb === 'Perorangan' ? $request->karyawan_id : null;
      $divisi = $jenisPenerimaDb === 'Perdivisi' ? $request->divisi : null;

      // Jika user memilih untuk menukar unit inventaris
      if ($request->filled('inventaris_id') && $maping->keluar && $request->inventaris_id != $maping->keluar->inventaris_id) {
        $oldInventaris = Inventaris::find($maping->keluar->inventaris_id);
        $newInventaris = Inventaris::findOrFail($request->inventaris_id);

        if ($newInventaris->status !== 'TERSEDIA') {
          return back()->withInput()->with('error', 'Unit inventaris yang dipilih tidak tersedia.');
        }

        // Kembalikan status inventaris lama ke TERSEDIA
        if ($oldInventaris) {
          $oldInventaris->update(['status' => 'TERSEDIA']);
        }

        // Ubah inventaris baru ke DIPAKAI
        $newInventaris->update(['status' => 'DIPAKAI']);

        // Update di keluar
        $maping->keluar->update(['inventaris_id' => $newInventaris->id]);
      }

      // Update di keluar agar sinkron
      if ($maping->keluar) {
        $maping->keluar->update([
          'karyawan_id' => $karyawanId,
          'lokasi_id' => $request->id_lokasi,
          'tgl_keluar' => $request->tanggal_digunakan,
          'jenis_penerima' => $jenisPenerimaDb,
          'divisi_klr' => $divisi,
        ]);
      }

      $oldLokasiId = $maping->id_lokasi;

      // Update di Maping
      $maping->update([
        'id_lokasi' => $request->id_lokasi,
        'karyawan_id' => $karyawanId,
        'jenis_penerima' => $jenisPenerimaDb,
        'divisi' => $divisi,
        'processor' => $request->filled('processor') ? strtoupper(trim($request->processor)) : null,
        'ram' => $request->filled('ram') ? strtoupper(trim($request->ram)) : null,
        'device_id' => $request->filled('device_id') ? strtoupper(trim($request->device_id)) : null,
        'produk_id' => $request->filled('produk_id') ? strtoupper(trim($request->produk_id)) : null,
        'system' => $request->filled('system') ? strtoupper(trim($request->system)) : null,
        'version' => $request->filled('version') ? strtoupper(trim($request->version)) : null,
        'instal_on' => $request->instal_on,
        'tanggal_digunakan' => $request->tanggal_digunakan,
        'catatan' => $request->catatan,
      ]);

      // Sinkronisasi Checklist Device jika lokasi berubah
      if ($oldLokasiId != $request->id_lokasi) {
        \App\Models\ChecklistDevice::where('maping_id', $maping->id)
          ->where('status_device', 'belum_dicek')
          ->whereHas('checklistRuangan', function ($rq) use ($oldLokasiId) {
            $rq->where('id_lokasi', $oldLokasiId);
          })
          ->each(function ($dev) {
            $ruangan = $dev->checklistRuangan;
            $dev->items()->delete();
            $dev->delete();
            if ($ruangan) {
              $ruangan->updateProgress();
            }
          });
      }

      // Update nama pengguna di checklist device jika penerima diupdate
      $newPenerima = $maping->penerima;
      \App\Models\ChecklistDevice::where('maping_id', $maping->id)
        ->where('status_device', 'belum_dicek')
        ->update(['nama_pengguna' => $newPenerima]);

      DB::commit();

      return redirect()
        ->route('maping.index')
        ->with('success', 'Mapping aset berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Gagal update mapping: ' . $e->getMessage());

      return back()
        ->withInput()
        ->with('error', 'Gagal memperbarui data mapping: ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   * Hanya mapping yang belum memiliki riwayat mutasi, pencabutan, dan servis yang dapat dihapus.
   * Saat dihapus, unit inventaris otomatis dikembalikan menjadi TERSEDIA dan transaksi keluar dibersihkan.
   */
  public function destroy(int $id)
  {
    $user = auth()->user();
    $maping = Maping::withoutGlobalScopes()->with(['keluar.inventaris'])->findOrFail($id);

    // Proteksi multi-company
    if ($user->role !== 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403, 'Anda tidak memiliki hak akses untuk menghapus mapping ini.');
    }

    // Validasi apakah boleh dihapus
    if (!$maping->canBeDeleted()) {
      return back()->with('error', $maping->delete_block_reason ?? 'Mapping tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
    }

    DB::beginTransaction();
    try {
      $keluar = $maping->keluar;
      $inventaris = $keluar?->inventaris;
      $kodeAset = $inventaris?->kode_aset ?? $inventaris?->no_inventaris ?? 'Perangkat';

      // 1. Bersihkan relasi hak akses jika ada
      $maping->mapingAccesses()->delete();
      $maping->historyHakAkses()->delete();

      // 2. Bersihkan foto bukti jika ada
      if ($keluar && $keluar->gambar) {
        if (Storage::disk('public')->exists($keluar->gambar)) {
          Storage::disk('public')->delete($keluar->gambar);
        }
      }

      // 3. Bersihkan checklist device yang belum dicek terkait mapping ini
      \App\Models\ChecklistDevice::where('maping_id', $maping->id)
        ->where('status_device', 'belum_dicek')
        ->each(function ($dev) {
          $ruangan = $dev->checklistRuangan;
          $dev->items()->delete();
          $dev->delete();
          if ($ruangan) {
            $ruangan->updateProgress();
          }
        });

      // 4. Hapus data mapping
      $maping->delete();

      // 4. Hapus data transaksi keluar terkait
      if ($keluar) {
        $keluar->delete();
      }

      // 5. Kembalikan unit inventaris menjadi TERSEDIA di gudang
      if ($inventaris) {
        $inventaris->update([
          'status' => 'TERSEDIA',
          'is_transfer' => false,
        ]);
      }

      DB::commit();

      return redirect()
        ->route('maping.index')
        ->with('success', "Data Mapping berhasil dihapus. Unit aset {$kodeAset} telah dikembalikan ke status TERSEDIA di gudang.");
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Gagal menghapus mapping ID ' . $id . ': ' . $e->getMessage());

      return back()->with('error', 'Gagal menghapus mapping: ' . $e->getMessage());
    }
  }

  /**
   * Mengaktifkan kembali mapping yang berstatus non-aktif (selesai/pencabutan/mutasi yang dibatalkan).
   */
  public function reactivate(int $id)
  {
    $user = auth()->user();
    $maping = Maping::withoutGlobalScopes()->with(['keluar.inventaris'])->findOrFail($id);

    if ($user->role != 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    DB::beginTransaction();
    try {
      $maping->update([
        'status' => 'aktif',
      ]);

      if ($maping->keluar && $maping->keluar->inventaris) {
        $maping->keluar->inventaris->update([
          'is_transfer' => false,
          'status' => 'DIPAKAI',
        ]);
      }

      DB::commit();

      return redirect()
        ->route('maping.index')
        ->with('success', 'Mapping aset dan unit inventaris berhasil diaktifkan kembali.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Error reactivating mapping: ' . $e->getMessage());

      return back()->with('error', 'Gagal mengaktifkan kembali mapping: ' . $e->getMessage());
    }
  }

  public function print(Request $request)
  {
    $user = auth()->user();

    $query = $this->buildMapingQuery($request);

    if (!$request->filled('status')) {
      $query->where('status', '!=', 'selesai');
    }

    $mapings = $query->get()->sortBy(function ($item) {
      return $item->keluar?->inventaris?->kode_aset ?? '';
    }, SORT_NATURAL)->values();

    foreach ($mapings as $maping) {
      $maping->aplikasis = $maping->mapingAccesses->filter(function ($item) {
        return strtoupper($item->kategori) === 'APLIKASI';
      });

      $maping->hakAksesPPN = $maping->mapingAccesses->filter(function ($item) {
        return strtoupper($item->kategori) === 'HAK AKSES' && strtoupper($item->jenis) === 'PPN';
      });

      $maping->hakAksesNonPPN = $maping->mapingAccesses->filter(function ($item) {
        return strtoupper($item->kategori) === 'HAK AKSES' && strtoupper($item->jenis) !== 'PPN';
      });
    }

    $perusahaanId = $request->get('perusahaan_id', $request->get('perusahaan'));
    if (in_array($user->role, ['super_admin', '1', 1]) || !$user->id_perusahaan) {
      $namaPerusahaan = !empty($perusahaanId)
        ? optional(Perusahaan::find($perusahaanId))->nama_perusahaan
        : 'SEMBILAN GROUP';
    } else {
      $namaPerusahaan = $user->perusahaan?->nama_perusahaan ?? 'Perusahaan';
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
      'keluar.inventaris.masuk',
    ]);

    if (Str::isUuid($identifier)) {
      $maping = $query->where('uuid', $identifier)->first();
    } else {
      $maping = $query->where('id', $identifier)->first();
    }

    if (!$maping) {
      $maping = $query->whereHas('keluar.inventaris', function ($q) use ($identifier) {
        $q->where('kode_aset', $identifier)
          ->orWhere('no_inventaris', $identifier);
      })->first();
    }

    if (!$maping) {
      $loan = Peminjaman::with(['karyawan', 'karyawanTujuan', 'perusahaanTujuan', 'inventaris.dataAset.kategori'])
        ->where('id', $identifier)
        ->orWhere('kode_peminjaman', $identifier)
        ->orWhereHas('inventaris', function ($q) use ($identifier) {
          $q->where('kode_aset', $identifier)
            ->orWhere('no_inventaris', $identifier);
        })
        ->first();

      if ($loan) {
        $loanMaping = Maping::withoutGlobalScopes()->with([
          'keluar.inventaris.dataAset.kategori',
          'lokasi',
          'perusahaan',
          'karyawan',
          'divisi',
        ])->whereHas('keluar', function ($q) use ($loan) {
          $q->where('inventaris_id', $loan->inventaris_id);
        })->latest('id')->first();

        if ($loanMaping) {
          $maping = $loanMaping;
          $activeLoan = $loan;
        } else {
          return redirect()->route('peminjaman.show', $loan->id);
        }
      } else {
        abort(404, 'Data aset atau mapping tidak ditemukan.');
      }
    }

    // Ambil peminjaman aktif jika belum di-set dari proses resolusi loan di atas
    $inventarisId = $maping->keluar?->inventaris_id;
    if (!isset($activeLoan) && $inventarisId) {
      $activeLoan = Peminjaman::with(['karyawan', 'karyawanTujuan', 'perusahaanTujuan'])
        ->where('inventaris_id', $inventarisId)
        ->whereIn('status', ['dipinjam', 'Dipinjam', 'menunggu_pengembalian'])
        ->latest('id')
        ->first();
    }

    // 1. Ambil checklist device terakhir yang sudah dicek (atau entri terbaru)
    $deviceQuery = ChecklistDevice::where(function ($q) use ($maping, $inventarisId) {
      $q->where('maping_id', $maping->id);
      if ($inventarisId) {
        $q->orWhere('inventaris_id', $inventarisId);
      }
    });

    $latestChecklist = (clone $deviceQuery)
      ->with(['checklistRuangan.lokasi', 'checkedBy', 'items'])
      ->orderByDesc('checked_at')
      ->orderByDesc('id')
      ->first();

    // 2. Cek apakah ada checklist hari ini untuk device ini
    $today = date('Y-m-d');
    $todayChecklist = (clone $deviceQuery)
      ->where(function ($q) use ($today) {
        $q->whereHas('checklistRuangan', function ($sub) use ($today) {
          $sub->whereDate('tanggal_pemeriksaan', $today)
            ->orWhereDate('tanggal_cek', $today);
        })
        ->orWhereDate('checked_at', $today);
      })
      ->with(['checklistRuangan.lokasi', 'checkedBy', 'items'])
      ->orderByDesc('checked_at')
      ->orderByDesc('id')
      ->first();

    // 3. Ambil 5 riwayat checklist terakhir yang sudah dicek
    $checklistHistory = (clone $deviceQuery)
      ->where('status_device', '!=', 'belum_dicek')
      ->with(['checklistRuangan.lokasi', 'checkedBy', 'items'])
      ->orderByDesc('checked_at')
      ->take(5)
      ->get();

    // 4. Ambil master item checklist aktif untuk perusahaan terkait
    $masterItems = ChecklistItem::where('is_active', true)
      ->where(function ($q) use ($maping) {
        $q->whereNull('id_perusahaan')
          ->orWhere('id_perusahaan', $maping->id_perusahaan);
      })
      ->orderBy('urutan')
      ->get();

    // 5. Cari ruangan pelaksanaan checklist terkait untuk navigasi tombol kembali
    $ruanganId = request('ruangan_id');
    $checklistRuangan = null;
    if ($ruanganId) {
      $checklistRuangan = ChecklistRuangan::withoutGlobalScopes()->with('lokasi')->find($ruanganId);
    }
    if (!$checklistRuangan && $todayChecklist) {
      $checklistRuangan = $todayChecklist->checklistRuangan;
    }
    if (!$checklistRuangan) {
      // Cari sesi checklist ruangan aktif hari ini atau yang sedang berlangsung yang memiliki device ini
      $checklistRuangan = ChecklistRuangan::withoutGlobalScopes()
        ->with('lokasi')
        ->whereHas('checklistDevices', function ($q) use ($maping, $inventarisId) {
          $q->where('maping_id', $maping->id);
          if ($inventarisId) {
            $q->orWhere('inventaris_id', $inventarisId);
          }
        })
        ->where(function ($q) use ($today) {
          $q->whereDate('tanggal_pemeriksaan', $today)
            ->orWhereDate('tanggal_cek', $today)
            ->orWhere('status', '!=', 'selesai');
        })
        ->latest('id')
        ->first();
    }
    if (!$checklistRuangan && $latestChecklist) {
      $checklistRuangan = $latestChecklist->checklistRuangan;
    }

    return view('content.dashboard.maping.public_show', compact(
      'maping',
      'latestChecklist',
      'todayChecklist',
      'checklistHistory',
      'masterItems',
      'activeLoan',
      'checklistRuangan'
    ));
  }

  /**
   * Simpan checklist pemeriksaan langsung dari scan QR/Barcode perangkat di lapangan.
   */
  public function submitChecklistFromScan(Request $request, string $identifier)
  {
    $user = auth()->user();
    if (!$user || !in_array($user->role, ['petugas', 'teknisi', 'super_admin', '1', '2'])) {
      if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
          'success' => false,
          'message' => 'Hanya Petugas IT atau Super Admin yang berwenang mengisi checklist.',
        ], 403);
      }
      return back()->with('error', 'Hanya Petugas IT atau Super Admin yang berwenang mengisi checklist.');
    }

    $query = Maping::withoutGlobalScopes()->with(['keluar.inventaris.dataAset']);
    if (Str::isUuid($identifier)) {
      $maping = $query->where('uuid', $identifier)->first();
    } else {
      $maping = $query->where('id', $identifier)->first();
    }

    if (!$maping) {
      $maping = $query->whereHas('keluar.inventaris', function ($q) use ($identifier) {
        $q->where('kode_aset', $identifier)
          ->orWhere('no_inventaris', $identifier);
      })->first();
    }

    if (!$maping) {
      $loan = Peminjaman::where('id', $identifier)
        ->orWhere('kode_peminjaman', $identifier)
        ->orWhereHas('inventaris', function ($q) use ($identifier) {
          $q->where('kode_aset', $identifier)
            ->orWhere('no_inventaris', $identifier);
        })
        ->first();

      if ($loan) {
        $maping = Maping::withoutGlobalScopes()->with(['keluar.inventaris.dataAset'])
          ->whereHas('keluar', fn($q) => $q->where('inventaris_id', $loan->inventaris_id))
          ->latest('id')
          ->first();
      }
    }

    if (!$maping) {
      if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => false, 'message' => 'Mapping atau aset tidak ditemukan.'], 404);
      }
      abort(404, 'Mapping atau aset tidak ditemukan.');
    }

    $request->validate([
      'status_device' => 'required|in:normal,ada_kendala,belum_dicek',
      'catatan_kendala' => 'nullable|string|max:500',
      'items' => 'nullable|array',
    ]);

    $statusDevice = $request->status_device;
    $catatan = ($statusDevice === 'belum_dicek') ? null : $request->catatan_kendala;
    $checkedAt = ($statusDevice === 'belum_dicek') ? null : Carbon::now('Asia/Jakarta');
    $checkedBy = ($statusDevice === 'belum_dicek') ? null : $user->id;

    DB::beginTransaction();
    try {
      $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
      $dayNum = (int) Carbon::now('Asia/Jakarta')->format('N');
      $mapHari = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'];
      $namaHari = $mapHari[$dayNum] ?? 'senin';
      $inventarisId = $maping->keluar?->inventaris_id;

      // 1. CARI SEMUA ChecklistDevice yang ada untuk perangkat ini di ruangan aktif / hari ini
      $devicesToUpdate = ChecklistDevice::where(function ($q) use ($maping, $inventarisId) {
          $q->where('maping_id', $maping->id);
          if ($inventarisId) {
            $q->orWhere('inventaris_id', $inventarisId);
          }
        })
        ->whereHas('checklistRuangan', function ($q) use ($today, $namaHari) {
          $q->where(function ($sub) use ($today, $namaHari) {
            $sub->whereDate('tanggal_pemeriksaan', $today)
              ->orWhereDate('tanggal_cek', $today)
              ->orWhere('status', '!=', 'selesai')
              ->orWhere('hari', $namaHari);
          });
        })
        ->with('checklistRuangan')
        ->get();

      // Jika tidak ditemukan di filter hari/jadwal di atas, cari di ruangan manapun yang belum selesai
      if ($devicesToUpdate->isEmpty()) {
        $devicesToUpdate = ChecklistDevice::where(function ($q) use ($maping, $inventarisId) {
            $q->where('maping_id', $maping->id);
            if ($inventarisId) {
              $q->orWhere('inventaris_id', $inventarisId);
            }
          })
          ->whereHas('checklistRuangan', function ($q) {
            $q->where('status', '!=', 'selesai');
          })
          ->with('checklistRuangan')
          ->get();
      }

      // Ambil master item checklist aktif untuk sinkronisasi item
      $masterItems = ChecklistItem::where('is_active', true)
        ->where(function ($q) use ($maping) {
          $q->whereNull('id_perusahaan')
            ->orWhere('id_perusahaan', $maping->id_perusahaan);
        })
        ->orderBy('urutan')
        ->get();

      $submittedItems = $request->input('items', []);

      // 2. JIKA PERANGKAT SUDAH ADA DI RUANGAN CHECKLIST:
      if ($devicesToUpdate->isNotEmpty()) {
        foreach ($devicesToUpdate as $checklistDevice) {
          $checklistDevice->update([
            'status_device' => $statusDevice,
            'catatan_kendala' => $catatan,
            'checked_at' => $checkedAt,
            'checked_by' => $checkedBy,
            'nama_pengguna' => $maping->penerima,
            'maping_id' => $maping->id,
            'inventaris_id' => $inventarisId ?: $checklistDevice->inventaris_id,
          ]);

          $this->syncDeviceItems($checklistDevice, $masterItems, $submittedItems, $statusDevice);

          if ($checklistDevice->checklistRuangan) {
            $checklistDevice->checklistRuangan->petugas_id = $user->id;
            $checklistDevice->checklistRuangan->updateProgress();
          }
        }
        $primaryDevice = $devicesToUpdate->first();
      } else {
        // 3. JIKA BELUM TERDAFTAR DI RUANGAN MANAPUN:
        // Cari ruangan yang paling sesuai untuk lokasi ini
        $ruangan = ChecklistRuangan::withoutGlobalScopes()
          ->where('id_lokasi', $maping->id_lokasi)
          ->where(function ($q) use ($today, $namaHari) {
            $q->whereDate('tanggal_pemeriksaan', $today)
              ->orWhereDate('tanggal_cek', $today)
              ->orWhere('status', '!=', 'selesai')
              ->orWhere('hari', $namaHari);
          })
          ->when($maping->id_perusahaan, function ($q) use ($maping) {
            $q->where(function ($sub) use ($maping) {
              $sub->where('id_perusahaan', $maping->id_perusahaan)
                  ->orWhereNull('id_perusahaan');
            });
          })
          ->orderByRaw("CASE 
              WHEN tanggal_pemeriksaan = '{$today}' THEN 1 
              WHEN status = 'sedang_dicek' THEN 2 
              WHEN status = 'belum_dicek' THEN 3 
              ELSE 4 END")
          ->latest('id')
          ->first();

        // Jika ruangan belum ada sama sekali, inisiasi ruangan baru
        if (!$ruangan) {
          $rutin = ChecklistJadwalRutin::where('id_lokasi', $maping->id_lokasi)
            ->where('hari', $namaHari)
            ->where('is_active', true)
            ->when($maping->id_perusahaan, fn($q) => $q->where('id_perusahaan', $maping->id_perusahaan))
            ->first();

          $ruangan = ChecklistRuangan::create([
            'jadwal_rutin_id' => $rutin?->id,
            'tanggal_pemeriksaan' => $today,
            'hari' => $namaHari,
            'id_lokasi' => $maping->id_lokasi,
            'id_perusahaan' => $maping->id_perusahaan ?: $rutin?->id_perusahaan,
            'petugas_id' => $user->id,
            'status' => 'sedang_dicek',
            'kondisi_ruangan' => 'semua_baik',
            'total_device' => 0,
            'total_checked' => 0,
          ]);
        }

        // Buat ChecklistDevice di ruangan ini
        $primaryDevice = ChecklistDevice::create([
          'checklist_ruangan_id' => $ruangan->id,
          'maping_id' => $maping->id,
          'inventaris_id' => $inventarisId,
          'nama_pengguna' => $maping->penerima,
          'status_device' => $statusDevice,
          'catatan_kendala' => $catatan,
          'checked_at' => $checkedAt,
          'checked_by' => $checkedBy,
        ]);

        $this->syncDeviceItems($primaryDevice, $masterItems, $submittedItems, $statusDevice);

        $ruangan->petugas_id = $user->id;
        $ruangan->updateProgress();
      }

      DB::commit();

      $pesan = ($statusDevice === 'normal')
        ? 'Perangkat berhasil ditandai NORMAL (Kondisi Baik).'
        : (($statusDevice === 'ada_kendala')
          ? 'Catatan kendala perangkat berhasil disimpan dan dilaporkan.'
          : 'Pemeriksaan perangkat berhasil dibatalkan (Belum Dicek).');

      if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
          'success' => true,
          'message' => $pesan,
          'status_device' => $primaryDevice->status_device,
          'checked_at' => $primaryDevice->checked_at ? $primaryDevice->checked_at->format('d M Y, H:i') : null,
          'checked_by' => $user->name,
          'catatan_kendala' => $primaryDevice->catatan_kendala,
        ]);
      }

      return back()->with('success', $pesan);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Gagal simpan checklist dari scan: ' . $e->getMessage());
      if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
          'success' => false,
          'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
        ], 500);
      }
      return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
  }

  /**
   * Helper sinkronisasi ChecklistDeviceItem
   */
  private function syncDeviceItems(ChecklistDevice $device, $masterItems, array $submittedItems, string $statusDevice): void
  {
    $existingItems = $device->items()->get();

    if ($existingItems->isEmpty()) {
      foreach ($masterItems as $mItem) {
        $isOk = ($statusDevice === 'normal')
          ? true
          : (!empty($submittedItems[$mItem->id]) || !empty($submittedItems[$mItem->nama_item]));
        if ($statusDevice === 'belum_dicek') $isOk = false;

        ChecklistDeviceItem::create([
          'checklist_device_id' => $device->id,
          'nama_item' => $mItem->nama_item,
          'kategori_item' => $mItem->kategori,
          'is_ok' => $isOk,
        ]);
      }
    } else {
      foreach ($existingItems as $eItem) {
        if ($statusDevice === 'normal') {
          $eItem->update(['is_ok' => true]);
        } elseif ($statusDevice === 'belum_dicek') {
          $eItem->update(['is_ok' => false]);
        } else {
          $isOk = !empty($submittedItems[$eItem->id]) || !empty($submittedItems[$eItem->nama_item]);
          $eItem->update(['is_ok' => $isOk]);
        }
      }
    }
  }

  public function exportExcel(Request $request)
  {
    $user = auth()->user();

    $query = $this->buildMapingQuery($request);

    if (!$request->filled('status')) {
      $query->where('status', '!=', 'selesai');
    }

    $mapings = $query->get()->sortBy(function ($item) {
      return $item->keluar?->inventaris?->kode_aset ?? '';
    }, SORT_NATURAL)->values();

    foreach ($mapings as $maping) {
      $maping->aplikasis = $maping->mapingAccesses->filter(function ($item) {
        return strtoupper($item->kategori) === 'APLIKASI';
      });

      $maping->hakAksesPPN = $maping->mapingAccesses->filter(function ($item) {
        return strtoupper($item->kategori) === 'HAK AKSES' && strtoupper($item->jenis) === 'PPN';
      });

      $maping->hakAksesNonPPN = $maping->mapingAccesses->filter(function ($item) {
        return strtoupper($item->kategori) === 'HAK AKSES' && strtoupper($item->jenis) !== 'PPN';
      });
    }

    $perusahaanId = $request->get('perusahaan_id', $request->get('perusahaan'));
    $namaPerusahaan = (in_array($user->role, ['super_admin', '1', 1]) || !$user->id_perusahaan)
      ? (!empty($perusahaanId) ? optional(Perusahaan::find($perusahaanId))->nama_perusahaan : 'SEMBILAN GROUP')
      : ($user->perusahaan?->nama_perusahaan ?? 'Perusahaan');

    return Excel::download(new MapingExport($mapings, $namaPerusahaan), 'Laporan Mapping.xlsx');
  }
}

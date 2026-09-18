<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Perusahaan;
use App\Models\Kategori;
use App\Models\DataAset;
use App\Models\HistoryMutasi;
use App\Models\HistoryPencabutan;
use App\Models\Maintenance;
use App\Models\HistoryHakAkses;
use App\Models\Masuk;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoryPerjalananAsetController extends Controller
{
  /**
   * Tampilan Utama: Rekap Stok & Status Aset per Data Aset (Mirip Cek Stok)
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Inventaris::with([
      'dataAset.kategori',
      'perusahaan',
      'keluarTerakhir.karyawan',
      'keluarTerakhir.lokasi',
      'keluarTerakhir.maping.karyawan',
      'peminjamanTerakhir.karyawan',
      'peminjamanTerakhir.karyawanTujuan',
    ]);

    // FILTER PERUSAHAAN
    if ($user->role != 'super_admin') {
      $accessibleIds = $this->getAccessibleCompanyIds($user);
      if ($accessibleIds) {
        $query->whereIn('perusahaan_id', $accessibleIds);
      }
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    // FILTER KATEGORI
    if ($request->filled('kategori_id')) {
      $isPerusahaanFiltered = ($user->role != 'super_admin') || $request->filled('perusahaan_id');
      if ($isPerusahaanFiltered) {
        $query->whereHas('dataAset', function ($q) use ($request) {
          $q->where('kategori_id', $request->kategori_id);
        });
      } else {
        // Super admin tanpa filter perusahaan (Semua Perusahaan):
        // Filter berdasarkan nama_barang agar kategori dengan nama sama di seluruh perusahaan tercakup
        $kategori = Kategori::find($request->kategori_id);
        if ($kategori) {
          $query->whereHas('dataAset.kategori', function ($q) use ($kategori) {
            $q->where('nama_barang', $kategori->nama_barang);
          });
        }
      }
    }

    // SEARCH (Kode Aset, No Inventaris, Kategori, Merek, Type, dan Nama Pemakai / Divisi)
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
          })
          // Pemakai via Transaksi Keluar (Karyawan / Divisi)
          ->orWhereHas('keluars.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%")
              ->orWhere('kode_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('keluars', function ($qq) use ($search) {
            $qq->where('divisi_klr', 'like', "%{$search}%");
          })
          // Pemakai via Mapping
          ->orWhereHas('keluars.maping.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('keluars.maping', function ($qq) use ($search) {
            $qq->where('divisi', 'like', "%{$search}%");
          })
          // Pemakai via Peminjaman
          ->orWhereHas('peminjamans.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('peminjamans.karyawanTujuan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          });
      });
    }

    $inventarisList = $query->latest()
      ->paginate(15)
      ->appends($request->query());

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    // Kategori untuk dropdown filter:
    // Jika super admin telah memilih perusahaan, tampilkan kategori milik perusahaan tsb.
    // Jika super admin belum memilih perusahaan (Semua Perusahaan), tampilkan kategori unik berdasarkan nama_barang agar tidak dobel.
    if ($user->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $kategoris = Kategori::where('perusahaan_id', $request->perusahaan_id)
          ->orderBy('nama_barang')
          ->get();
      } else {
        $kategoris = Kategori::select('nama_barang', DB::raw('MIN(id) as id'))
          ->groupBy('nama_barang')
          ->orderBy('nama_barang')
          ->get();
      }
    } else {
      $kategoris = Kategori::where('perusahaan_id', $user->id_perusahaan)
        ->orderBy('nama_barang')
        ->get();
    }

    return view('content.dashboard.history.perjalanan-aset', compact(
      'inventarisList',
      'perusahaans',
      'kategoris',
      'user'
    ));
  }

  /**
   * Detail Riwayat Perjalanan Timeline Aset (Masuk -> Keluar -> Mutasi -> Maintenance -> Hak Akses -> Pencabutan)
   */
  public function show(Request $request, $id)
  {
    $user = auth()->user();
    $inventaris = $this->resolveInventaris($id, $user);

    if ($inventaris->isEmpty()) {
      abort(404, 'Data aset tidak ditemukan.');
    }

    $timeline = $this->buildTimeline($inventaris, $request, true);

    $kodeAsets = $timeline->pluck('kode_aset')
      ->merge($timeline->pluck('kode_aset_lama'))
      ->merge($timeline->pluck('kode_aset_baru'))
      ->merge($inventaris->pluck('kode_aset'))
      ->filter()
      ->unique()
      ->sort()
      ->values();

    // Hitung ringkasan statistik
    $totalKeluar = $timeline->where('aktivitas', 'KELUAR')->count();
    $totalMutasi = $timeline->where('aktivitas', 'MUTASI')->count();
    $totalCabut = $timeline->where('aktivitas', 'PENCABUTAN')->count();
    $totalMaintenance = $timeline->where('aktivitas', 'MAINTENANCE')->count();
    $totalHakAkses = $timeline->where('aktivitas', 'HAK AKSES')->count();

    return view('content.dashboard.history.show-perjalanan-aset', compact(
      'inventaris',
      'timeline',
      'kodeAsets',
      'totalKeluar',
      'totalMutasi',
      'totalCabut',
      'totalMaintenance',
      'totalHakAkses',
      'user',
      'id'
    ));
  }

  /**
   * Cetak PDF Laporan Riwayat Perjalanan Timeline
   */
  public function cetak(Request $request, $id)
  {
    $user = auth()->user();
    $inventaris = $this->resolveInventaris($id, $user);

    if ($inventaris->isEmpty()) {
      abort(404, 'Data aset tidak ditemukan.');
    }

    $timeline = $this->buildTimeline($inventaris, $request);

    return view('content.dashboard.history.cetak-history-perjalanan', compact('inventaris', 'timeline', 'user'));
  }

  public function exportExcel(Request $request, $id)
  {
    $user = auth()->user();
    $inventaris = $this->resolveInventaris($id, $user);

    if ($inventaris->isEmpty()) {
      abort(404, 'Data aset tidak ditemukan.');
    }

    $timeline = $this->buildTimeline($inventaris, $request);

    $kodeAset = $inventaris->first()?->kode_aset ?? 'Unit';
    $filename = 'Riwayat_Perjalanan_' . str_replace('/', '-', $kodeAset) . '.xlsx';

    return \Maatwebsite\Excel\Facades\Excel::download(
      new \App\Exports\HistoryPerjalananExport($inventaris, $timeline, $user),
      $filename
    );
  }

  /**
   * Cetak PDF Laporan Daftar Tracking Device / History Perjalanan Aset (Index List)
   */
  public function cetakIndex(Request $request)
  {
    $user = auth()->user();

    $query = Inventaris::with([
      'dataAset.kategori',
      'perusahaan',
      'keluarTerakhir.karyawan',
      'keluarTerakhir.lokasi',
      'keluarTerakhir.maping.karyawan',
      'peminjamanTerakhir.karyawan',
      'peminjamanTerakhir.karyawanTujuan',
    ]);

    // FILTER PERUSAHAAN
    if ($user->role != 'super_admin') {
      $accessibleIds = $this->getAccessibleCompanyIds($user);
      if ($accessibleIds) {
        $query->whereIn('perusahaan_id', $accessibleIds);
      }
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    // FILTER KATEGORI
    if ($request->filled('kategori_id')) {
      $isPerusahaanFiltered = ($user->role != 'super_admin') || $request->filled('perusahaan_id');
      if ($isPerusahaanFiltered) {
        $query->whereHas('dataAset', function ($q) use ($request) {
          $q->where('kategori_id', $request->kategori_id);
        });
      } else {
        $kategori = Kategori::find($request->kategori_id);
        if ($kategori) {
          $query->whereHas('dataAset.kategori', function ($q) use ($kategori) {
            $q->where('nama_barang', $kategori->nama_barang);
          });
        }
      }
    }

    // SEARCH (Kode Aset, No Inventaris, Kategori, Merek, Type, dan Nama Pemakai / Divisi)
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
          })
          // Pemakai via Transaksi Keluar (Karyawan / Divisi)
          ->orWhereHas('keluars.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%")
              ->orWhere('kode_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('keluars', function ($qq) use ($search) {
            $qq->where('divisi_klr', 'like', "%{$search}%");
          })
          // Pemakai via Mapping
          ->orWhereHas('keluars.maping.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('keluars.maping', function ($qq) use ($search) {
            $qq->where('divisi', 'like', "%{$search}%");
          })
          // Pemakai via Peminjaman
          ->orWhereHas('peminjamans.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('peminjamans.karyawanTujuan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          });
      });
    }

    $inventarisList = $query->latest()->get();

    // Nama Perusahaan untuk Header Laporan
    $namaPerusahaan = 'SEMBILAN GROUP';
    if ($user->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $namaPerusahaan = Perusahaan::find($request->perusahaan_id)?->nama_perusahaan ?? 'SEMBILAN GROUP';
      }
    } else {
      $namaPerusahaan = Perusahaan::find($user->id_perusahaan)?->nama_perusahaan ?? 'SEMBILAN GROUP';
    }

    // Nama Kategori Terpilih
    $namaKategori = 'Semua Kategori';
    if ($request->filled('kategori_id')) {
      $kategori = Kategori::find($request->kategori_id);
      if ($kategori) {
        $namaKategori = $kategori->nama_barang;
      }
    }

    return view('content.dashboard.history.cetak-perjalanan-index', compact(
      'inventarisList',
      'user',
      'namaPerusahaan',
      'namaKategori'
    ));
  }

  public function exportExcelIndex(Request $request)
  {
    $user = auth()->user();

    $query = Inventaris::with([
      'dataAset.kategori',
      'perusahaan',
      'keluarTerakhir.karyawan',
      'keluarTerakhir.lokasi',
      'keluarTerakhir.maping.karyawan',
      'peminjamanTerakhir.karyawan',
      'peminjamanTerakhir.karyawanTujuan',
    ]);

    if ($user->role != 'super_admin') {
      $accessibleIds = $this->getAccessibleCompanyIds($user);
      if ($accessibleIds) {
        $query->whereIn('perusahaan_id', $accessibleIds);
      }
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    // FILTER KATEGORI
    if ($request->filled('kategori_id')) {
      $isPerusahaanFiltered = ($user->role != 'super_admin') || $request->filled('perusahaan_id');
      if ($isPerusahaanFiltered) {
        $query->whereHas('dataAset', function ($q) use ($request) {
          $q->where('kategori_id', $request->kategori_id);
        });
      } else {
        $kategori = Kategori::find($request->kategori_id);
        if ($kategori) {
          $query->whereHas('dataAset.kategori', function ($q) use ($kategori) {
            $q->where('nama_barang', $kategori->nama_barang);
          });
        }
      }
    }

    // SEARCH (Kode Aset, No Inventaris, Kategori, Merek, Type, dan Nama Pemakai / Divisi)
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
          })
          // Pemakai via Transaksi Keluar (Karyawan / Divisi)
          ->orWhereHas('keluars.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%")
              ->orWhere('kode_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('keluars', function ($qq) use ($search) {
            $qq->where('divisi_klr', 'like', "%{$search}%");
          })
          // Pemakai via Mapping
          ->orWhereHas('keluars.maping.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('keluars.maping', function ($qq) use ($search) {
            $qq->where('divisi', 'like', "%{$search}%");
          })
          // Pemakai via Peminjaman
          ->orWhereHas('peminjamans.karyawan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          })
          ->orWhereHas('peminjamans.karyawanTujuan', function ($qq) use ($search) {
            $qq->where('nama_karyawan', 'like', "%{$search}%");
          });
      });
    }

    $inventarisList = $query->latest()->get();

    return \Maatwebsite\Excel\Facades\Excel::download(
      new \App\Exports\HistoryPerjalananIndexExport($inventarisList, $user),
      'Daftar_Perjalanan_Aset.xlsx'
    );
  }

  /**
   * Resolve inventaris record by either inventaris ID or kode_aset, ensuring only ONE device is targeted.
   */
  private function resolveInventaris($id, $user)
  {
    $baseQuery = Inventaris::withoutGlobalScopes()->with([
      'dataAset.kategori',
      'perusahaan',
      'masuk' => function ($q) {
        $q->withoutGlobalScopes()->with('supplier', 'perusahaanAsal');
      },
      'keluars' => function ($q) {
        $q->withoutGlobalScopes()->with(['karyawan', 'lokasi', 'user']);
      },
    ]);

    $accessibleIds = $this->getAccessibleCompanyIds($user);

    // 1. Cek Inventaris berdasarkan ID langsung
    $target = null;
    if (is_numeric($id)) {
      $target = (clone $baseQuery)->where('id', $id)->first();
    }

    // 2. Jika tidak ditemukan dengan ID numerik, coba cari berdasarkan kode_aset
    if (!$target) {
      $byKode = (clone $baseQuery)->where('kode_aset', $id);
      if ($accessibleIds) {
        $byKode->whereIn('perusahaan_id', $accessibleIds);
      }
      $target = $byKode->first();
    }

    if (!$target) {
      return new Collection();
    }

    // Validasi izin akses jika bukan super_admin
    if ($accessibleIds && !in_array($target->perusahaan_id, $accessibleIds)) {
      // Boleh diakses jika aset ini pernah dimutasi dari/ke perusahaan user
      $hasAccessViaMutasi = HistoryMutasi::where('inventaris_id', $target->id)
        ->where(function ($mq) use ($accessibleIds) {
          $mq->whereIn('id_perusahaan_asal', $accessibleIds)
            ->orWhereIn('id_perusahaan_tujuan', $accessibleIds);
        })->exists();

      if (!$hasAccessViaMutasi) {
        // Cek jika aset ini hasil mutasi masuk dari perusahaan user
        $hasAccessViaMasuk = false;
        if ($target->masuk && in_array($target->masuk->perusahaan_asal, $accessibleIds)) {
          $hasAccessViaMasuk = true;
        }

        if (!$hasAccessViaMasuk && $target->masuk?->history_mutasi_id) {
          $hasAccessViaMasuk = HistoryMutasi::where('id', $target->masuk->history_mutasi_id)
            ->where(function ($mq) use ($accessibleIds) {
              $mq->whereIn('id_perusahaan_asal', $accessibleIds)
                ->orWhereIn('id_perusahaan_tujuan', $accessibleIds);
            })->exists();
        }

        if (!$hasAccessViaMasuk) {
          return new Collection();
        }
      }
    }

    return new Collection([$target]);
  }

  /**
   * Get accessible company IDs for the current user.
   */
  private function getAccessibleCompanyIds($user)
  {
    if ($user->role === 'super_admin') {
      return null;
    }

    if (!empty($user->id_perusahaan)) {
      return [$user->id_perusahaan];
    }

    if (!empty($user->perusahaan_id)) {
      return [$user->perusahaan_id];
    }

    return null;
  }

  /**
   * Menggabungkan Seluruh Riwayat Aktivitas untuk unit device ini: MASUK -> KELUAR -> MUTASI -> MAINTENANCE -> PENCABUTAN -> HAK AKSES
   */
  private function buildTimeline($inventaris, ?Request $request = null, bool $descending = false)
  {
    $timeline = collect();

    $target = $inventaris->first();
    if (!$target) {
      return $timeline;
    }

    // Kumpulkan seluruh Inventaris record yang merepresentasikan unit fisik yang sama
    // (jika terjadi mutasi antar-perusahaan yang membuat record inventaris baru di perusahaan tujuan)
    $allInventaris = collect([$target]);
    $knownInvIds = collect([$target->id]);
    $mutasiIds = collect();

    // 1. Trace ke belakang (asal unit sebelum mutasi antar-perusahaan)
    $curr = $target;
    while ($curr && $curr->masuk && $curr->masuk->history_mutasi_id) {
      $parentMutasi = HistoryMutasi::with('creator')->find($curr->masuk->history_mutasi_id);
      if ($parentMutasi && $parentMutasi->inventaris_id) {
        $mutasiIds->push($parentMutasi->id);
        if (!$knownInvIds->contains($parentMutasi->inventaris_id)) {
          $knownInvIds->push($parentMutasi->inventaris_id);
          $parentInv = Inventaris::withoutGlobalScopes()->with([
            'dataAset.kategori',
            'perusahaan',
            'masuk' => function ($q) {
              $q->withoutGlobalScopes()->with('supplier', 'perusahaanAsal');
            },
            'keluars' => function ($q) {
              $q->withoutGlobalScopes()->with(['karyawan', 'lokasi', 'user']);
            },
          ])->find($parentMutasi->inventaris_id);

          if ($parentInv) {
            $allInventaris->push($parentInv);
            $curr = $parentInv;
            continue;
          }
        }
      }
      break;
    }

    // 2. Trace ke depan (tujuan unit setelah mutasi antar-perusahaan)
    $queue = collect([$target->id]);
    while ($queue->isNotEmpty()) {
      $checkId = $queue->shift();
      $childMutasis = HistoryMutasi::with('creator')
        ->where('inventaris_id', $checkId)
        ->where('jenis_mutasi', 'antar_perusahaan')
        ->get();

      foreach ($childMutasis as $cm) {
        $mutasiIds->push($cm->id);
        // Cari Masuk yang terbentuk dari mutasi ini
        $masuks = Masuk::withoutGlobalScopes()->where('history_mutasi_id', $cm->id)->get();
        foreach ($masuks as $m) {
          $childInvs = Inventaris::withoutGlobalScopes()->with([
            'dataAset.kategori',
            'perusahaan',
            'masuk' => function ($q) {
              $q->withoutGlobalScopes()->with('supplier', 'perusahaanAsal');
            },
            'keluars' => function ($q) {
              $q->withoutGlobalScopes()->with(['karyawan', 'lokasi', 'user']);
            },
          ])
          ->where('masuk_id', $m->id)
          ->get();

          foreach ($childInvs as $ci) {
            if (!$knownInvIds->contains($ci->id)) {
              $knownInvIds->push($ci->id);
              $allInventaris->push($ci);
              $queue->push($ci->id);
            }
          }
        }
      }
    }

    // Mutasi internal unit ini + mutasi antar-perusahaan unit ini
    $internalMutasis = HistoryMutasi::with('creator')
      ->whereIn('inventaris_id', $knownInvIds)
      ->where('jenis_mutasi', 'internal')
      ->get();

    $antarMutasis = HistoryMutasi::with('creator')
      ->whereIn('id', $mutasiIds->unique())
      ->get();

    $mutasiTerkait = $internalMutasis->concat($antarMutasis)->unique('id');
    $allInvIds = $knownInvIds->unique()->toArray();

    // 1. ASET MASUK (PROCUREMENT / MUTASI MASUK)
    foreach ($allInventaris as $item) {
      if ($item->masuk) {
        $isMutasiMasuk = $item->masuk->jenis_masuk === 'Mutasi' || !empty($item->masuk->history_mutasi_id);
        $keteranganMasuk = $isMutasiMasuk
          ? 'Barang diterima hasil mutasi dari perusahaan: ' . ($item->masuk->perusahaanAsal?->nama_perusahaan ?? 'Perusahaan Asal')
          : 'Barang diterima dari supplier: ' . ($item->masuk->supplier?->nama_supplier ?? 'Vendor');

        $timeline->push([
          'tanggal' => Carbon::parse($item->masuk->tanggal_pembelian ?? $item->created_at),
          'aktivitas' => 'MASUK',
          'kode_aset' => $item->kode_aset,
          'inventaris' => $item->no_inventaris,
          'user_lama' => null,
          'user_baru' => 'Stok Gudang',
          'lokasi_lama' => null,
          'lokasi_baru' => 'Gudang / Stock',
          'keterangan' => $keteranganMasuk,
          'petugas' => 'Sistem',
        ]);
      }
    }

    // 2. TRANSAKSI KELUAR (PEMAKAIAN)
    foreach ($allInventaris as $item) {
      if ($item->keluars) {
        foreach ($item->keluars->sortBy('tgl_keluar') as $index => $keluar) {
          $penerima = $keluar->jenis_penerima == 'Perorangan' 
            ? ($keluar->karyawan?->nama_karyawan ?? '-') 
            : ($keluar->divisi_klr ?? '-');

          $timeline->push([
            'tanggal' => Carbon::parse($keluar->tgl_keluar),
            'aktivitas' => 'KELUAR',
            'kode_aset' => $item->kode_aset,
            'inventaris' => $item->no_inventaris,
            'user_lama' => null,
            'user_baru' => $penerima,
            'lokasi_lama' => null,
            'lokasi_baru' => $keluar->lokasi?->nama_lokasi ?? '-',
            'keterangan' => $index == 0 ? 'Aset pertama kali digunakan' : 'Aset digunakan kembali setelah pencabutan / mutasi',
            'petugas' => $keluar->user?->name ?? 'Petugas',
          ]);
        }
      }
    }

    // 3. HISTORY MUTASI
    foreach ($mutasiTerkait as $item) {
      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal_mutasi),
        'aktivitas' => 'MUTASI',
        'kode_aset' => $item->kode_aset_baru ?? $item->kode_aset_lama,
        'kode_aset_lama' => $item->kode_aset_lama,
        'kode_aset_baru' => $item->kode_aset_baru,
        'inventaris' => $item->no_inventaris_baru ?? $item->no_inventaris_lama,
        'user_lama' => $item->user_lama,
        'user_baru' => $item->user_baru,
        'lokasi_lama' => $item->lokasi_lama,
        'lokasi_baru' => $item->lokasi_baru,
        'keterangan' => ($item->jenis_mutasi == 'internal' ? 'Mutasi Internal' : 'Mutasi Antar Perusahaan') . ($item->catatan ? ' (' . $item->catatan . ')' : ''),
        'petugas' => $item->creator?->name ?? 'Petugas',
      ]);
    }

    // 4. MAINTENANCE / SERVIS
    $maintenances = Maintenance::withoutGlobalScopes()
      ->with('creator')
      ->whereIn('inventaris_id', $allInvIds)
      ->orderBy('created_at')
      ->get();

    foreach ($maintenances as $maint) {
      $inv = $allInventaris->firstWhere('id', $maint->inventaris_id);
      $timeline->push([
        'tanggal' => Carbon::parse($maint->created_at),
        'aktivitas' => 'MAINTENANCE',
        'kode_aset' => $inv?->kode_aset ?? $target->kode_aset,
        'inventaris' => $inv?->no_inventaris ?? $target->no_inventaris,
        'user_lama' => null,
        'user_baru' => null,
        'lokasi_lama' => null,
        'lokasi_baru' => 'Tempat Servis / Vendor',
        'keterangan' => 'Status: ' . $maint->status . ' - Keluhan: ' . ($maint->keluhan ?? $maint->deskripsi_kerusakan ?? 'Servis unit'),
        'petugas' => $maint->creator?->name ?? $maint->user?->name ?? 'Petugas Servis',
        'gambar' => $maint->gambar,
        'maintenance_id' => $maint->id,
      ]);
    }

    // 5. HISTORY PENCABUTAN
    $historyCabut = HistoryPencabutan::withoutGlobalScopes()
      ->with([
        'creator',
        'maping.karyawan',
        'maping.keluar.karyawan',
        'inventaris.keluarTerakhir.karyawan',
      ])
      ->whereIn('inventaris_id', $allInvIds)
      ->orderBy('tanggal_pencabutan')
      ->get();

    foreach ($historyCabut as $item) {
      $inv = $allInventaris->firstWhere('id', $item->inventaris_id);

      // Cari nama user yang memegang aset saat pencabutan
      $userPencabutan = $item->user_lama;
      if (empty($userPencabutan) || $userPencabutan === '-') {
        $userPencabutan = $item->maping?->penerima
          ?? $item->maping?->karyawan?->nama_karyawan
          ?? $item->maping?->keluar?->karyawan?->nama_karyawan
          ?? $item->maping?->divisi
          ?? $item->maping?->keluar?->divisi_klr
          ?? $item->inventaris?->keluarTerakhir?->karyawan?->nama_karyawan
          ?? $item->inventaris?->keluarTerakhir?->divisi_klr;
      }

      $lokasiLama = $item->lokasi_lama;
      if (empty($lokasiLama) || $lokasiLama === '-') {
        $lokasiLama = $item->maping?->lokasi?->nama_lokasi
          ?? $item->maping?->keluar?->lokasi?->nama_lokasi;
      }

      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal_pencabutan),
        'aktivitas' => 'PENCABUTAN',
        'kode_aset' => $inv?->kode_aset ?? $item->kode_aset ?? $target->kode_aset,
        'inventaris' => $inv?->no_inventaris ?? $item->no_inventaris ?? $target->no_inventaris,
        'user_lama' => $userPencabutan,
        'user_baru' => $userPencabutan,
        'lokasi_lama' => $lokasiLama,
        'lokasi_baru' => $item->lokasi_baru ?? 'Gudang',
        'keterangan' => 'Pencabutan: ' . ($item->alasan ?? 'Penarikan unit dari pengguna'),
        'petugas' => $item->creator?->name ?? 'Petugas',
      ]);
    }

    // 6. HISTORY HAK AKSES
    $historyHakAkses = HistoryHakAkses::with([
        'access',
        'user',
        'maping' => function ($q) {
          $q->withoutGlobalScopes()->with(['keluar' => function ($kq) {
            $kq->withoutGlobalScopes();
          }, 'lokasi']);
        },
      ])
      ->whereHas('maping', function ($mq) use ($allInvIds) {
        $mq->withoutGlobalScopes()->whereHas('keluar', function ($kq) use ($allInvIds) {
          $kq->withoutGlobalScopes()->whereIn('inventaris_id', $allInvIds);
        });
      })
      ->orderBy('created_at')
      ->get();

    foreach ($historyHakAkses as $hakAkses) {
      $inv = $allInventaris->firstWhere('id', $hakAkses->maping?->keluar?->inventaris_id);

      $namaAkses = $hakAkses->nama_akses ?? $hakAkses->access?->nama_akses ?? '-';
      $kategori  = $hakAkses->kategori ?? $hakAkses->access?->kategori ?? 'Hak Akses';
      $jenis     = $hakAkses->jenis ?? $hakAkses->access?->jenis ?? 'NON PPN';
      $email     = $hakAkses->email;

      $aksi = strtolower($hakAkses->aksi ?? 'tambah');
      if ($aksi === 'tambah') {
        $aksiText = 'Menambahkan';
      } elseif ($aksi === 'update') {
        $aksiText = 'Memperbarui';
      } elseif ($aksi === 'hapus') {
        $aksiText = 'Menghapus';
      } else {
        $aksiText = ucfirst($aksi);
      }

      $jenisLabel = ($kategori === 'Aplikasi') ? 'Aplikasi' : "Hak Akses {$jenis}";
      $emailText  = !empty($email) ? ", {$email}" : '';

      $keteranganFormat = "{$aksiText} {$jenisLabel} ({$namaAkses}){$emailText}";

      $timeline->push([
        'tanggal' => Carbon::parse($hakAkses->created_at),
        'aktivitas' => 'HAK AKSES',
        'kode_aset' => $inv?->kode_aset ?? $target->kode_aset,
        'inventaris' => $inv?->no_inventaris ?? $target->no_inventaris,
        'user_lama' => null,
        'user_baru' => $hakAkses->maping?->penerima ?? '-',
        'lokasi_lama' => null,
        'lokasi_baru' => $hakAkses->maping?->lokasi?->nama_lokasi ?? '-',
        'hak_akses' => $namaAkses . ($email ? " ({$email})" : ''),
        'keterangan' => $keteranganFormat,
        'petugas' => $hakAkses->user?->name ?? 'Petugas',
      ]);
    }

    // FILTER TANGGAL
    if ($request) {
      if ($request->filled('tanggal_awal')) {
        $timeline = $timeline->filter(function ($item) use ($request) {
          return $item['tanggal']->gte(Carbon::parse($request->tanggal_awal)->startOfDay());
        });
      }

      if ($request->filled('tanggal_akhir')) {
        $timeline = $timeline->filter(function ($item) use ($request) {
          return $item['tanggal']->lte(Carbon::parse($request->tanggal_akhir)->endOfDay());
        });
      }

      // FILTER KODE ASET
      if ($request->filled('kode_aset')) {
        $kode = $request->kode_aset;
        $timeline = $timeline->filter(function ($item) use ($kode) {
          return ($item['kode_aset'] ?? null) == $kode ||
            ($item['kode_aset_lama'] ?? null) == $kode ||
            ($item['kode_aset_baru'] ?? null) == $kode;
        });
      }

      // FILTER AKTIVITAS
      if ($request->filled('aktivitas')) {
        $aktivitasVal = strtoupper($request->aktivitas);
        $timeline = $timeline->filter(function ($item) use ($aktivitasVal) {
          return strtoupper($item['aktivitas']) === $aktivitasVal;
        });
      }
    }

    if ($descending) {
      return $timeline->sortByDesc('tanggal')->values();
    }

    return $timeline->sortBy('tanggal')->values();
  }
}

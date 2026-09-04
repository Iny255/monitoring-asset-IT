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
use Carbon\Carbon;

class HistoryPerjalananAsetController extends Controller
{
  /**
   * Tampilan Utama: Rekap Stok & Status Aset per Data Aset (Mirip Cek Stok)
   */
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Inventaris::with(['dataAset.kategori', 'perusahaan', 'keluarTerakhir.karyawan', 'keluarTerakhir.lokasi']);

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

    $inventarisList = $query->latest()
      ->paginate(15)
      ->appends($request->query());

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    $selectedPerusahaanId = $user->role == 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;
    $kategoris = Kategori::query()
      ->when($selectedPerusahaanId, fn($q) => $q->where('perusahaan_id', $selectedPerusahaanId))
      ->orderBy('nama_barang')
      ->get();

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

    $kodeAsets = $inventaris->pluck('kode_aset')->unique()->sort()->values();

    $timeline = $this->buildTimeline($inventaris, $request, true);

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

  public function exportExcelIndex(Request $request)
  {
    $user = auth()->user();

    $query = Inventaris::with(['dataAset.kategori', 'perusahaan', 'keluarTerakhir.karyawan', 'keluarTerakhir.lokasi']);

    if ($user->role != 'super_admin') {
      $accessibleIds = $this->getAccessibleCompanyIds($user);
      if ($accessibleIds) {
        $query->whereIn('perusahaan_id', $accessibleIds);
      }
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    if ($request->filled('kategori_id')) {
      $query->whereHas('dataAset', function ($q) use ($request) {
        $q->where('kategori_id', $request->kategori_id);
      });
    }

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

    $inventarisList = $query->latest()->get();

    return \Maatwebsite\Excel\Facades\Excel::download(
      new \App\Exports\HistoryPerjalananIndexExport($inventarisList, $user),
      'Daftar_Perjalanan_Aset.xlsx'
    );
  }

  /**
   * Resolve inventaris record by either inventaris ID or data_aset ID, handling multi-company and transfer permissions.
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

    // 1. Cek Inventaris berdasarkan ID langsung milik perusahaan user (atau yang pernah dimutasi dari/ke perusahaan user)
    $invQuery = (clone $baseQuery)->where('id', $id);
    if ($accessibleIds) {
      $invQuery->where(function ($q) use ($accessibleIds, $id) {
        $q->whereIn('perusahaan_id', $accessibleIds)
          ->orWhereHas('masuk', function ($mq) use ($accessibleIds) {
            $mq->withoutGlobalScopes()->whereIn('perusahaan_asal', $accessibleIds);
          })
          ->orWhereIn('id', function ($sub) use ($accessibleIds) {
            $sub->select('inventaris_id')
              ->from('history_mutasis')
              ->whereIn('id_perusahaan_asal', $accessibleIds)
              ->orWhereIn('id_perusahaan_tujuan', $accessibleIds);
          });
      });
    }
    $inventaris = $invQuery->orderBy('kode_aset')->get();

    // 2. Jika belum ditemukan, coba cari berdasarkan data_aset_id
    if ($inventaris->isEmpty()) {
      $dataAsetQuery = (clone $baseQuery)->where('data_aset_id', $id);
      if ($accessibleIds) {
        $dataAsetQuery->whereIn('perusahaan_id', $accessibleIds);
      }
      $inventaris = $dataAsetQuery->orderBy('kode_aset')->get();
    }

    // 3. Fallback: jika super admin atau aset mutasi antar perusahaan yang tercatat di history mutasi
    if ($inventaris->isEmpty()) {
      if ($user->role === 'super_admin') {
        $inventaris = (clone $baseQuery)->where('id', $id)->orWhere('data_aset_id', $id)->orderBy('kode_aset')->get();
      } elseif ($accessibleIds) {
        $hasMutasi = HistoryMutasi::where('inventaris_id', $id)
          ->where(function ($mq) use ($accessibleIds) {
            $mq->whereIn('id_perusahaan_asal', $accessibleIds)
              ->orWhereIn('id_perusahaan_tujuan', $accessibleIds);
          })->exists();

        if ($hasMutasi) {
          $inventaris = (clone $baseQuery)->where('id', $id)->orderBy('kode_aset')->get();
        }
      }
    }

    return $inventaris;
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
   * Menggabungkan Seluruh Riwayat Aktivitas: MASUK -> KELUAR -> MUTASI -> MAINTENANCE -> PENCABUTAN -> HAK AKSES
   */
  private function buildTimeline(Collection $inventaris, ?Request $request = null, bool $descending = false)
  {
    $timeline = collect();

    // Dapatkan semua ID inventaris terkait (termasuk sebelum/sesudah mutasi antar perusahaan)
    $invIds = $inventaris->pluck('id')->filter()->unique()->toArray();
    $mutasiTerkait = HistoryMutasi::with('creator')
      ->where(function ($q) use ($inventaris, $invIds) {
        $q->whereIn('inventaris_id', $invIds)
          ->orWhereIn('no_inventaris_lama', $inventaris->pluck('no_inventaris'))
          ->orWhereIn('no_inventaris_baru', $inventaris->pluck('no_inventaris'))
          ->orWhereIn('kode_aset_lama', $inventaris->pluck('kode_aset'))
          ->orWhereIn('kode_aset_baru', $inventaris->pluck('kode_aset'));
      })
      ->orderBy('tanggal_mutasi')
      ->get();

    // Cari inventaris turunan/asal dari mutasi jika belum masuk di $inventaris
    $additionalInvIds = collect();
    foreach ($mutasiTerkait as $m) {
      if ($m->inventaris_id && !in_array($m->inventaris_id, $invIds)) {
        $additionalInvIds->push($m->inventaris_id);
      }
    }

    // Cari inventaris baru yang tercipta dari transaksi mutasi masuk
    if ($mutasiTerkait->isNotEmpty()) {
      $masukIds = Masuk::withoutGlobalScopes()
        ->whereIn('history_mutasi_id', $mutasiTerkait->pluck('id'))
        ->pluck('id');
      $invBaruIds = Inventaris::withoutGlobalScopes()
        ->whereIn('masuk_id', $masukIds)
        ->pluck('id');
      foreach ($invBaruIds as $ibId) {
        if (!in_array($ibId, $invIds)) {
          $additionalInvIds->push($ibId);
        }
      }
    }

    $allInventaris = $inventaris;
    if ($additionalInvIds->isNotEmpty()) {
      $extraInvs = Inventaris::withoutGlobalScopes()->with([
        'dataAset.kategori',
        'perusahaan',
        'masuk' => function ($q) {
          $q->withoutGlobalScopes()->with('supplier', 'perusahaanAsal');
        },
        'keluars' => function ($q) {
          $q->withoutGlobalScopes()->with(['karyawan', 'lokasi', 'user']);
        },
      ])->whereIn('id', $additionalInvIds->unique())->get();

      $allInventaris = $inventaris->concat($extraInvs)->unique('id');
    }
    $allInvIds = $allInventaris->pluck('id')->filter()->unique()->toArray();

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
      $timeline->push([
        'tanggal' => Carbon::parse($maint->created_at),
        'aktivitas' => 'MAINTENANCE',
        'kode_aset' => $allInventaris->firstWhere('id', $maint->inventaris_id)?->kode_aset ?? '-',
        'inventaris' => $allInventaris->firstWhere('id', $maint->inventaris_id)?->no_inventaris ?? '-',
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
    $historyCabut = HistoryPencabutan::with('creator')
      ->whereIn('inventaris_id', $allInvIds)
      ->orderBy('tanggal_pencabutan')
      ->get();

    foreach ($historyCabut as $item) {
      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal_pencabutan),
        'aktivitas' => 'PENCABUTAN',
        'kode_aset' => $item->kode_aset,
        'inventaris' => $item->no_inventaris,
        'user_lama' => $item->user_lama,
        'user_baru' => null,
        'lokasi_lama' => $item->lokasi_lama,
        'lokasi_baru' => $item->lokasi_baru ?? 'Gudang',
        'keterangan' => 'Pencabutan: ' . ($item->alasan ?? 'Penarikan unit dari pengguna'),
        'petugas' => $item->creator?->name ?? 'Petugas',
      ]);
    }

    // 6. HISTORY HAK AKSES
    $historyHakAkses = HistoryHakAkses::with(['access', 'user', 'maping.keluar', 'maping.lokasi'])
      ->whereHas('maping.keluar', function ($q) use ($allInvIds) {
        $q->withoutGlobalScopes()->whereIn('inventaris_id', $allInvIds);
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
        'kode_aset' => $inv?->kode_aset ?? '-',
        'inventaris' => $inv?->no_inventaris ?? '-',
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

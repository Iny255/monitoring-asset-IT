<?php

namespace App\Http\Controllers\main_dashboard;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\Masuk;
use App\Models\Keluar;
use App\Models\Peminjaman;
use App\Models\Maintenance;
use App\Models\Perusahaan;
use App\Models\Kategori;
use App\Models\DataAset;
use App\Models\Supplier;
use App\Models\Lokasi;
use App\Models\Karyawan;
use App\Models\Maping;
use App\Models\Access;
use App\Models\MapingAccess;
use App\Models\HistoryHakAkses;
use App\Models\HistoryMutasi;
use App\Models\HistoryPencabutan;

class DashboardPetugasController extends Controller
{
  public function petugas()
  {
    $now = Carbon::now('Asia/Jakarta');
    $user = auth()->user();

    $dashboard = [
      'master' => $this->master($user),

      'inventaris' => $this->inventaris($user),

      'penerimaan' => $this->penerimaan($user),

      'pemakaian' => $this->pemakaian($user),

      'mapping' => $this->mapping($user),

      'hak_akses' => $this->hakAkses($user),

      'mutasi' => $this->mutasi($user),

      'peminjaman' => $this->peminjaman($user),

      'maintenance' => $this->maintenance($user),

      'pencabutan' => $this->pencabutan($user),
      'transaksi_chart' => $this->transaksiChart($user),

      'grafik' => $this->grafik($user),

      'komposisi' => $this->komposisiAset($user),
      'timeline' => $this->timeline($user),

      'reminder' => $this->reminder($user),

      'aktivitas' => $this->aktivitas($user),
    ];

    return view('content.dashboard.dashboard-petugas', compact('now', 'dashboard'));
  }
  private function filterPerusahaan($query, $user, $column = 'perusahaan_id')
  {
    if ($user->role !== 'super_admin') {
      $query->where($column, $user->id_perusahaan);
    }

    return $query;
  }
  private function master($user)
  {
    return [
      'perusahaan' => $user->role == 'super_admin' ? Perusahaan::count() : 1,

      'kategori' => $this->filterPerusahaan(Kategori::query(), $user)->count(),

      'data_aset' => $this->filterPerusahaan(DataAset::query(), $user)->count(),

      'supplier' => $this->filterPerusahaan(Supplier::query(), $user)->count(),

      'lokasi' => $this->filterPerusahaan(Lokasi::query(), $user, 'id_perusahaan')->count(),

      'karyawan' => $this->filterPerusahaan(Karyawan::query(), $user, 'id_perusahaan')->count(),
      'hak_akses' => $this->filterPerusahaan(Access::query(), $user, 'id_perusahaan')->count(),
    ];
  }

  private function inventaris($user)
  {
    $inventaris = $this->filterPerusahaan(Inventaris::query(), $user);

    return [
      'total' => (clone $inventaris)->count(),

      'tersedia' => (clone $inventaris)->where('status', 'TERSEDIA')->count(),

      'dipakai' => (clone $inventaris)->where('status', 'DIPAKAI')->count(),

      'dipinjam' => (clone $inventaris)->where('status', 'DIPINJAM')->count(),

      'rusak' => (clone $inventaris)->where('status', 'RUSAK')->count(),

      'afkir' => (clone $inventaris)->where('status', 'AFKIR')->count(),
    ];
  }

  private function penerimaan($user)
  {
    $query = $this->filterPerusahaan(Masuk::query(), $user);

    return [
      'bulan_ini' => (clone $query)
        ->whereMonth('tanggal_pembelian', now()->month)
        ->whereYear('tanggal_pembelian', now()->year)
        ->count(),
      'hari_ini' => (clone $query)->whereDate('tanggal_pembelian', today())->count(),

      'total' => (clone $query)->count(),
    ];
  }

  private function pemakaian($user)
  {
    $query = $this->filterPerusahaan(Keluar::query(), $user);

    return [
      'bulan_ini' => (clone $query)
        ->whereMonth('tgl_keluar', now()->month)
        ->whereYear('tgl_keluar', now()->year)
        ->count(),
      'hari_ini' => (clone $query)->whereDate('tgl_keluar', today())->count(),

      'total' => (clone $query)->count(),
    ];
  }

  private function komposisiAset($user)
  {
    return $this->filterPerusahaan(Inventaris::with('dataAset.kategori'), $user)
      ->get()

      ->groupBy(function ($item) {
        return $item->dataAset->kategori->nama_barang ?? 'LAINNYA';
      })

      ->map(function ($items, $namaBarang) {
        return [
          'nama_barang' => $namaBarang,
          'total' => $items->where('status', 'TERSEDIA')->count(),
        ];
      })

      ->sortByDesc('total')

      ->values();
  }

  private function grafik($user)
  {
    $tahun = request('tahun', now()->year);

    $masuk = $this->filterPerusahaan(Masuk::query(), $user)
      ->whereYear('tanggal_pembelian', $tahun)
      ->selectRaw('MONTH(tanggal_pembelian) bulan, COUNT(*) total')
      ->groupBy('bulan')
      ->pluck('total', 'bulan')
      ->toArray();

    $keluar = $this->filterPerusahaan(Keluar::query(), $user)
      ->whereYear('tgl_keluar', $tahun)
      ->selectRaw('MONTH(tgl_keluar) bulan, COUNT(*) total')
      ->groupBy('bulan')
      ->pluck('total', 'bulan')
      ->toArray();

    $label = [];
    $dataMasuk = [];
    $dataKeluar = [];

    for ($i = 1; $i <= 12; $i++) {
      $label[] = Carbon::create()
        ->month($i)
        ->translatedFormat('M');

      $dataMasuk[] = $masuk[$i] ?? 0;

      $dataKeluar[] = $keluar[$i] ?? 0;
    }

    $tahunMasuk = $this->filterPerusahaan(Masuk::query(), $user)
      ->selectRaw('YEAR(tanggal_pembelian) y')
      ->whereNotNull('tanggal_pembelian')
      ->pluck('y');

    $tahunKeluar = $this->filterPerusahaan(Keluar::query(), $user)
      ->selectRaw('YEAR(tgl_keluar) y')
      ->whereNotNull('tgl_keluar')
      ->pluck('y');

    $availableYears = $tahunMasuk->merge($tahunKeluar)
      ->push(now()->year)
      ->unique()
      ->sortDesc()
      ->values()
      ->toArray();

    return [
      'label' => $label,

      'masuk' => $dataMasuk,

      'keluar' => $dataKeluar,

      'tahun' => (int) $tahun,

      'available_years' => $availableYears,
    ];
  }
  private function mapping($user)
  {
    $query = $this->filterPerusahaan(Maping::query(), $user, 'id_perusahaan');

    return [
      'total' => (clone $query)->count(),

      'aktif' => (clone $query)->where('status', 'aktif')->count(),

      'selesai' => (clone $query)->where('status', 'selesai')->count(),

      'servis' => (clone $query)->where('status', 'servis')->count(),

      'maintenance' => (clone $query)->where('status', 'maintenance')->count(),

      'bulan_ini' => (clone $query)
        ->whereMonth('tanggal_digunakan', now()->month)
        ->whereYear('tanggal_digunakan', now()->year)
        ->count(),
    ];
  }

  private function hakAkses($user)
  {
    /*
    |--------------------------------------------------------------------------
    | Hak Akses Aktif
    |--------------------------------------------------------------------------
    */

    $access = MapingAccess::query();

    if ($user->role != 'super_admin') {
      $access->whereHas('maping', function ($q) use ($user) {
        $q->where('id_perusahaan', $user->id_perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | History Hak Akses
    |--------------------------------------------------------------------------
    */

    $history = HistoryHakAkses::query();

    if ($user->role != 'super_admin') {
      $history->whereHas('maping', function ($q) use ($user) {
        $q->where('id_perusahaan', $user->id_perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | Hitung Statistik
    |--------------------------------------------------------------------------
    */

    $total = (clone $access)->count();

    $aktif = (clone $access)->where('status', 'aktif')->count();

    $persentase = $total > 0 ? round(($aktif / $total) * 100) : 0;

    return [
      /*
        |--------------------------------------------------------------------------
        | Kondisi Saat Ini
        |--------------------------------------------------------------------------
        */

      'total' => $total,

      'aktif' => $aktif,

      'nonaktif' => (clone $access)->where('status', 'nonaktif')->count(),

      /*
        |--------------------------------------------------------------------------
        | Aktivitas
        |--------------------------------------------------------------------------
        */

      'ditambah' => (clone $history)->where('aksi', 'tambah')->count(),

      'dihapus' => (clone $history)->where('aksi', 'hapus')->count(),

      'diupdate' => (clone $history)->where('aksi', 'update')->count(),

      /*
        |--------------------------------------------------------------------------
        | Bulan Ini
        |--------------------------------------------------------------------------
        */

      'bulan_ini' => (clone $history)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count(),

      /*
        |--------------------------------------------------------------------------
        | Persentase Aktif (Untuk Radial Chart)
        |--------------------------------------------------------------------------
        */

      'persentase' => $persentase,
    ];
  }

  private function mutasi($user)
  {
    $query = HistoryMutasi::query();

    if ($user->role != 'super_admin') {
      $query->where(function ($q) use ($user) {
        $q->where('id_perusahaan_asal', $user->id_perusahaan)->orWhere('id_perusahaan_tujuan', $user->id_perusahaan);
      });
    }

    return [
      /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

      'total' => (clone $query)->count(),

      'internal' => (clone $query)->where('jenis_mutasi', 'internal')->count(),

      'antar_perusahaan' => (clone $query)->where('jenis_mutasi', 'antar_perusahaan')->count(),

      /*
        |--------------------------------------------------------------------------
        | Waktu
        |--------------------------------------------------------------------------
        */

      'hari_ini' => (clone $query)->whereDate('tanggal_mutasi', today())->count(),

      'bulan_ini' => (clone $query)
        ->whereMonth('tanggal_mutasi', now()->month)
        ->whereYear('tanggal_mutasi', now()->year)
        ->count(),

      /*
        |--------------------------------------------------------------------------
        | Hak Akses
        |--------------------------------------------------------------------------
        */

      'copy_hak_akses' => (clone $query)->where('opsi_hak_akses', 'copy')->count(),

      'manual_hak_akses' => (clone $query)->where('opsi_hak_akses', 'manual')->count(),
    ];
  }

  private function peminjaman($user)
  {
    $query = Peminjaman::query();

    if ($user->role != 'super_admin') {
      $query->whereHas('inventaris', function ($q) use ($user) {
        $q->where('perusahaan_id', $user->id_perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | Hitung Statistik
    |--------------------------------------------------------------------------
    */

    $total = (clone $query)->count();

    $dikembalikan = (clone $query)->where('status', 'Dikembalikan')->count();

    $persentase = $total > 0 ? round(($dikembalikan / $total) * 100) : 0;

    return [
      /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

      'total' => $total,

      /*
        |--------------------------------------------------------------------------
        | Jenis Peminjaman
        |--------------------------------------------------------------------------
        */

      'internal' => (clone $query)->where('jenis_peminjaman', 'internal')->count(),

      'antar_perusahaan' => (clone $query)->where('jenis_peminjaman', 'antar_perusahaan')->count(),

      /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

      'dipinjam' => (clone $query)->where('status', 'Dipinjam')->count(),

      'dikembalikan' => $dikembalikan,

      'hilang' => (clone $query)->where('status', 'Hilang')->count(),

      /*
        |--------------------------------------------------------------------------
        | Periode
        |--------------------------------------------------------------------------
        */

      'hari_ini' => (clone $query)->whereDate('tanggal_pinjam', today())->count(),

      'bulan_ini' => (clone $query)
        ->whereMonth('tanggal_pinjam', now()->month)
        ->whereYear('tanggal_pinjam', now()->year)
        ->count(),

      /*
        |--------------------------------------------------------------------------
        | KPI Dashboard
        |--------------------------------------------------------------------------
        */

      'persentase' => $persentase,
    ];
  }

  private function maintenance($user)
  {
    $query = Maintenance::query();

    if ($user->role != 'super_admin') {
      $query->whereHas('inventaris', function ($q) use ($user) {
        $q->where('perusahaan_id', $user->id_perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | Hitung Statistik
    |--------------------------------------------------------------------------
    */

    $total = (clone $query)->count();

    $selesai = (clone $query)->where('status', 'Selesai')->count();

    $persentase = $total > 0 ? round(($selesai / $total) * 100) : 0;

    return [
      /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

      'total' => $total,

      'pengajuan' => (clone $query)->where('status', 'Pengajuan')->count(),

      'diproses' => (clone $query)->where('status', 'Diproses')->count(),

      'selesai' => $selesai,

      'tidak_dapat_diperbaiki' => (clone $query)->where('status', 'Tidak Dapat Diperbaiki')->count(),

      /*
        |--------------------------------------------------------------------------
        | Jenis
        |--------------------------------------------------------------------------
        */

      'service' => (clone $query)->where('jenis', 'Service')->count(),

      'maintenance' => (clone $query)->where('jenis', 'Maintenance')->count(),

      /*
        |--------------------------------------------------------------------------
        | Waktu
        |--------------------------------------------------------------------------
        */

      'hari_ini' => (clone $query)->whereDate('tanggal', today())->count(),

      'bulan_ini' => (clone $query)
        ->whereMonth('tanggal', now()->month)
        ->whereYear('tanggal', now()->year)
        ->count(),

      /*
        |--------------------------------------------------------------------------
        | KPI Radial Chart
        |--------------------------------------------------------------------------
        */

      'persentase' => $persentase,
    ];
  }

  private function pencabutan($user)
  {
    $query = HistoryPencabutan::query();

    if ($user->role != 'super_admin') {
      $query->where('id_perusahaan', $user->id_perusahaan);
    }

    return [
      /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

      'total' => (clone $query)->count(),

      /*
        |--------------------------------------------------------------------------
        | Periode
        |--------------------------------------------------------------------------
        */

      'hari_ini' => (clone $query)->whereDate('tanggal_pencabutan', today())->count(),

      'bulan_ini' => (clone $query)
        ->whereMonth('tanggal_pencabutan', now()->month)
        ->whereYear('tanggal_pencabutan', now()->year)
        ->count(),

      'tahun_ini' => (clone $query)->whereYear('tanggal_pencabutan', now()->year)->count(),

      /*
        |--------------------------------------------------------------------------
        | 5 Data Terbaru
        |--------------------------------------------------------------------------
        */

      'terbaru' => (clone $query)
        ->with('inventaris')
        ->latest('tanggal_pencabutan')
        ->take(5)
        ->get(),
    ];
  }
  private function transaksiChart($user)
  {
    return [
      'label' => ['Penerimaan', 'Pemakaian', 'Mutasi', 'Pencabutan'],

      'data' => [
        $this->penerimaan($user)['bulan_ini'],

        $this->pemakaian($user)['bulan_ini'],

        $this->mutasi($user)['bulan_ini'],

        $this->pencabutan($user)['bulan_ini'],
      ],
    ];
  }
  private function timeline($user)
  {
    $timeline = collect();

    /*
    |--------------------------------------------------------------------------
    | PENERIMAAN
    |--------------------------------------------------------------------------
    */

    $masuk = Masuk::query();

    if ($user->role != 'super_admin') {
      $masuk->where('perusahaan_id', $user->id_perusahaan);
    }

    foreach (
      $masuk
        ->latest('tanggal_pembelian')
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal_pembelian),

        'judul' => 'Penerimaan Aset',

        'icon' => 'bx-download',

        'color' => 'success',

        'deskripsi' => $item->kode_transaksi ?? 'Penerimaan aset',
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PEMAKAIAN
    |--------------------------------------------------------------------------
    */

    $keluar = Keluar::query();

    if ($user->role != 'super_admin') {
      $keluar->where('perusahaan_id', $user->id_perusahaan);
    }

    foreach (
      $keluar
        ->latest('tgl_keluar')
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'tanggal' => Carbon::parse($item->tgl_keluar),

        'judul' => 'Pemakaian Aset',

        'icon' => 'bx-desktop',

        'color' => 'primary',

        'deskripsi' => $item->kode_keluar ?? 'Pemakaian aset',
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MUTASI
    |--------------------------------------------------------------------------
    */

    $mutasi = HistoryMutasi::query();

    if ($user->role != 'super_admin') {
      $mutasi->where('id_perusahaan_asal', $user->id_perusahaan);
    }

    foreach (
      $mutasi
        ->latest('tanggal_mutasi')
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal_mutasi),

        'judul' => 'Mutasi',

        'icon' => 'bx-transfer-alt',

        'color' => 'warning',

        'deskripsi' => $item->nama_aset,
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MAINTENANCE
    |--------------------------------------------------------------------------
    */

    $maintenance = Maintenance::query();

    if ($user->role != 'super_admin') {
      $maintenance->whereHas('inventaris', function ($q) use ($user) {
        $q->where('perusahaan_id', $user->id_perusahaan);
      });
    }

    foreach (
      $maintenance
        ->latest('tanggal')
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal),

        'judul' => $item->jenis,

        'icon' => 'bx-wrench',

        'color' => 'danger',

        'deskripsi' => $item->kode_service,
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PENCABUTAN
    |--------------------------------------------------------------------------
    */

    $cabut = HistoryPencabutan::query();

    if ($user->role != 'super_admin') {
      $cabut->where('id_perusahaan', $user->id_perusahaan);
    }

    foreach (
      $cabut
        ->latest('tanggal_pencabutan')
        ->take(5)
        ->get()
      as $item
    ) {
      $timeline->push([
        'tanggal' => Carbon::parse($item->tanggal_pencabutan),

        'judul' => 'Pencabutan',

        'icon' => 'bx-trash',

        'color' => 'secondary',

        'deskripsi' => $item->nama_aset,
      ]);
    }

    return $timeline

      ->sortByDesc('tanggal')

      ->take(12)

      ->values();
  }

  private function reminder($user)
  {
    $maintenance = $this->maintenance($user);

    $mapping = $this->mapping($user);

    $peminjaman = $this->peminjaman($user);

    $inventaris = $this->inventaris($user);

    return [
      'maintenance_pengajuan' => $maintenance['pengajuan'],

      'maintenance_diproses' => $maintenance['diproses'],

      'peminjaman_aktif' => $peminjaman['dipinjam'],

      'mapping_servis' => $mapping['servis'],

      'mapping_maintenance' => $mapping['maintenance'],

      'inventaris_rusak' => $inventaris['rusak'],
    ];
  }

  private function aktivitas($user)
  {
    return [
      'penerimaan_hari_ini' => $this->penerimaan($user)['hari_ini'],

      'pemakaian_hari_ini' => $this->pemakaian($user)['hari_ini'],

      'mutasi_hari_ini' => $this->mutasi($user)['hari_ini'],

      'maintenance_hari_ini' => $this->maintenance($user)['hari_ini'],

      'peminjaman_hari_ini' => $this->peminjaman($user)['hari_ini'],

      'pencabutan_hari_ini' => $this->pencabutan($user)['hari_ini'],
    ];
  }
}

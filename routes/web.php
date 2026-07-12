<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;

use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MasukController;
use App\Http\Controllers\KeluarController;
use App\Http\Controllers\MapingController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DataAsetController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\MapingAccessController;
use App\Http\Controllers\HistoryHakAksesController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\HistoryMutasiController;
use App\Http\Controllers\PencabutanController;
use App\Http\Controllers\HistoryPencabutanController;
use App\Http\Controllers\HistoryStokController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\HistoryMaintenanceController;
use App\Http\Controllers\main_dashboard\DashboardPetugasController;

Route::get('/', function () {
  return redirect('/login');
});

Route::get('/login', [LoginController::class, 'index'])
  ->name('login')
  ->middleware('guest');

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/maping/{id}', [MapingController::class, 'publicShow'])
  ->where('id', '[0-9]+')
  ->name('maping.public_show');

Route::middleware(['auth'])->group(function () {
  /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    */

  Route::middleware(['role:super_admin'])->group(function () {
    Route::get('/dashboard/superadmin', [
      App\Http\Controllers\main_dashboard\DashboardSuperAdminController::class,
      'index',
    ])->name('dashboard.superadmin');

    Route::resource('/dashboard/user', DashboardUserController::class);
    Route::get('/dashboard/hapususer/{id}', [DashboardUserController::class, 'hapususer']);
    Route::get('/dashboard/detailuser/{id}', [DashboardUserController::class, 'show']);
    Route::resource('/dashboard/perusahaan', PerusahaanController::class);
  });

  Route::get('/maping/lokasi-by-perusahaan/{id}', [MapingController::class, 'getLokasiByPerusahaan']);

  Route::get('/dashboard/get-kategori/{id}', [DataAsetController::class, 'getKategori'])->name('data-aset.getKategori');
  /*
    |--------------------------------------------------------------------------
    | PETUGAS (SUPERADMIN JUGA BISA)
    |--------------------------------------------------------------------------
    */
  Route::middleware(['role:petugas,super_admin'])->group(function () {
    Route::get('/dashboard/petugas', [DashboardPetugasController::class, 'petugas'])->name('dashboard.petugas');

    Route::resource('/dashboard/aset', KategoriController::class)->names([
      'index' => 'aset.index',
      'create' => 'aset.create',
      'store' => 'aset.store',
      'show' => 'aset.show',
      'edit' => 'aset.edit',
      'update' => 'aset.update',
      'destroy' => 'aset.destroy',
    ]);
    Route::resource('/dashboard/useraset', KaryawanController::class)->except(['show']);
    Route::resource('/dashboard/lokasi', LokasiController::class);
    Route::resource('/dashboard/supplier', SupplierController::class);
    Route::resource('/dashboard/data-aset', DataAsetController::class);
    Route::resource('/dashboard/hak-akses', AccessController::class)->names('hak-akses');

    Route::get('/dashboard/transaksi-masuk/cetak', [MasukController::class, 'cetak'])->name('transaksi-masuk.cetak');
    Route::get('/dashboard/transaksi-masuk/stok', [MasukController::class, 'stok'])->name('transaksi-masuk.stok');
    Route::get('/stok/{dataAsetId}/history/cetak', [HistoryStokController::class, 'cetakHistoryStok'])->name(
      'stok.history.cetak'
    );
    Route::resource('/dashboard/transaksi-masuk', MasukController::class)->parameters([
      'transaksi-masuk' => 'masuk',
    ]);

    Route::get('/dashboard/get-supplier/{id}', [MasukController::class, 'getSupplier']);

    Route::get('/dashboard/get-data-aset/{id}', [MasukController::class, 'getDataAset']);

    Route::get('/dashboard/transaksi-keluar/cetak', [KeluarController::class, 'cetak'])->name('transaksi-keluar.cetak');
    Route::resource('/dashboard/transaksi-keluar', KeluarController::class);
    Route::get('/dashboard/get-kategori/{perusahaan}', [KeluarController::class, 'getKategori'])->name(
      'transaksi-keluar.getKategori'
    );
    Route::get('/dashboard/get-kategori/{perusahaan}', [KeluarController::class, 'getKategori'])->name(
      'transaksi-keluar.getKategori'
    );

    Route::post('/dashboard/get-inventaris', [KeluarController::class, 'getInventaris'])->name(
      'transaksi-keluar.getInventaris'
    );

    Route::get('/dashboard/get-inventaris-detail/{id}', [KeluarController::class, 'getInventarisDetail'])->name(
      'transaksi-keluar.getInventarisDetail'
    );

    Route::post('/dashboard/search-karyawan', [KeluarController::class, 'getKaryawan'])->name(
      'transaksi-keluar.searchKaryawan'
    );
    Route::get('/dashboard/get-lokasi/{perusahaan}', [KeluarController::class, 'getLokasi']);
    Route::get('/dashboard/peminjaman/search-karyawan', [PeminjamanController::class, 'searchKaryawan'])->name(
      'peminjaman.search'
    );
    Route::get('/dashboard/peminjaman/karyawan-perusahaan/{id}', [
      PeminjamanController::class,
      'getKaryawanPerusahaan',
    ])->name('peminjaman.karyawan-perusahaan');
    Route::get('/dashboard/peminjaman/inventaris-by-kategori/{kategori}', [
      PeminjamanController::class,
      'inventarisByKategori',
    ])->name('peminjaman.inventaris');
    Route::get('/dashboard/peminjaman/cetak', [PeminjamanController::class, 'cetak'])->name('peminjaman.cetak');
    Route::resource('/dashboard/peminjaman', PeminjamanController::class)->except(['destroy']);

    Route::get('/dashboard/transaksi-masuk/history-stok/{dataAsetId}', [
      HistoryStokController::class,
      'historyStok',
    ])->name('stok.history');
    Route::get('/dashboard/maping/get-lokasi', [MutasiController::class, 'getLokasi'])->name('maping.getLokasi');
    Route::get('/dashboard/maping/get-divisi', [MutasiController::class, 'getDivisi'])->name('maping.getDivisi');

    Route::get('/dashboard/maping/search-user-mutasi', [MutasiController::class, 'searchUserMutasi'])->name(
      'maping.searchUserMutasi'
    );
    Route::get('/dashboard/maping/print', [MapingController::class, 'print'])->name('maping.print');

    Route::get('/dashboard/maping/export-excel', [MapingController::class, 'exportExcel'])->name('maping.export.excel');
    Route::resource('/dashboard/maping', MapingController::class);
    Route::get('/maping/get-kategori', [MapingController::class, 'getKategori'])->name('maping.getKategori');

    Route::get('/maping/get-aset', [MapingController::class, 'getAset'])->name('maping.getAset');

    Route::get('/maping/get-detail-aset/{id}', [MapingController::class, 'getDetailAset'])->name(
      'maping.getDetailAset'
    );
    Route::get('/maping/get-access', [MapingController::class, 'getAccess'])->name('maping.getAccess');
    Route::get('/dashboard/karyawan/search', [MutasiController::class, 'searchKaryawan'])->name('karyawan.search');

    Route::get('/dashboard/hak-akses/filter/{jenis}', [AccessController::class, 'filterJenis'])->name(
      'hak-akses.filter'
    );
    Route::get('/dashboard/maping/{maping}/servis', [MaintenanceController::class, 'createFromMapping'])->name(
      'maping.servis'
    );
    Route::get('/dashboard/peminjaman/{peminjaman}/servis', [
      MaintenanceController::class,
      'createFromPeminjaman',
    ])->name('peminjaman.servis');
    Route::patch('/dashboard/maintenance/{maintenance}/proses', [MaintenanceController::class, 'proses'])->name(
      'maintenance.proses'
    );

    Route::patch('/dashboard/maintenance/{maintenance}/selesai', [MaintenanceController::class, 'selesai'])->name(
      'maintenance.selesai'
    );
    Route::patch('/dashboard/maintenance/{maintenance}/dibatalkan', [MaintenanceController::class, 'dibatalkan'])->name(
      'maintenance.dibatalkan'
    );

    Route::patch('/dashboard/maintenance/{maintenance}/tidak-dapat-diperbaiki', [
      MaintenanceController::class,
      'tidakDapatDiperbaiki',
    ])->name('maintenance.tidakDapatDiperbaiki');

   
    Route::get('/dashboard/maintenance/cetak', [MaintenanceController::class, 'cetak'])->name('maintenance.cetak');

    Route::resource('/dashboard/maintenance', MaintenanceController::class);
    Route::prefix('dashboard/history')
      ->middleware(['role:petugas,super_admin'])
      ->group(function () {
        // HISTORY HAK AKSES
        Route::get('/hak-akses', [HistoryHakAksesController::class, 'index'])->name('history.hak-akses.index');

        Route::get('/hak-akses/cetak', [HistoryHakAksesController::class, 'cetak'])->name('history.hak-akses.cetak');

        // HISTORY MUTASI
        Route::get('/mutasi', [HistoryMutasiController::class, 'index'])->name('history.mutasi.index');

        Route::get('/mutasi/{historyMutasi}', [HistoryMutasiController::class, 'show'])->name('history.mutasi.show');

        Route::get('/mutasi/{historyMutasi}/cetak', [HistoryMutasiController::class, 'print'])->name(
          'history.mutasi.cetak'
        );

        Route::get('/mutasi/cetak/semua', [HistoryMutasiController::class, 'printAll'])->name(
          'history.mutasi.cetak.semua'
        );

        // HISTORY PENCABUTAN
        Route::get('/pencabutan', [HistoryPencabutanController::class, 'index'])->name('history.pencabutan.index');

        Route::get('/pencabutan/cetak', [HistoryPencabutanController::class, 'cetak'])->name(
          'history.pencabutan.cetak'
        );
         Route::get('/maintenance', [HistoryMaintenanceController::class, 'index'])
            ->name('history.maintenance.index');
        Route::get('/maintenance/cetak', [HistoryMaintenanceController::class, 'cetak'])
            ->name('history.maintenance.cetak');
      });

    Route::prefix('dashboard/maping/{maping}')->group(function () {
      Route::get('/hak-akses', [MapingAccessController::class, 'index'])->name('maping.hak-akses');

      Route::post('/hak-akses', [MapingAccessController::class, 'store'])->name('maping.hak-akses.store');

      Route::delete('/hak-akses/{access}', [MapingAccessController::class, 'destroy'])->name(
        'maping.hak-akses.destroy'
      );

    });

    Route::prefix('dashboard/maping')
      ->name('maping.')
      ->group(function () {
        Route::get('{maping}/mutasi', [MutasiController::class, 'mutasiForm'])->name('mutasi');

        Route::post('{maping}/mutasi', [MutasiController::class, 'mutasiStore'])->name('mutasi.store');
      });

    Route::prefix('dashboard/pencabutan')
      ->name('pencabutan.')
      ->group(function () {
        Route::get('{maping}/create', [PencabutanController::class, 'create'])->name('create');

        Route::post('{maping}', [PencabutanController::class, 'store'])->name('store');
      });
  });
});

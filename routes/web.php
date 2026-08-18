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
use App\Http\Controllers\HistoryPemakaianController;
use App\Http\Controllers\HistoryPerjalananAsetController;
use App\Http\Controllers\main_dashboard\DashboardPetugasController;
use App\Http\Controllers\main_dashboard\DashboardSuperAdminController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCategoryController;
use App\Http\Controllers\UserAssetController;

Route::get('/', function () {
  return redirect('/login');
});

Route::get('/login', [LoginController::class, 'index'])
  ->name('login')
  ->middleware('guest');

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/maping/{id}', [MapingController::class, 'publicShow'])
  ->where('id', '[a-zA-Z0-9\-]+')
  ->name('maping.public_show');

Route::middleware(['auth'])->group(function () {
  /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    */

  Route::middleware(['role:super_admin'])->group(function () {
    Route::get('/dashboard/superadmin', [DashboardSuperAdminController::class, 'superAdmin'])->name(
      'dashboard.superadmin'
    );

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
    Route::get('/dashboard/aset/detail-perusahaan', [KategoriController::class, 'detailPerusahaan'])->name(
      'aset.detailPerusahaan'
    );

    Route::resource('/dashboard/aset', KategoriController::class)->names([
      'index' => 'aset.index',
      'create' => 'aset.create',
      'store' => 'aset.store',
      'show' => 'aset.show',
      'edit' => 'aset.edit',
      'update' => 'aset.update',
      'destroy' => 'aset.destroy',
    ]);
    Route::get('/dashboard/useraset/detail-perusahaan', [KaryawanController::class, 'detailPerusahaan'])->name(
      'useraset.detailPerusahaan'
    );
    Route::resource('/dashboard/useraset', KaryawanController::class)->except(['show']);
    Route::get('/dashboard/lokasi/detail-perusahaan', [LokasiController::class, 'detailPerusahaan'])->name(
      'lokasi.detailPerusahaan'
    );
    Route::resource('/dashboard/lokasi', LokasiController::class);
    Route::get('/dashboard/supplier/detail-perusahaan', [SupplierController::class, 'detailPerusahaan'])->name(
      'supplier.detailPerusahaan'
    );
    Route::resource('/dashboard/supplier', SupplierController::class);
    Route::resource('/dashboard/data-aset', DataAsetController::class);
    Route::get('/dashboard/hak-akses/detail-perusahaan', [AccessController::class, 'detailPerusahaan'])->name(
      'hak-akses.detailPerusahaan'
    );
    Route::resource('/dashboard/hak-akses', AccessController::class)->names('hak-akses');

    Route::get('/dashboard/transaksi-masuk/export-excel', [MasukController::class, 'exportExcel'])->name(
      'transaksi-masuk.exportExcel'
    );
    Route::get('/dashboard/transaksi-masuk/cetak', [MasukController::class, 'cetak'])->name('transaksi-masuk.cetak');
    Route::get('/dashboard/transaksi-masuk/stok', [MasukController::class, 'stok'])->name('transaksi-masuk.stok');
    Route::get('/stok/{dataAsetId}/history/cetak', [HistoryStokController::class, 'cetakHistoryStok'])->name(
      'stok.history.cetak'
    );
    Route::resource('/dashboard/transaksi-masuk', MasukController::class)->parameters([
      'transaksi-masuk' => 'masuk',
    ]);

    Route::get('/dashboard/get-supplier/{id}', [MasukController::class, 'getSupplier']);

    Route::get('/dashboard/get-kategori-masuk/{id}', [MasukController::class, 'getKategori']);

    Route::get('/dashboard/get-data-aset/{id}', [MasukController::class, 'getDataAset']);

    Route::get('/dashboard/transaksi-keluar/export-excel', [KeluarController::class, 'exportExcel'])->name(
      'transaksi-keluar.exportExcel'
    );
    Route::get('/dashboard/transaksi-keluar/cetak', [KeluarController::class, 'cetak'])->name('transaksi-keluar.cetak');
    Route::resource('/dashboard/transaksi-keluar', KeluarController::class);
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
    Route::get('/dashboard/peminjaman/export-excel', [PeminjamanController::class, 'exportExcel'])->name(
      'peminjaman.exportExcel'
    );
    Route::get('/dashboard/kategori/by-perusahaan/{id}', [PeminjamanController::class, 'kategoriByPerusahaan']);
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
    Route::get('/dashboard/maping/get-kategori', [MapingController::class, 'getKategori'])->name('maping.getKategori');
    Route::get('/dashboard/maping/get-available-inventaris', [MapingController::class, 'getAvailableInventaris'])->name('maping.getAvailableInventaris');
    Route::get('/dashboard/maping/get-filter-options', [MapingController::class, 'getFilterOptionsByPerusahaan'])->name('maping.getFilterOptions');
    Route::get('/dashboard/maping/get-lokasi-by-perusahaan/{id}', [MapingController::class, 'getLokasiByPerusahaan'])->name('maping.getLokasiByPerusahaan');
    Route::get('/dashboard/maping/search-karyawan', [MapingController::class, 'searchKaryawan'])->name('maping.searchKaryawan');
    Route::get('/dashboard/maping/pemakaian', [MapingController::class, 'pemakaian'])->name('maping.pemakaian');
    Route::resource('/dashboard/maping', MapingController::class);

    Route::get('/maping/get-aset', [MapingController::class, 'getAset'])->name('maping.getAset');

    Route::get('/maping/get-detail-aset/{id}', [MapingController::class, 'getDetailAset'])->name(
      'maping.getDetailAset'
    );
    Route::get('/maping/get-access', [MapingController::class, 'getAccess'])->name('maping.getAccess');
    Route::get('/dashboard/karyawan/search', [MutasiController::class, 'searchKaryawan'])->name('karyawan.search');

    Route::get('/dashboard/hak-akses/filter/{jenis}', [AccessController::class, 'filterJenis'])->name(
      'hak-akses.filter'
    );
    Route::get('/dashboard/maintenance/inventaris/{id}', [MaintenanceController::class, 'inventarisPerusahaan'])->name(
      'maintenance.inventarisPerusahaan'
    );

    Route::get('/dashboard/maintenance/inventaris-by-kategori/{kategori}', [
      MaintenanceController::class,
      'inventarisByKategori',
    ])->name('maintenance.inventarisKategori');
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
    Route::get('/dashboard/maintenance/export-excel', [MaintenanceController::class, 'exportExcel'])->name('maintenance.export_excel');

    Route::post('/dashboard/maintenance/{maintenance}/update-gambar', [
      MaintenanceController::class,
      'updateGambar',
    ])->name('maintenance.update_gambar');

    Route::resource('/dashboard/maintenance', MaintenanceController::class);
    Route::prefix('dashboard/history')
      ->middleware(['role:petugas,super_admin'])
      ->group(function () {
        // HISTORY PERJALANAN ASET
        Route::get('/perjalanan-aset', [HistoryPerjalananAsetController::class, 'index'])->name('history.perjalanan.index');
        Route::get('/perjalanan-aset/export-excel', [HistoryPerjalananAsetController::class, 'exportExcelIndex'])->name('history.perjalanan.export_excel_index');
        Route::get('/perjalanan-aset/{id}', [HistoryPerjalananAsetController::class, 'show'])->name('history.perjalanan.show');
        Route::get('/perjalanan-aset/{id}/cetak', [HistoryPerjalananAsetController::class, 'cetak'])->name('history.perjalanan.cetak');
        Route::get('/perjalanan-aset/{id}/export-excel', [HistoryPerjalananAsetController::class, 'exportExcel'])->name('history.perjalanan.export_excel');

        // HISTORY HAK AKSES (REDIRECT TO HISTORY PERJALANAN ASET)
        Route::get('/hak-akses', function () {
          return redirect()->route('history.perjalanan.index');
        })->name('history.hak-akses.index');

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
        Route::get('/maintenance', [HistoryMaintenanceController::class, 'index'])->name('history.maintenance.index');
        Route::get('/maintenance/cetak', [HistoryMaintenanceController::class, 'cetak'])->name(
          'history.maintenance.cetak'
        );
        Route::get('/maintenance/export-excel', [HistoryMaintenanceController::class, 'exportExcel'])->name(
          'history.maintenance.export_excel'
        );
      });

    Route::prefix('dashboard/maping/{maping}')->group(function () {
      Route::get('/hak-akses', [MapingAccessController::class, 'index'])->name('maping.hak-akses');

      Route::post('/hak-akses', [MapingAccessController::class, 'store'])->name('maping.hak-akses.store');

      Route::post('/hak-akses/bulk-update', [MapingAccessController::class, 'bulkUpdate'])->name('maping.hak-akses.bulk-update');

      Route::put('/hak-akses/{access}', [MapingAccessController::class, 'update'])->name('maping.hak-akses.update');

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

  Route::get('/dashboard/aset-saya', [UserAssetController::class, 'index'])->name('aset-saya.index')->middleware(['role:user,karyawan']);

  /*
  |--------------------------------------------------------------------------
  | E-TICKETING HELPDESK IT SUPPORT
  |--------------------------------------------------------------------------
  */
  Route::prefix('dashboard/e-ticket')->name('e-ticket.')->middleware(['role:user,karyawan,petugas,super_admin'])->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::get('/create', [TicketController::class, 'create'])->name('create');
    Route::post('/', [TicketController::class, 'store'])->name('store');
    Route::get('/{id}', [TicketController::class, 'show'])->name('show');
    Route::post('/{id}/reply', [TicketController::class, 'storeReply'])->name('reply');
    Route::put('/{id}/status', [TicketController::class, 'updateStatus'])->name('update-status');
    Route::post('/{id}/convert-maintenance', [TicketController::class, 'convertToMaintenance'])->name('convert-maintenance');
  });

  Route::prefix('dashboard/ticket-categories')->name('ticket-categories.')->middleware(['role:petugas,super_admin'])->group(function () {
    Route::get('/', [TicketCategoryController::class, 'index'])->name('index');
    Route::post('/', [TicketCategoryController::class, 'store'])->name('store');
    Route::put('/{id}', [TicketCategoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [TicketCategoryController::class, 'destroy'])->name('destroy');
  });
});

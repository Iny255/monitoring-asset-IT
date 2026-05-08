<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;

use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MasukController;
use App\Http\Controllers\KeluarController;
use App\Http\Controllers\MapingController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\main_dashboard\DashboardManagerController;
use App\Http\Controllers\main_dashboard\DashboardPetugasController;
use App\Http\Controllers\ManagerMapingController;

Route::get('/', function () {
  return redirect('/login');
});

Route::get('/login', [LoginController::class, 'index'])
  ->name('login')
  ->middleware('guest');

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/get-kode-kategori/{id}', [KategoriController::class, 'getKode']);

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
  Route::get('/dashboard/get-kode-lokasi/{id}', [LokasiController::class, 'getKode']);
  Route::get('/dashboard/get-kode-masuk/{id}', [MasukController::class, 'getKode']);
  Route::get('/dashboard/get-kategori/{id}', [MasukController::class, 'getKategori']);
  Route::get('/dashboard/get-kode-keluar/{id}', [KeluarController::class, 'getKodeKeluar']);
  Route::get('/maping/lokasi-by-perusahaan/{id}', [MapingController::class, 'getLokasiByPerusahaan']);
  /*
    |--------------------------------------------------------------------------
    | PETUGAS (SUPERADMIN JUGA BISA)
    |--------------------------------------------------------------------------
    */
  Route::middleware(['role:petugas,super_admin'])->group(function () {
    Route::get('/dashboard/petugas', [DashboardPetugasController::class, 'petugas'])->name('dashboard.petugas');

    Route::resource('/dashboard/kategori', KategoriController::class);
    Route::resource('/dashboard/karyawan', KaryawanController::class)->except(['show']);
    Route::resource('/dashboard/lokasi', LokasiController::class);
    Route::get('/dashboard/transaksi-masuk/stok', [MasukController::class, 'stok'])->name('transaksi-masuk.stok');
    Route::resource('/dashboard/transaksi-masuk', MasukController::class)->parameters([
      'transaksi-masuk' => 'masuk',
    ]);
    Route::get('/dashboard/transaksi-masuk/{masuk}/download', [MasukController::class, 'download'])->name(
      'transaksi-masuk.download'
    );

    Route::resource('/dashboard/transaksi-keluar', KeluarController::class);
    Route::post('/dashboard/transaksi-keluar/autofill', [KeluarController::class, 'autofillByKodeMasuk'])->name(
      'transaksi-keluar.autofill'
    );
    Route::post('/dashboard/transaksi-keluar/get-karyawan', [KeluarController::class, 'getKaryawanByNama'])->name(
      'keluar.getKaryawanByNama'
    );
    Route::get('/dashboard/maping/print', [MapingController::class, 'print'])->name('maping.print');
    Route::resource('/dashboard/maping', MapingController::class);
    Route::post('/dashboard/maping/get-barang', [MapingController::class, 'getBarangByKeluar'])->name(
      'maping.getBarang'
    );
    Route::get('/dashboard/karyawan/search', [MapingController::class, 'searchKaryawan'])->name('karyawan.search');

    Route::get('/dashboard/maping/mutasi/{id}', [MapingController::class, 'mutasiForm'])->name('maping.mutasi');
    Route::post('/dashboard/maping/mutasi/{id}', [MapingController::class, 'mutasiStore'])->name('maping.mutasi.store');
    Route::get('/dashboard/history/mutasi', [MapingController::class, 'historyGlobal'])->name('maping.historyGlobal');
    Route::get('/maping/{id}/history-user', [MapingController::class, 'historyUser'])->name('maping.historyUser');

    Route::get('/maping/{id}/detail-ajax', [MapingController::class, 'detailAjax'])->name('maping.detailAjax');

    Route::post('/dashboard/maping/cabut/{id}', [MapingController::class, 'cabut'])->name('maping.cabut');
    Route::get('/dashboard/history/pencabutan', [MapingController::class, 'historyCabut'])->name('maping.historyCabut');
  });

  /*
    |--------------------------------------------------------------------------
    | MANAGER (SUPERADMIN JUGA BISA)
    |--------------------------------------------------------------------------
    */
  Route::middleware(['role:manager,super_admin'])->group(function () {
    Route::get('/dashboard/manager', [DashboardManagerController::class, 'index'])->name('dashboard.manager');

    Route::get('/manager/laporan/stok', [LaporanController::class, 'stok'])->name('manager.laporan.stok');

    Route::get('/manager/laporan/masuk', [LaporanController::class, 'laporanMasuk'])->name('manager.laporan.masuk');
    Route::get('/manager/laporan/masuk/{id}', [LaporanController::class, 'show'])->name('manager.laporan.masuk.show');

    Route::get('/manager/laporan/keluar', [LaporanController::class, 'laporanKeluar'])->name('manager.laporan.keluar');
    Route::get('/manager/laporan/keluar/{id}', [LaporanController::class, 'showKeluar'])->name(
      'manager.laporan.keluar.show'
    );

    Route::get('/manager/laporan/peminjaman', [LaporanController::class, 'laporanPeminjaman'])->name(
      'manager.laporan.peminjaman'
    );

    Route::get('/manager/laporan/peminjaman/{id}', [LaporanController::class, 'showPeminjaman'])->name(
      'manager.laporan.peminjaman.show'
    );

    Route::get('/manager/maping', [ManagerMapingController::class, 'maping'])->name('manager.maping.index');

    Route::get('/manager/maping/print', [ManagerMapingController::class, 'cetakmaping'])->name('manager.maping.cetak');
    Route::get('/manager/maping/{id}', [ManagerMapingController::class, 'showmaping'])->name('manager.maping.show');
  });

  /*
    |--------------------------------------------------------------------------
    | SHARED (SEMUA ROLE)
    |--------------------------------------------------------------------------
    */
  Route::get('/dashboard/peminjaman/search-karyawan', [PeminjamanController::class, 'searchKaryawan'])->name(
    'peminjaman.search'
  );
  Route::middleware(['role:manager,petugas,super_admin'])->group(function () {
    Route::resource('/dashboard/peminjaman', PeminjamanController::class);
    Route::get('/dashboard/peminjaman/get-nama-barang/{kode}', [PeminjamanController::class, 'getNamaBarang']);
    Route::get('/peminjaman/cek-status/{kode}', [PeminjamanController::class, 'cekStatus']);

    Route::get('/dashboard/history/mutasi', [MapingController::class, 'historyGlobal'])->name('maping.historyGlobal');
    Route::get('/maping/{id}/history-user', [MapingController::class, 'historyUser'])->name('maping.historyUser');
    Route::delete('/mutasi/{id}', [MapingController::class, 'destroyMutasi']);

    Route::get('/dashboard/history/pencabutan', [MapingController::class, 'historyCabut'])->name('maping.historyCabut');

    Route::delete('/dashboard/history/pencabutan/{id}', [MapingController::class, 'hapusCabut'])->name(
      'history.cabut.hapus'
    );

    Route::get('/maping/{id}/detail-ajax', [MapingController::class, 'detailAjax'])->name('maping.detail');
  });
});

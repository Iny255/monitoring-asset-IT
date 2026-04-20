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

Route::middleware(['auth'])->group(function () {

  /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    */
  Route::middleware(['role:super_admin'])->group(function () {

    Route::get(
      '/dashboard/superadmin',
      [App\Http\Controllers\main_dashboard\DashboardSuperAdminController::class, 'index']
    )->name('dashboard.superadmin');

    Route::resource('/dashboard/user', DashboardUserController::class);
    Route::resource('/dashboard/perusahaan', PerusahaanController::class);
  });


  /*
    |--------------------------------------------------------------------------
    | PETUGAS (SUPERADMIN JUGA BISA)
    |--------------------------------------------------------------------------
    */
  Route::middleware(['role:petugas,super_admin'])->group(function () {

    Route::get(
      '/dashboard/petugas',
      [DashboardPetugasController::class, 'petugas']
    )->name('dashboard.petugas');

    Route::resource('/dashboard/kategori', KategoriController::class);
    Route::resource('/dashboard/karyawan', KaryawanController::class);
    Route::resource('/dashboard/lokasi', LokasiController::class);
    Route::get('/dashboard/transaksi-masuk/stok', [MasukController::class, 'stok'])
      ->name('transaksi-masuk.stok');
    Route::resource('/dashboard/transaksi-masuk', MasukController::class)
      ->parameters([
        'transaksi-masuk' => 'masuk'
      ]);
    Route::get(
      '/dashboard/transaksi-masuk/{masuk}/download',
      [MasukController::class, 'download']
    )->name('transaksi-masuk.download');

    Route::resource('/dashboard/transaksi-keluar', KeluarController::class);
    Route::post(
      '/dashboard/transaksi-keluar/autofill',
      [KeluarController::class, 'autofillByKodeMasuk']
    )->name('transaksi-keluar.autofill');
    Route::post(
      '/dashboard/transaksi-keluar/get-karyawan',
      [KeluarController::class, 'getKaryawanByNama']
    )->name('keluar.getKaryawanByNama');
    Route::resource('/dashboard/maping', MapingController::class);
    Route::get('/dashboard/maping/mutasi/{id}', [MapingController::class, 'mutasi'])->name('maping.mutasi');
    Route::post('/dashboard/maping/cabut/{id}', [MapingController::class, 'cabut'])->name('maping.cabut');
  });


  /*
    |--------------------------------------------------------------------------
    | MANAGER (SUPERADMIN JUGA BISA)
    |--------------------------------------------------------------------------
    */
  Route::middleware(['role:manager,super_admin'])->group(function () {

    Route::get(
      '/dashboard/manager',
      [DashboardManagerController::class, 'index']
    )->name('dashboard.manager');

    Route::get(
      '/manager/laporan/stok',
      [LaporanController::class, 'stok']
    )->name('manager.laporan.stok');

    Route::get(
      '/manager/laporan/masuk',
      [LaporanController::class, 'laporanMasuk']
    )->name('manager.laporan.masuk');

    Route::get(
      '/manager/laporan/keluar',
      [LaporanController::class, 'laporanKeluar']
    )->name('manager.laporan.keluar');
  });


  /*
    |--------------------------------------------------------------------------
    | SHARED (SEMUA ROLE)
    |--------------------------------------------------------------------------
    */
  Route::middleware(['role:manager,petugas,super_admin'])->group(function () {

    Route::resource('/dashboard/peminjaman', PeminjamanController::class);

    Route::get(
      '/dashboard/history/mutasi',
      [MapingController::class, 'historyGlobal']
    )->name('maping.historyGlobal');

    Route::get(
      '/dashboard/history/pencabutan',
      [MapingController::class, 'historyCabut']
    )->name('maping.historyCabut');

    Route::delete(
      '/dashboard/history/pencabutan/{id}',
      [MapingController::class, 'hapusCabut']
    )->name('history.cabut.hapus');

    Route::get(
      '/maping/{id}/detail-ajax',
      [MapingController::class, 'detailAjax']
    )->name('maping.detail');
  });
});

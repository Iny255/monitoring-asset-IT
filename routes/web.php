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
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DataAsetController;
use App\Http\Controllers\main_dashboard\DashboardPetugasController;

Route::get('/', function () {
  return redirect('/login');
});

Route::get('/login', [LoginController::class, 'index'])
  ->name('login')
  ->middleware('guest');

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/maping/{id}', [MapingController::class, 'publicShow'])->name('maping.public_show');

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
  Route::get('/dashboard/peminjaman/lokasi-by-perusahaan/{id}', [PeminjamanController::class, 'lokasiByPerusahaan']);
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

    Route::get('/dashboard/transaksi-masuk/cetak', [MasukController::class, 'cetak'])->name('transaksi-masuk.cetak');
    Route::get('/dashboard/transaksi-masuk/stok', [MasukController::class, 'stok'])->name('transaksi-masuk.stok');
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
    Route::get('/dashboard/transaksi-masuk/history-stok/{dataAsetId}', [MasukController::class, 'historyStok'])->name(
      'stok.history'
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
    | SHARED (SEMUA ROLE)
    |--------------------------------------------------------------------------
    */
  Route::get('/dashboard/peminjaman/search-karyawan', [PeminjamanController::class, 'searchKaryawan'])->name(
    'peminjaman.search'
  );
  Route::middleware(['role:petugas,super_admin'])->group(function () {
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

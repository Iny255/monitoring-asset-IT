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

// Route::get('/', [LoginController::class, 'index'])
//     ->name('login')
//     ->middleware('guest');

// // Auth Routes
// Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
// Route::post('/login', [LoginController::class, 'authenticate']);
// Route::get('/register', [RegisterController::class, 'index'])->name('register')->middleware('guest');
// Route::post('/register', [RegisterController::class, 'store']);
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'index'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // ===== PETUGAS =====
    Route::middleware(['role:petugas'])->group(function () {

        Route::get('/dashboard/petugas', [DashboardPetugasController::class, 'petugas'])
            ->name('dashboard.petugas');
        Route::resource('/dashboard/user', DashboardUserController::class);

        Route::get('/dashboard/hapususer/{id}', [DashboardUserController::class, 'hapususer'])
            ->name('user.hapus');
        Route::get('/dashboard/edituser/{id}', [DashboardUserController::class, 'edit'])
            ->name('user.edit');
        Route::put('/dashboard/user/update/{id}', [DashboardUserController::class, 'update'])
            ->name('user.update');
        Route::get('/dashboard/detailuser/{id}', [DashboardUserController::class, 'show'])
            ->name('user.show');

        Route::resource('/dashboard/kategori', KategoriController::class);
        Route::resource('/dashboard/karyawan', KaryawanController::class);
        Route::resource('/dashboard/perusahaan', PerusahaanController::class);

        Route::resource('/dashboard/lokasi', LokasiController::class);

        Route::resource('dashboard/transaksi-masuk', MasukController::class);

        //stok opname//
        Route::get('/dashboard/stok', [MasukController::class, 'stok'])
            ->name('masuk.stok');


        Route::resource('/dashboard/transaksi-keluar', KeluarController::class);

        Route::get('/dashboard/masuk-by-kode/{kode}', [KeluarController::class, 'getMasukByKode'])
            ->name('keluar.getMasukByKode');
        Route::post(
            '/dashboard/transaksi-keluar/autofill',
            [KeluarController::class, 'autofillByKodeMasuk']
        )->name('transaksi-keluar.autofill');
        Route::post(
            '/dashboard/karyawan-by-nama',
            [KeluarController::class, 'getKaryawanByNama']
        )->name('keluar.getKaryawanByNama');
        Route::get('/dashboard/hapus/{id}', [KeluarController::class, 'hapus'])
            ->name('keluar.hapus');

        Route::resource('/dashboard/maping', MapingController::class);
        Route::post(
            '/dashboard/maping/get-barang',
            [MapingController::class, 'getBarangByKeluar']
        )->name('maping.getBarang');
        Route::get('/maping/print', [MapingController::class, 'print'])->name('maping.print');

        //mutasi
        Route::get('maping/{id}/mutasi', [MapingController::class, 'mutasiForm'])->name('maping.mutasi');
        Route::post('maping/{id}/mutasi', [MapingController::class, 'mutasiStore'])->name('maping.mutasi.store');
        // Route::get('/dashboard/history/mutasi', [MapingController::class, 'historyGlobal'])
        //     ->name('maping.historyGlobal');
        Route::get('/karyawan/search', [MapingController::class, 'searchKaryawan'])
            ->name('karyawan.search');
        // Route::delete('/dashboard/mutasi/{id}', [MapingController::class, 'destroyMutasi'])
        //     ->name('maping.mutasi.destroy');
    });

    //peminjaman
    Route::resource('/dashboard/peminjaman', PeminjamanController::class);
    Route::get('/peminjaman/get-nama-barang/{kode}', [PeminjamanController::class, 'getNamaBarang']);
    Route::get('/peminjaman/search-karyawan', [PeminjamanController::class, 'searchKaryawan'])
        ->name('peminjaman.searchKaryawan');
    Route::get('/peminjaman/cek-status/{kode}', function ($kode) {

        $masihDipinjam = \App\Models\Peminjaman::whereHas('keluar', function ($q) use ($kode) {
            $q->where('kode_barang', $kode);
        })
            ->where('status', 'Dipinjam') // HARUS SAMA DENGAN DB
            ->exists();

        return response()->json([
            'dipinjam' => $masihDipinjam
        ]);
    });

    Route::middleware(['role:manager'])->group(function () {

        Route::get('/dashboard/manager', [DashboardManagerController::class, 'index'])
            ->name('dashboard.manager');

        /* ================= LAPORAN ================= */

        Route::get('/manager/laporan/stok', [LaporanController::class, 'stok'])
            ->name('manager.laporan.stok');

        Route::get('/manager/cetak/stok', [LaporanController::class, 'cetakStok'])
            ->name('manager.cetak.stok');
        Route::get('/manager/laporan/masuk', [LaporanController::class, 'laporanMasuk'])
            ->name('manager.laporan.masuk');
        Route::get('/manager/laporan/masuk{id}', [LaporanController::class, 'show'])
            ->name('laporan.masuk.show');

        Route::get('/manager/laporan/keluar', [LaporanController::class, 'laporanKeluar'])
            ->name('manager.laporan.keluar');
        Route::get('/manager/laporan/keluar/{id}', [LaporanController::class, 'showkeluar'])
            ->name('laporan.keluar.show');

        Route::get('/manager/laporan/peminjaman', [LaporanController::class, 'laporanPeminjaman'])
            ->name('manager.laporan.peminjaman');
        Route::get('/manager/laporan/peminjaman/{id}', [LaporanController::class, 'showpeminjaman'])
            ->name('laporan.peminjaman.show');

        // maping manager
        Route::get('/manager/maping', [ManagerMapingController::class, 'maping'])
            ->name('manager.maping');

        Route::get('/manager/maping/cetak', [ManagerMapingController::class, 'cetakmaping'])
            ->name('manager.maping.cetak');

        Route::get('/manager/maping/{id}', [ManagerMapingController::class, 'showmaping'])
            ->name('manager.maping.show');
    });

    // ===== MANAGER & PETUGAS =====
    Route::middleware(['role:manager,petugas'])->group(function () {
        Route::get('/dashboard/history/mutasi', [MapingController::class, 'historyGlobal'])
            ->name('maping.historyGlobal');
        Route::delete('/dashboard/mutasi/{id}', [MapingController::class, 'destroyMutasi'])
            ->name('maping.mutasi.destroy');
    });
});

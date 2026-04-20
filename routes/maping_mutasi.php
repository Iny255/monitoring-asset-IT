<?php

use Illuminate\Support\Facades\Route;

// Temporary route for mutasi until full implementation
Route::middleware(['auth', 'role:petugas,super_admin'])->group(function () {
    Route::get('/dashboard/maping/mutasi/{id}', function ($id) {
        return redirect()->route('maping.index')->with('error', 'Fitur mutasi dalam pengembangan');
    })->name('maping.mutasi');
});
?>


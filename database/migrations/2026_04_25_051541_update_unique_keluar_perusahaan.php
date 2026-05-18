<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('keluars', function (Blueprint $table) {
      // HAPUS UNIQUE LAMA
      $table->dropUnique('keluars_kode_keluar_unique');

      // OPTIONAL:
      // kalau kode_barang juga sebelumnya unique
      // hapus juga bila ada
      // $table->dropUnique('keluars_kode_barang_unique');

      // BUAT UNIQUE PER PERUSAHAAN
      $table->unique(['id_perusahaan', 'kode_keluar'], 'keluars_perusahaan_kode_keluar_unique');

      // OPTIONAL
      $table->unique(['id_perusahaan', 'kode_barang'], 'keluars_perusahaan_kode_barang_unique');
    });
  }

  public function down(): void
  {
    Schema::table('keluars', function (Blueprint $table) {
      // DROP COMPOSITE UNIQUE
      $table->dropUnique('keluars_perusahaan_kode_keluar_unique');

      $table->dropUnique('keluars_perusahaan_kode_barang_unique');

      // KEMBALIKAN UNIQUE LAMA
      $table->unique('kode_keluar');

      // OPTIONAL
      // $table->unique('kode_barang');
    });
  }
};

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
    Schema::table('lokasis', function (Blueprint $table) {
      // ❌ hapus unique global
      $table->dropUnique(['kode_lokasi']);

      // ✅ tambah unique per perusahaan
      $table->unique(['kode_lokasi', 'id_perusahaan']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('lokasis', function (Blueprint $table) {
      $table->dropUnique(['kode_lokasi', 'id_perusahaan']);

      $table->unique('kode_lokasi');
    });
  }
};

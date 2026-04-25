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
      // ❌ JANGAN drop apapun (karena tidak ada unique)

      // ✅ langsung buat composite unique
      $table->unique(['kode_keluar', 'id_perusahaan']);
      $table->unique(['kode_barang', 'id_perusahaan']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('keluars', function (Blueprint $table) {
      $table->dropUnique(['kode_keluar', 'id_perusahaan']);
      $table->dropUnique(['kode_barang', 'id_perusahaan']);
    });
  }
};

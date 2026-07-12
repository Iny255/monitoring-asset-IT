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
    Schema::table('maintenances', function (Blueprint $table) {
      // Asal transaksi
      $table
        ->enum('asal', ['Manual', 'Mapping', 'Peminjaman'])
        ->default('Manual')
        ->after('peminjaman_id');

      // Referensi Mapping
      $table
        ->foreignId('maping_id')
        ->nullable()
        ->after('asal')
        ->constrained('mapings')
        ->cascadeOnUpdate()
        ->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('maintenances', function (Blueprint $table) {
      $table->dropForeign(['maping_id']);

      $table->dropColumn(['asal', 'maping_id']);
    });
  }
};

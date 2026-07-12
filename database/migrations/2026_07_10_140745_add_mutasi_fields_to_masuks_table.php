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
    Schema::table('masuks', function (Blueprint $table) {
      $table
        ->enum('jenis_masuk', ['Pembelian', 'Mutasi'])
        ->default('Pembelian')
        ->after('data_aset_id');

      $table
        ->foreignId('perusahaan_asal')
        ->nullable()
        ->after('supplier_id')
        ->constrained('perusahaans')
        ->nullOnDelete();

      $table
        ->foreignId('history_mutasi_id')
        ->nullable()
        ->after('perusahaan_asal')
        ->constrained('history_mutasis')
        ->nullOnDelete();
    });
  }

  public function down(): void
  {
    Schema::table('masuks', function (Blueprint $table) {
      $table->dropForeign(['history_mutasi_id']);
      $table->dropForeign(['perusahaan_asal']);

      $table->dropColumn(['history_mutasi_id', 'perusahaan_asal', 'jenis_masuk']);
    });
  }
};

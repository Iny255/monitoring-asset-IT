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
    Schema::create('inventaris', function (Blueprint $table) {
      $table->id();

      $table
        ->foreignId('masuk_id')
        ->constrained('masuks')
        ->cascadeOnDelete();

      $table
        ->foreignId('perusahaan_id')
        ->constrained('perusahaans')
        ->cascadeOnDelete();

      $table
        ->foreignId('data_aset_id')
        ->constrained('data_asets')
        ->cascadeOnDelete();

      $table->string('kode_aset');

      $table->string('no_inventaris');
      $table->enum('status', ['TERSEDIA', 'DIPAKAI', 'DIPINJAM', 'RUSAK'])->default('TERSEDIA');

      $table->timestamps();

      // unik per perusahaan
      $table->unique(['perusahaan_id', 'kode_aset']);

      $table->unique(['perusahaan_id', 'no_inventaris']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('inventaris');
  }
};

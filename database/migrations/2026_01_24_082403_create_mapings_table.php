<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('mapings', function (Blueprint $table) {
      $table->id();

      // RELASI
      $table
        ->foreignId('id_keluar')
        ->constrained('keluars')
        ->cascadeOnDelete();

      $table
        ->foreignId('id_lokasi')
        ->constrained('lokasis')
        ->cascadeOnDelete();

      $table
        ->foreignId('id_perusahaan')
        ->constrained('perusahaans')
        ->cascadeOnDelete();

      // SPESIFIKASI DEVICE
      $table->string('processor', 100)->nullable();

      $table->string('ram', 20)->nullable();

      $table->string('device_id', 100)->nullable();

      $table->string('produk_id', 100)->nullable();

      $table->string('system', 50)->nullable();

      $table->string('version', 50)->nullable();

      $table->date('instal_on')->nullable();
      $table->date('tanggal_digunakan');

      $table->text('catatan')->nullable();

      // STATUS MAPPING
      $table->enum('status', ['aktif', 'selesai', 'dipinjam', 'servis', 'maintenance'])->default('aktif');

      $table->timestamps();

      // INDEX
      $table->index('id_perusahaan');
      $table->index('id_keluar');
      $table->index('id_lokasi');

      // UNIQUE
      $table->unique(['device_id', 'id_perusahaan']);
      $table->unique(['produk_id', 'id_perusahaan']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('mapings');
  }
};

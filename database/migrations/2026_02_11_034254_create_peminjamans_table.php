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
    Schema::create('peminjamans', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('kategori_id')
        ->constrained()
        ->cascadeOnDelete();
      $table
        ->foreignId('karyawan_id')
        ->constrained()
        ->cascadeOnDelete();
      $table
        ->foreignId('perusahaan_id')
        ->constrained()
        ->cascadeOnDelete();
      $table
        ->foreignId('lokasi_id')
        ->constrained()
        ->cascadeOnDelete();

      $table->date('tanggal_pinjam');
      $table->date('tanggal_rencana_kembali');
      $table->date('tanggal_kembali')->nullable();

      $table->enum('status', ['pending', 'disetujui', 'dipinjam', 'dikembalikan', 'ditolak'])->default('pending');

      $table->text('keperluan',100)->nullable();
      $table->text('catatan',100)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('peminjamans');
  }
};

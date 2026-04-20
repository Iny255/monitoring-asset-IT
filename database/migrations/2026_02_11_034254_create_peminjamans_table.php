<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('peminjamans', function (Blueprint $table) {
      $table->id();

      // ================= RELASI =================
      $table
        ->foreignId('kategori_id')
        ->constrained('kategoris')
        ->cascadeOnDelete();

      $table
        ->foreignId('karyawan_id')
        ->constrained('karyawans')
        ->cascadeOnDelete();

      $table
        ->foreignId('perusahaan_id')
        ->constrained('perusahaans')
        ->cascadeOnDelete();

      $table
        ->foreignId('lokasi_id')
        ->constrained('lokasis')
        ->cascadeOnDelete();

      // 🔥 TAMBAH INI (WAJIB)
      $table
        ->foreignId('keluar_id')
        ->nullable()
        ->constrained('keluars')
        ->nullOnDelete();

      // ================= TANGGAL =================
      $table->date('tanggal_pinjam');
      $table->date('tanggal_rencana_kembali');
      $table->date('tanggal_kembali')->nullable();

      // ================= STATUS =================
      $table->enum('status', ['pending','dipinjam', 'dikembalikan', 'ditolak'])->default('pending');

      // ================= KETERANGAN =================
      $table->string('keperluan', 100)->nullable(); // ✅ FIX
      $table->string('catatan', 100)->nullable(); // ✅ FIX

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('peminjamans');
  }
};

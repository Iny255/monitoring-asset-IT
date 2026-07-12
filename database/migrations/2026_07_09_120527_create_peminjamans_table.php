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

      // Nomor transaksi
      $table->string('kode_peminjaman')->unique();

      // Inventaris yang dipinjam
      $table
        ->foreignId('inventaris_id')
        ->constrained('inventaris')
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      // Jenis peminjaman
      $table->enum('jenis_peminjaman', ['internal', 'antar_perusahaan']);

      // Digunakan jika jenis = internal
      $table
        ->foreignId('karyawan_id')
        ->nullable()
        ->constrained('karyawans')
        ->nullOnDelete();

      // Digunakan jika jenis = antar_perusahaan
      $table
        ->foreignId('perusahaan_tujuan_id')
        ->nullable()
        ->constrained('perusahaans')
        ->nullOnDelete();

      // Penanggung jawab di perusahaan tujuan (opsional)
      $table
        ->foreignId('karyawan_tujuan_id')
        ->nullable()
        ->constrained('karyawans')
        ->nullOnDelete();

      // Petugas yang melakukan input
      $table
        ->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      // Tanggal
      $table->date('tanggal_pinjam');

      $table->date('tanggal_rencana_kembali');

      $table->date('tanggal_kembali')->nullable();

      // Keperluan peminjaman
      $table->text('keperluan');

      // Snapshot kondisi saat dipinjam
      $table->string('kondisi_pinjam')->nullable();

      // Kondisi saat dikembalikan
      $table->string('kondisi_kembali')->nullable();

      // Keterangan saat pengembalian
      $table->text('keterangan_kembali')->nullable();

      // Status transaksi
      $table->enum('status', ['Dipinjam', 'Dikembalikan', 'Hilang'])->default('Dipinjam');

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

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
    Schema::create('maintenances', function (Blueprint $table) {
      $table->id();

      // Nomor transaksi
      $table->string('kode_service')->unique();

      // Inventaris yang diservis
      $table
        ->foreignId('inventaris_id')
        ->constrained('inventaris')
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      // Informasi Service
      $table->date('tanggal');

      $table->enum('jenis', ['Service', 'Maintenance']);

      $table->enum('kategori', ['Hardware', 'Software', 'Cleaning', 'Jaringan', 'Upgrade', 'Lainnya'])->nullable();

      $table
        ->enum('status', [
          'Pengajuan',
          'Diproses',
          'Selesai',
          'Tidak Dapat Diperbaiki',
          'Dibatalkan',
        ])
        ->default('Pengajuan');

      // Detail pekerjaan
      $table->text('keluhan')->nullable();
      $table->text('diagnosa')->nullable();
      $table->text('tindakan')->nullable();

      // Vendor / Teknisi
      $table->string('vendor')->nullable();

      // Biaya
      $table->decimal('biaya', 15, 2)->default(0);

      // Tanggal selesai
      $table->date('tanggal_selesai')->nullable();

      // Catatan tambahan
      $table->text('catatan')->nullable();

      // User yang membuat transaksi
      $table
        ->foreignId('created_by')
        ->constrained('users')
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('maintenances');
  }
};

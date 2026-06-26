<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('mutasi_mapings', function (Blueprint $table) {
      $table->id();

      $table
        ->foreignId('id_maping')
        ->constrained('mapings')
        ->cascadeOnDelete();

      $table
        ->foreignId('id_perusahaan')
        ->constrained('perusahaans')
        ->cascadeOnDelete();

      // lokasi
      $table
        ->foreignId('dari_lokasi')
        ->nullable()
        ->constrained('lokasis')
        ->nullOnDelete();

      $table
        ->foreignId('ke_lokasi')
        ->nullable()
        ->constrained('lokasis')
        ->nullOnDelete();

      // user aset
      $table
        ->foreignId('dari_karyawan')
        ->nullable()
        ->constrained('karyawans')
        ->nullOnDelete();

      $table
        ->foreignId('ke_karyawan')
        ->nullable()
        ->constrained('karyawans')
        ->nullOnDelete();

      // akses
      $table->text('dari_aplikasi')->nullable();
      $table->text('ke_aplikasi')->nullable();

      $table->text('dari_data_ppn')->nullable();
      $table->text('ke_data_ppn')->nullable();

      $table->text('dari_data_non_ppn')->nullable();
      $table->text('ke_data_non_ppn')->nullable();

      $table->date('tanggal_mutasi');

      $table->text('keterangan')->nullable();

      $table
        ->foreignId('created_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('mutasi_mapings');
  }
};

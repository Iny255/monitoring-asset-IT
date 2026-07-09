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
    Schema::create('keluars', function (Blueprint $table) {
      $table->id();

      $table
        ->foreignId('inventaris_id')
        ->constrained('inventaris')
        ->cascadeOnDelete();

      $table
        ->foreignId('perusahaan_id')
        ->constrained('perusahaans')
        ->cascadeOnDelete();

      $table
        ->foreignId('karyawan_id')
        ->nullable()
        ->constrained('karyawans')
        ->nullOnDelete();
      $table
        ->foreignId('created_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

      $table->date('tgl_keluar');

      $table->enum('jenis_penerima', ['Perorangan', 'Perdivisi']);

      $table->string('divisi_klr')->nullable();

      $table->string('perusahaan_klr')->nullable();

      $table->string('gambar')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('keluars');
  }
};

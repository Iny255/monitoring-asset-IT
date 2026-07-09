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
    Schema::create('history_mutasis', function (Blueprint $table) {
      $table->id();

      /*
            |--------------------------------------------------------------------------
            | Mapping
            |--------------------------------------------------------------------------
            */

      $table
        ->foreignId('maping_id')
        ->constrained('mapings')
        ->cascadeOnUpdate()
        ->cascadeOnDelete();

      /*
            |--------------------------------------------------------------------------
            | Jenis Mutasi
            |--------------------------------------------------------------------------
            */

      $table->enum('jenis_mutasi', ['internal', 'antar_perusahaan']);

      /*
            |--------------------------------------------------------------------------
            | Perusahaan
            |--------------------------------------------------------------------------
            */

      $table
        ->foreignId('id_perusahaan_asal')
        ->constrained('perusahaans')
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      $table
        ->foreignId('id_perusahaan_tujuan')
        ->constrained('perusahaans')
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      /*
            |--------------------------------------------------------------------------
            | Snapshot Asset
            |--------------------------------------------------------------------------
            */

      $table->string('kode_aset_lama');
      $table->string('kode_aset_baru');

      $table->string('no_inventaris_lama');
      $table->string('no_inventaris_baru');

      $table->string('nama_aset');

      /*
            |--------------------------------------------------------------------------
            | Lokasi
            |--------------------------------------------------------------------------
            */

      $table->string('lokasi_lama');
      $table->string('lokasi_baru');

      /*
            |--------------------------------------------------------------------------
            | User Asset
            |--------------------------------------------------------------------------
            */

      $table->string('user_lama');
      $table->string('user_baru');

      /*
            |--------------------------------------------------------------------------
            | Mutasi
            |--------------------------------------------------------------------------
            */

      $table->date('tanggal_mutasi');

      $table->text('catatan')->nullable();

      /*
            |--------------------------------------------------------------------------
            | Pelaku
            |--------------------------------------------------------------------------
            */

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
    Schema::dropIfExists('history_mutasis');
  }
};

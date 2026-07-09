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
    Schema::create('history_pencabutans', function (Blueprint $table) {
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
            | Perusahaan
            |--------------------------------------------------------------------------
            */

      $table
        ->foreignId('id_perusahaan')
        ->constrained('perusahaans')
        ->cascadeOnUpdate()
        ->restrictOnDelete();

      /*
            |--------------------------------------------------------------------------
            | Snapshot Asset
            |--------------------------------------------------------------------------
            */

      $table->string('kode_aset');

      $table->string('no_inventaris');

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
            | User
            |--------------------------------------------------------------------------
            */

      $table->string('user_lama');

      /*
            |--------------------------------------------------------------------------
            | Pencabutan
            |--------------------------------------------------------------------------
            */

      $table->date('tanggal_pencabutan');

      $table->text('alasan')->nullable();

      /*
            |--------------------------------------------------------------------------
            | Petugas
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

  public function down(): void
  {
    Schema::dropIfExists('history_pencabutans');
  }
};

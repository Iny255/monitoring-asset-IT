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
    Schema::create('history_hak_akses', function (Blueprint $table) {
      $table->id();

      /*
            |--------------------------------------------------------------------------
            | RELASI
            |--------------------------------------------------------------------------
            */

      $table
        ->foreignId('maping_id')
        ->constrained('mapings')
        ->cascadeOnDelete();

      $table
        ->foreignId('access_id')
        ->constrained('accesses')
        ->cascadeOnDelete();

      $table
        ->foreignId('user_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

      /*
            |--------------------------------------------------------------------------
            | HISTORY
            |--------------------------------------------------------------------------
            */

      $table->enum('aksi', ['tambah', 'hapus', 'update']);

      $table->text('keterangan')->nullable();

      $table->timestamps();

      /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

      $table->index('maping_id');
      $table->index('access_id');
      $table->index('user_id');
      $table->index('aksi');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('history_hak_akses');
  }
};

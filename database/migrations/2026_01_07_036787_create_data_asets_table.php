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
    Schema::create('data_asets', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('perusahaan_id')
        ->constrained('perusahaans')
        ->cascadeOnDelete();

      $table
        ->foreignId('kategori_id')
        ->constrained('kategoris')
        ->cascadeOnDelete();

      $table->string('merek');

      $table->string('type');

      $table->string('warna')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('data_asets');
  }
};

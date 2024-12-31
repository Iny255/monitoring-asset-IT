<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('anthropometris', function (Blueprint $table) {
      $table->id();
      $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
      $table->string('jenis_kelamin',50);
      $table->float('tinggi_badan');
      $table->float('berat_badan');
      $table->integer('usia');

      // $table->integer('bulan');
      // $table->integer('tahun');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('anthropometris');
  }
};
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
    Schema::create('pertumbuhans', function (Blueprint $table) {
      $table->id();
      $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
      $table->foreignId('anthropometri_id')->constrained('anthropometris')->onDelete('cascade');

      $table->integer('bulan');
      $table->integer('tahun');
      $table->float('tinggi_badan');
      $table->float('berat_badan');
      $table->string('cara_ukur',50);

      //added 9/18/2024
      $table->string('status_stunting',50);
      $table->string('status_gizi',50);
      $table->integer('usia');
      $table->string('z_score',50);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pertumbuhans');
  }
};
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
    Schema::table('pencabutans', function (Blueprint $table) {
      $table->unsignedBigInteger('id_lokasi')->nullable();
      $table->unsignedBigInteger('id_perusahaan')->nullable();
      $table->unsignedBigInteger('id_karyawan')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('pencabutans', function (Blueprint $table) {
      //
    });
  }
};

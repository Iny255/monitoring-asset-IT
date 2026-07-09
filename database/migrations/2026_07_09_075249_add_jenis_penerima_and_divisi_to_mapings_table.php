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
    Schema::table('mapings', function (Blueprint $table) {
      $table
        ->enum('jenis_penerima', ['Perorangan', 'Perdivisi'])
        ->default('Perorangan')
        ->after('karyawan_id');

      $table
        ->string('divisi', 100)
        ->nullable()
        ->after('jenis_penerima');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('mapings', function (Blueprint $table) {
      $table->dropColumn(['jenis_penerima', 'divisi']);
    });
  }
};

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
    Schema::table('keluars', function (Blueprint $table) {
     $table->date('tgl_keluar')
      ->nullable()
      ->after('jumlah');
    });
  }

  public function down()
  {
    Schema::table('keluars', function (Blueprint $table) {
      $table->dropColumn('tgl_keluar');
    });
  }
};

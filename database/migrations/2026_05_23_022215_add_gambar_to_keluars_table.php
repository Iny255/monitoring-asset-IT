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
      $table
        ->string('gambar')
        ->nullable()
        ->after('no_inventaris');
    });
  }

  public function down(): void
  {
    Schema::table('keluars', function (Blueprint $table) {
      $table->dropColumn('gambar');
    });
  }
};

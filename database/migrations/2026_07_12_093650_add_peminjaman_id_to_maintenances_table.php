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
    Schema::table('maintenances', function (Blueprint $table) {
      $table
        ->foreignId('peminjaman_id')
        ->nullable()
        ->after('inventaris_id')
        ->constrained('peminjamans')
        ->cascadeOnUpdate()
        ->nullOnDelete();
    });
  }

  public function down(): void
  {
    Schema::table('maintenances', function (Blueprint $table) {
      $table->dropForeign(['peminjaman_id']);

      $table->dropColumn('peminjaman_id');
    });
  }
};

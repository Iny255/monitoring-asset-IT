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
    Schema::table('history_pencabutans', function (Blueprint $table) {
      //
      $table
        ->foreignId('inventaris_id')
        ->after('maping_id')
        ->constrained('inventaris')
        ->cascadeOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('history_pencabutans', function (Blueprint $table) {
      //
      $table->dropForeign(['inventaris_id']);
      $table->dropColumn('inventaris_id');
    });
  }
};

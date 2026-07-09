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
    Schema::table('history_mutasis', function (Blueprint $table) {
      $table
        ->foreignId('maping_baru_id')
        ->nullable()
        ->after('maping_id')
        ->constrained('mapings')
        ->nullOnDelete();
    });
  }

  public function down(): void
  {
    Schema::table('history_mutasis', function (Blueprint $table) {
      $table->dropForeign(['maping_baru_id']);
      $table->dropColumn('maping_baru_id');
    });
  }
};

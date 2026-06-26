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
    Schema::table('accesses', function (Blueprint $table) {
      $table->enum('jenis', ['Software', 'PPN', 'NON PPN'])->after('kategori');

      $table->index('jenis');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('accesses', function (Blueprint $table) {
      $table->dropIndex(['jenis']);
      $table->dropColumn('jenis');
    });
  }
};

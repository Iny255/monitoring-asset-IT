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
    Schema::table('inventaris', function (Blueprint $table) {
      $table->renameColumn('is_active', 'is_transfer');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('is_transfer_on_inventaris', function (Blueprint $table) {
      //
    });
  }
};

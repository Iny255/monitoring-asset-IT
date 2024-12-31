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
    Schema::create('lokasis', function (Blueprint $table) {
      $table->id();
      $table->string('nama_posyandu', 20);
      $table->enum('dukuh', [
        'Cendono',
        'Kawakan',
        'Madu',
        'Dawe'
      ])->default('Cendono');
      $table->string('rt', 2);
      $table->string('rw', 2);
      $table->string('username', 50);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('lokasis');
  }
};
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
    Schema::create('maping_accesses', function (Blueprint $table) {
      $table->id();

      $table
        ->foreignId('maping_id')
        ->constrained('mapings')
        ->cascadeOnDelete();

      $table
        ->foreignId('access_id')
        ->constrained('accesses')
        ->cascadeOnDelete();

      $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

      $table->timestamps();

      $table->unique(['maping_id', 'access_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('maping_accesses');
  }
};

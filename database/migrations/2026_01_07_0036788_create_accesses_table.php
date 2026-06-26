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
    Schema::create('accesses', function (Blueprint $table) {
     $table->id();

    $table->enum('kategori', [
        'Aplikasi',
        'Hak Akses'
    ]);

    $table->string('nama_akses', 150);

    $table->enum('status', [
        'aktif',
        'nonaktif'
    ])->default('aktif');

    $table->timestamps();

    // Index
    $table->index('kategori');
    $table->index('status');

    // Tidak boleh ada nama yang sama dalam kategori yang sama
    $table->unique(['kategori', 'nama_akses']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('accesses');
  }
};

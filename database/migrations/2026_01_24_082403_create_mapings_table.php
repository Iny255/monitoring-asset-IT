<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('mapings', function (Blueprint $table) {
            $table->id();

            // ================= RELASI =================
            $table->foreignId('id_lokasi')
                ->constrained('lokasis')
                ->cascadeOnDelete();

            $table->foreignId('id_keluar')
                ->constrained('keluars')
                ->cascadeOnDelete();

            $table->foreignId('id_perusahaan')
                ->constrained('perusahaans')
                ->cascadeOnDelete();

            // ================= SPESIFIKASI =================
            $table->string('processor', 100)->nullable();
            $table->string('device_id', 50)->nullable();
            $table->string('produk_id', 50)->nullable();
            $table->integer('ram')->nullable();
            $table->string('system', 50)->nullable();
            $table->string('version', 20)->nullable(); // 🔥 dari 5 jadi 20 (lebih realistis)
            $table->date('instal_on')->nullable();

            // ================= DATA =================
            $table->string('aplikasi', 100)->nullable();
            $table->string('data_p', 100)->nullable();
            $table->string('data_n', 100)->nullable();

            // 🔥 TAMBAHAN PENTING
            $table->enum('status', ['aktif', 'dicabut'])->default('aktif');

            $table->timestamps();

            // ================= INDEX =================
            $table->index('id_perusahaan');
            $table->index('id_keluar');

            // 🔥 UNIQUE PER PERUSAHAAN (AMAN)
            $table->unique(['device_id', 'id_perusahaan']);
            $table->unique(['produk_id', 'id_perusahaan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapings');
    }
};
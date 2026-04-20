<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('mutasi_mapings', function (Blueprint $table) {
            $table->id();

            // 🔥 RELASI UTAMA
            $table->foreignId('id_maping')
                ->constrained('mapings')
                ->cascadeOnDelete();

            // 🔥 WAJIB MULTI PERUSAHAAN
            $table->foreignId('id_perusahaan')
                ->constrained('perusahaans')
                ->cascadeOnDelete();

            // ================= LOKASI =================
            $table->foreignId('dari_lokasi')
                ->nullable()
                ->constrained('lokasis')
                ->nullOnDelete();

            $table->foreignId('ke_lokasi')
                ->nullable()
                ->constrained('lokasis')
                ->nullOnDelete();

            // ================= PERUSAHAAN =================
            $table->foreignId('dari_perusahaan')
                ->nullable()
                ->constrained('perusahaans')
                ->nullOnDelete();

            $table->foreignId('ke_perusahaan')
                ->nullable()
                ->constrained('perusahaans')
                ->nullOnDelete();

            // ================= KARYAWAN =================
            $table->foreignId('dari_karyawan')
                ->nullable()
                ->constrained('karyawans')
                ->nullOnDelete();

            $table->foreignId('ke_karyawan')
                ->nullable()
                ->constrained('karyawans')
                ->nullOnDelete();

            // ================= DATA =================
            $table->string('dari_no_inventaris')->nullable();
            $table->string('ke_no_inventaris')->nullable();

            $table->string('dari_aplikasi')->nullable();
            $table->string('ke_aplikasi')->nullable();

            $table->string('dari_data_ppn')->nullable();
            $table->string('ke_data_ppn')->nullable();

            $table->string('dari_data_non_ppn')->nullable();
            $table->string('ke_data_non_ppn')->nullable();

            // ================= META =================
            $table->date('tanggal_mutasi');

            // 🔥 OPTIONAL (RECOMMENDED)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // ================= INDEX =================
            $table->index('id_perusahaan');
            $table->index('id_maping');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_mapings');
    }
};
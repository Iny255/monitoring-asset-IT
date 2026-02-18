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
        Schema::create('mutasi_mapings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_maping')->constrained('mapings')->cascadeOnDelete();

            $table->foreignId('dari_lokasi')->nullable()->constrained('lokasis');
            $table->foreignId('ke_lokasi')->nullable()->constrained('lokasis');

            $table->foreignId('dari_perusahaan')->nullable()->constrained('perusahaans');
            $table->foreignId('ke_perusahaan')->nullable()->constrained('perusahaans');

            $table->foreignId('dari_karyawan')->nullable()->constrained('karyawans');
            $table->foreignId('ke_karyawan')->nullable()->constrained('karyawans');

            $table->string('dari_no_inventaris')->nullable();
            $table->string('ke_no_inventaris')->nullable();

            $table->string('dari_aplikasi')->nullable();
            $table->string('ke_aplikasi')->nullable();

            $table->string('dari_data_ppn')->nullable();
            $table->string('ke_data_ppn')->nullable();

            $table->string('dari_data_non_ppn')->nullable();
            $table->string('ke_data_non_ppn')->nullable();

            $table->date('tanggal_mutasi');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_mapings');
    }
};

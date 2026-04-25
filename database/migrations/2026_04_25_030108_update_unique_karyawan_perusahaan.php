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
          Schema::table('karyawans', function (Blueprint $table) {

            // ❌ hapus unique lama (kode_karyawan global)
            $table->dropUnique(['kode_karyawan']);

            // ✅ tambah unique per perusahaan
            $table->unique(['kode_karyawan', 'id_perusahaan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {

            // rollback: hapus composite unique
            $table->dropUnique(['kode_karyawan', 'id_perusahaan']);

            // kembalikan ke unique lama
            $table->unique('kode_karyawan');
        });
    }
};

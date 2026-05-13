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
        Schema::table('kategoris', function (Blueprint $table) {

            // =====================================
            // TAMBAH UNIQUE BARU
            // =====================================

            $table->unique(
                ['perusahaan_id', 'kode_barang'],
                'kategoris_perusahaan_kode_barang_unique'
            );

            $table->unique(
                ['perusahaan_id', 'nama_barang'],
                'kategoris_perusahaan_nama_barang_unique'
            );

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {

            // =====================================
            // DROP UNIQUE
            // =====================================

            $table->dropUnique(
                'kategoris_perusahaan_kode_barang_unique'
            );

            $table->dropUnique(
                'kategoris_perusahaan_nama_barang_unique'
            );

        });
    }
};
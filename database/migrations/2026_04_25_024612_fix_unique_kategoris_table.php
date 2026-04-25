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

            // ❌ drop unique lama (sesuai database kamu)
            $table->dropUnique('kode_perusahaan_unique');

            // (opsional aman kalau foreign key ada dan ingin dirapikan)
            // $table->dropForeign('kategoris_perusahaan_id_foreign');

            // ✅ unique baru: per perusahaan untuk kode_barang
            $table->unique(
                ['perusahaan_id', 'kode_barang'],
                'kategoris_perusahaan_kode_barang_unique'
            );

            // ✅ unique baru: per perusahaan untuk nama_barang
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

            // ❌ drop unique baru
            $table->dropUnique('kategoris_perusahaan_kode_barang_unique');
            $table->dropUnique('kategoris_perusahaan_nama_barang_unique');

            // 🔁 balikin seperti semula
            $table->unique(
                ['kode_barang', 'perusahaan_id'],
                'kode_perusahaan_unique'
            );
        });
    }
};

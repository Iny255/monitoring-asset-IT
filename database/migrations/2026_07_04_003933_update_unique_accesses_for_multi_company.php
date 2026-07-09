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
       Schema::table('accesses', function (Blueprint $table) {

            $table->dropUnique('accesses_kategori_jenis_nama_akses_unique');

            $table->unique(
                ['id_perusahaan', 'kategori', 'jenis', 'nama_akses'],
                'accesses_perusahaan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('accesses', function (Blueprint $table) {

            $table->dropUnique('accesses_perusahaan_unique');

            $table->unique(
                ['kategori', 'jenis', 'nama_akses'],
                'accesses_kategori_jenis_nama_akses_unique'
            );
        });
    }
};

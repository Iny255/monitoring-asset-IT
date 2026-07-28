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
        Schema::table('maping_accesses', function (Blueprint $table) {
            if (!Schema::hasColumn('maping_accesses', 'nama_akses')) {
                $table->string('nama_akses')->nullable()->after('access_id');
            }
            if (!Schema::hasColumn('maping_accesses', 'kategori')) {
                $table->enum('kategori', ['Aplikasi', 'Hak Akses'])->default('Hak Akses')->after('nama_akses');
            }
            if (!Schema::hasColumn('maping_accesses', 'jenis')) {
                $table->enum('jenis', ['Software', 'PPN', 'NON PPN'])->default('NON PPN')->after('kategori');
            }
        });

        Schema::table('history_hak_akses', function (Blueprint $table) {
            if (!Schema::hasColumn('history_hak_akses', 'nama_akses')) {
                $table->string('nama_akses')->nullable()->after('access_id');
            }
            if (!Schema::hasColumn('history_hak_akses', 'kategori')) {
                $table->enum('kategori', ['Aplikasi', 'Hak Akses'])->default('Hak Akses')->after('nama_akses');
            }
            if (!Schema::hasColumn('history_hak_akses', 'jenis')) {
                $table->enum('jenis', ['Software', 'PPN', 'NON PPN'])->default('NON PPN')->after('kategori');
            }
        });

        // Make access_id nullable on maping_accesses and history_hak_akses
        try {
            Schema::table('maping_accesses', function (Blueprint $table) {
                $table->unsignedBigInteger('access_id')->nullable()->change();
            });
            Schema::table('history_hak_akses', function (Blueprint $table) {
                $table->unsignedBigInteger('access_id')->nullable()->change();
            });
        } catch (\Throwable $e) {
            // Ignore if doctrine/dbal not installed or change not supported
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maping_accesses', function (Blueprint $table) {
            if (Schema::hasColumn('maping_accesses', 'nama_akses')) {
                $table->dropColumn(['nama_akses', 'kategori', 'jenis']);
            }
        });

        Schema::table('history_hak_akses', function (Blueprint $table) {
            if (Schema::hasColumn('history_hak_akses', 'nama_akses')) {
                $table->dropColumn(['nama_akses', 'kategori', 'jenis']);
            }
        });
    }
};

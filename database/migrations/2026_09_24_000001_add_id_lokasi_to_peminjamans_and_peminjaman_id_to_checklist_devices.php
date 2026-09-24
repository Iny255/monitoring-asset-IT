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
        Schema::table('peminjamans', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjamans', 'id_lokasi')) {
                $table->foreignId('id_lokasi')
                    ->nullable()
                    ->after('karyawan_tujuan_id')
                    ->constrained('lokasis')
                    ->nullOnDelete();
            }
        });

        Schema::table('checklist_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('checklist_devices', 'peminjaman_id')) {
                $table->foreignId('peminjaman_id')
                    ->nullable()
                    ->after('maping_id')
                    ->constrained('peminjamans')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_devices', function (Blueprint $table) {
            if (Schema::hasColumn('checklist_devices', 'peminjaman_id')) {
                $table->dropForeign(['peminjaman_id']);
                $table->dropColumn('peminjaman_id');
            }
        });

        Schema::table('peminjamans', function (Blueprint $table) {
            if (Schema::hasColumn('peminjamans', 'id_lokasi')) {
                $table->dropForeign(['id_lokasi']);
                $table->dropColumn('id_lokasi');
            }
        });
    }
};

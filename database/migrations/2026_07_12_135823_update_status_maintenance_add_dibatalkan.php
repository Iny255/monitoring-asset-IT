<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE maintenances
            MODIFY status ENUM(
                'Pengajuan',
                'Diproses',
                'Selesai',
                'Tidak Dapat Diperbaiki',
                'Dibatalkan'
            ) NOT NULL DEFAULT 'Pengajuan'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE maintenances
            MODIFY status ENUM(
                'Pengajuan',
                'Diproses',
                'Selesai',
                'Tidak Dapat Diperbaiki'
            ) NOT NULL DEFAULT 'Pengajuan'
        ");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table
                ->enum('status', ['dipinjam', 'dikembalikan'])
                ->default('dipinjam')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table
                ->enum('status', ['pending', 'disetujui', 'dipinjam', 'dikembalikan', 'ditolak'])
                ->default('pending')
                ->change();
        });
    }
};

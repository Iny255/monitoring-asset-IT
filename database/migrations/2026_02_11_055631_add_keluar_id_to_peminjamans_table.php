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
            $table->unsignedBigInteger('keluar_id')->nullable()->after('id');

            // Jika mau relasi foreign key
            $table->foreign('keluar_id')
                  ->references('id')
                  ->on('keluars')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropForeign(['keluar_id']);
            $table->dropColumn('keluar_id');
        });
    }
};

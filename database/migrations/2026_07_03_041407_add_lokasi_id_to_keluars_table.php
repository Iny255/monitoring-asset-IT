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
         Schema::table('keluars', function (Blueprint $table) {

        $table->foreignId('lokasi_id')
              ->nullable()
              ->after('karyawan_id')
              ->constrained('lokasis')
              ->nullOnDelete();

    });
}

public function down()
{
    Schema::table('keluars', function (Blueprint $table) {

        $table->dropForeign(['lokasi_id']);
        $table->dropColumn('lokasi_id');

    });
}
};

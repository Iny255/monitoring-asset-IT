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

            $table->foreignId('id_perusahaan')
                ->nullable()
                ->after('id')
                ->constrained('perusahaans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('accesses', function (Blueprint $table) {

            $table->dropConstrainedForeignId('id_perusahaan');

        });
    }
};

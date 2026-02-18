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
        Schema::create('mapings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lokasi');
            $table->unsignedBigInteger('id_keluar');
            $table->unsignedBigInteger('id_perusahaan');
            $table->string('processor', 100)->nullable();
            $table->string('device_id', 50)->nullable()->unique();
            $table->string('produk_id', 50)->nullable()->unique();
            $table->integer('ram')->nullable();
            $table->string('system', 50)->nullable();
            $table->string('version', 5)->nullable();
            $table->date('instal_on')->nullable();
            $table->string('aplikasi', 100)->nullable();
            $table->string('data_p', 100)->nullable();
            $table->string('data_n', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_lokasi')
                ->references('id')
                ->on('lokasis')
                ->onDelete('cascade');

            $table->foreign('id_keluar')
                ->references('id')
                ->on('keluars')
                ->onDelete('cascade');

            $table->foreign('id_perusahaan')
                ->references('id')
                ->on('perusahaans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapings');
    }
};

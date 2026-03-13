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
        Schema::create('pencabutans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_maping');
            $table->unsignedBigInteger('id_keluar');

            $table->date('tanggal_cabut');
             $table->enum('kondisi', ['Baik', 'Rusak'])->default('Baik');;
            $table->string('alasan')->nullable();

            $table->timestamps();

            $table->foreign('id_maping')
                ->references('id')
                ->on('mapings')
                ->cascadeOnDelete();

            $table->foreign('id_keluar')
                ->references('id')
                ->on('keluars')
                ->cascadeOnDelete();
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencabutans');
    }
};

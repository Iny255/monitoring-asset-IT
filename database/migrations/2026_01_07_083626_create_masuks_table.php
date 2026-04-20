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
        Schema::create('masuks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kategori');
            $table->string('kode_masuk', 20)->unique();
            $table->string('type', 50);
            $table->string('merek', 50);
            $table->integer('jumlah');
            $table->integer('garansi');
            $table->date('tgl_beli');
            $table->string('supplier', 50);
            $table->string('harga', 50);
            $table->string('gambar')->nullable(); // path gambar nota

            $table->timestamps();

            // Foreign key
            $table->foreign('id_kategori')
                ->references('id')
                ->on('kategoris')
                ->onDelete('cascade');
                
            $table->foreignId('perusahaan_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masuks');
    }
};

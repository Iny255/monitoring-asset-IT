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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_karyawan', 7)->unique();
            $table->string('nama_karyawan',100);
            $table->string('jabatan',50);
            $table->string('divisi',50);
            $table->string('perusahaan',50);
            $table->timestamps();

             $table->foreignId('id_perusahaan')
            ->constrained('perusahaans')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};

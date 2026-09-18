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
        Schema::create('checklist_jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jadwal', 50)->unique();
            $table->foreignId('id_perusahaan')->constrained('perusahaans')->cascadeOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasis')->cascadeOnDelete();
            $table->integer('tahun');
            $table->integer('bulan'); // 1-12
            $table->integer('minggu_ke'); // 1-5
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['terjadwal', 'berjalan', 'selesai', 'terlewat'])->default('terjadwal');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_jadwals');
    }
};

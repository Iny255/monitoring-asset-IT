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
        Schema::create('checklist_ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('checklist_jadwals')->cascadeOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasis')->cascadeOnDelete();
            $table->foreignId('id_perusahaan')->constrained('perusahaans')->cascadeOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_cek')->nullable();
            $table->enum('status', ['belum_dicek', 'sedang_dicek', 'selesai'])->default('belum_dicek');
            $table->enum('kondisi_ruangan', ['semua_baik', 'ada_kendala'])->default('semua_baik');
            $table->integer('total_device')->default(0);
            $table->integer('total_checked')->default(0);
            $table->text('catatan_ruangan')->nullable();
            $table->timestamps();
        });

        Schema::create('checklist_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_ruangan_id')->constrained('checklist_ruangans')->cascadeOnDelete();
            $table->foreignId('maping_id')->nullable()->constrained('mapings')->nullOnDelete();
            $table->foreignId('inventaris_id')->constrained('inventaris')->cascadeOnDelete();
            $table->string('nama_pengguna', 150)->nullable();
            $table->enum('status_device', ['belum_dicek', 'normal', 'ada_kendala'])->default('belum_dicek');
            $table->text('catatan_kendala')->nullable();
            $table->foreignId('maintenance_id')->nullable()->constrained('maintenances')->nullOnDelete();
            $table->dateTime('checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('checklist_device_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_device_id')->constrained('checklist_devices')->cascadeOnDelete();
            $table->string('nama_item', 255);
            $table->string('kategori_item', 50)->default('Umum');
            $table->boolean('is_ok')->default(true);
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_device_items');
        Schema::dropIfExists('checklist_devices');
        Schema::dropIfExists('checklist_ruangans');
    }
};

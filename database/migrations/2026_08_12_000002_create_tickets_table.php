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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique(); // e.g. TKT-20260812-0001
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pelapor (User)
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->onDelete('set null');
            $table->foreignId('id_perusahaan')->nullable()->constrained('perusahaans')->onDelete('set null');
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasis')->onDelete('set null');
            $table->foreignId('inventaris_id')->nullable()->constrained('inventaris')->onDelete('set null'); // Aset terkait (opsional)
            $table->foreignId('ticket_category_id')->constrained('ticket_categories')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('prioritas', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed', 'rejected'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Petugas IT Penanggung Jawab
            $table->foreignId('maintenance_id')->nullable()->constrained('maintenances')->onDelete('set null'); // Link ke Maintenance jika dikonversi
            $table->string('lampiran')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

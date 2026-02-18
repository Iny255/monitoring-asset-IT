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
        Schema::create('keluars', function (Blueprint $table) {
            $table->id();
            // Relasi
            $table->foreignId('id_karyawan')->nullable()->constrained('karyawans')->cascadeOnDelete();
            $table->foreignId('id_masuk')->constrained('masuks')->cascadeOnDelete();

            // Data transaksi
            $table->string('kode_keluar', 30)->unique();
            $table->integer('jumlah')->default(1);
            $table->string('keterangan', 100)->nullable();

            // Data barang
            $table->string('kode_barang', 30)->index(); // jangan unique
            $table->string('warna', 50)->nullable();
            $table->string('no_inventaris', 50)->nullable();

            // Jika barang kadang milik divisi, kadang per orang
            $table->enum('jenis_penerima', ['Perorangan', 'Perdivisi'])
                ->default('Perorangan');
            // isi: "divisi" / "karyawan"
              $table->string('divisi_klr', 50)->nullable();
              $table->string('perusahaan_klr', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluars');
    }
};

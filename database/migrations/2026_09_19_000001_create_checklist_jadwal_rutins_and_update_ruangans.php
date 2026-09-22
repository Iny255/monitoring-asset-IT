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
        if (!Schema::hasTable('checklist_jadwal_rutins')) {
            Schema::create('checklist_jadwal_rutins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_perusahaan')->constrained('perusahaans')->cascadeOnDelete();
                $table->foreignId('id_lokasi')->constrained('lokasis')->cascadeOnDelete();
                $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->time('jam_mulai')->default('08:00')->nullable();
                $table->time('jam_selesai')->default('17:00')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('catatan')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['id_perusahaan', 'id_lokasi', 'hari'], 'uniq_rutin_lokasi_hari');
            });
        }

        if (Schema::hasTable('checklist_ruangans')) {
            Schema::table('checklist_ruangans', function (Blueprint $table) {
                if (Schema::hasColumn('checklist_ruangans', 'jadwal_id')) {
                    $table->unsignedBigInteger('jadwal_id')->nullable()->change();
                }

                if (!Schema::hasColumn('checklist_ruangans', 'jadwal_rutin_id')) {
                    $table->foreignId('jadwal_rutin_id')
                        ->nullable()
                        ->after('jadwal_id')
                        ->constrained('checklist_jadwal_rutins')
                        ->nullOnDelete();
                }

                if (!Schema::hasColumn('checklist_ruangans', 'tanggal_pemeriksaan')) {
                    $table->date('tanggal_pemeriksaan')->nullable()->after('jadwal_rutin_id')->index();
                }

                if (!Schema::hasColumn('checklist_ruangans', 'hari')) {
                    $table->string('hari', 20)->nullable()->after('tanggal_pemeriksaan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('checklist_ruangans')) {
            Schema::table('checklist_ruangans', function (Blueprint $table) {
                if (Schema::hasColumn('checklist_ruangans', 'jadwal_rutin_id')) {
                    $table->dropForeign(['jadwal_rutin_id']);
                    $table->dropColumn('jadwal_rutin_id');
                }
                if (Schema::hasColumn('checklist_ruangans', 'tanggal_pemeriksaan')) {
                    $table->dropColumn('tanggal_pemeriksaan');
                }
                if (Schema::hasColumn('checklist_ruangans', 'hari')) {
                    $table->dropColumn('hari');
                }
            });
        }

        Schema::dropIfExists('checklist_jadwal_rutins');
    }
};

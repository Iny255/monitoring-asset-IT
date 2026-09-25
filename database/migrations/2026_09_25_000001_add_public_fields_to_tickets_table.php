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
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('nama_pelapor', 100)->nullable()->after('user_id');
            $table->string('kontak_pelapor', 50)->nullable()->after('nama_pelapor');
            $table->string('email_pelapor', 100)->nullable()->after('kontak_pelapor');
            $table->boolean('is_public')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['nama_pelapor', 'kontak_pelapor', 'email_pelapor', 'is_public']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};

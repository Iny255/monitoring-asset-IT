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
            $table->text('tindakan_perbaikan')->nullable()->after('status');
            $table->text('tindakan_pencegahan')->nullable()->after('tindakan_perbaikan');
            $table->text('verifikasi')->nullable()->after('tindakan_pencegahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['tindakan_perbaikan', 'tindakan_pencegahan', 'verifikasi']);
        });
    }
};

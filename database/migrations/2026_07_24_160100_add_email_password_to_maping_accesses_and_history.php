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
        Schema::table('maping_accesses', function (Blueprint $table) {
            if (!Schema::hasColumn('maping_accesses', 'email')) {
                $table->string('email')->nullable()->after('access_id');
            }
            if (!Schema::hasColumn('maping_accesses', 'password')) {
                $table->text('password')->nullable()->after('email');
            }
        });

        Schema::table('history_hak_akses', function (Blueprint $table) {
            if (!Schema::hasColumn('history_hak_akses', 'email')) {
                $table->string('email')->nullable()->after('access_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maping_accesses', function (Blueprint $table) {
            if (Schema::hasColumn('maping_accesses', 'email')) {
                $table->dropColumn(['email', 'password']);
            }
        });

        Schema::table('history_hak_akses', function (Blueprint $table) {
            if (Schema::hasColumn('history_hak_akses', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};

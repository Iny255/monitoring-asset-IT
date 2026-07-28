<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Maping;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('mapings', 'uuid')) {
            Schema::table('mapings', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id')->unique();
            });

            // Auto-populate existing records with UUID
            try {
                Maping::withoutGlobalScopes()->whereNull('uuid')->chunk(100, function ($mapings) {
                    foreach ($mapings as $maping) {
                        $maping->uuid = (string) Str::uuid();
                        $maping->save();
                    }
                });
            } catch (\Throwable $e) {
                // Fail-safe if table is empty or during early setup
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mapings', 'uuid')) {
            Schema::table('mapings', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};

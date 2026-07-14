<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    DB::statement("
            ALTER TABLE inventaris
            MODIFY status ENUM(
                'TERSEDIA',
                'DIPAKAI',
                'DIPINJAM',
                'RUSAK',
                'AFKIR'
            ) NOT NULL DEFAULT 'TERSEDIA'
        ");
  }

  public function down(): void
  {
    DB::statement("
            ALTER TABLE inventaris
            MODIFY status ENUM(
                'TERSEDIA',
                'DIPAKAI',
                'DIPINJAM',
                'RUSAK'
            ) NOT NULL DEFAULT 'TERSEDIA'
        ");
  }
};

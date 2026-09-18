<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel roles
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50)->unique();
                $table->string('display_name', 100);
                $table->text('description')->nullable();
                $table->boolean('can_manage_settings')->default(false);
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });
        }

        // 2. Buat tabel modules
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('name', 100);
                $table->string('group', 50);
                $table->string('url', 150)->nullable();
                $table->string('route_name', 100)->nullable();
                $table->string('icon', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Buat pivot table role_module
        if (!Schema::hasTable('role_module')) {
            Schema::create('role_module', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['role_id', 'module_id']);
            });
        }

        // 4. Ubah kolom users.role dari ENUM menjadi VARCHAR(50) agar dinamis
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(50) NOT NULL DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_module');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('roles');

        DB::statement("ALTER TABLE users MODIFY role ENUM('user','karyawan','petugas','super_admin') DEFAULT 'user'");
    }
};

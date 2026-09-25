<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('roles')) {
            // 1. Tambah role teknisi
            DB::table('roles')->updateOrInsert(
                ['name' => 'teknisi'],
                [
                    'display_name' => 'Teknisi IT',
                    'description' => 'Petugas / Teknisi IT penanganan tiket kendala dan service maintenance aset.',
                    'can_manage_settings' => false,
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // 2. Assign modul default untuk teknisi jika tabel modules & role_module tersedia
            if (Schema::hasTable('modules') && Schema::hasTable('role_module')) {
                $teknisiRole = DB::table('roles')->where('name', 'teknisi')->first();
                if ($teknisiRole) {
                    $teknisiModuleCodes = [
                        'dashboard_petugas',
                        'ticket_list',
                        'trx_maintenance',
                        'checklist_pelaksanaan',
                        'history_perjalanan',
                        'trx_peminjaman',
                        'trx_mapping',
                    ];

                    $modules = DB::table('modules')->whereIn('code', $teknisiModuleCodes)->get();
                    foreach ($modules as $m) {
                        DB::table('role_module')->updateOrInsert(
                            [
                                'role_id' => $teknisiRole->id,
                                'module_id' => $m->id,
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('roles')) {
            $teknisiRole = DB::table('roles')->where('name', 'teknisi')->first();
            if ($teknisiRole) {
                if (Schema::hasTable('role_module')) {
                    DB::table('role_module')->where('role_id', $teknisiRole->id)->delete();
                }
                DB::table('roles')->where('id', $teknisiRole->id)->delete();
            }
        }
    }
};

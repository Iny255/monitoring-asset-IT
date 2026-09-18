<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAndModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. SEED DEFAULT ROLES
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Akses penuh seluruh sistem, modul, dan manajemen role/pengaturan.',
                'can_manage_settings' => true,
                'is_system' => true,
            ],
            [
                'name' => 'petugas',
                'display_name' => 'Petugas IT Support',
                'description' => 'Petugas operasional aset, checklist device, inventaris, dan helpdesk ticket.',
                'can_manage_settings' => false,
                'is_system' => true,
            ],
            [
                'name' => 'user',
                'display_name' => 'User / Karyawan (Pelapor Tiket)',
                'description' => 'Pengguna umum pelapor tiket helpdesk dan melihat inventaris aset sendiri.',
                'can_manage_settings' => false,
                'is_system' => true,
            ],
            [
                'name' => 'karyawan',
                'display_name' => 'Karyawan',
                'description' => 'Karyawan pemegang aset dan pelapor tiket helpdesk.',
                'can_manage_settings' => false,
                'is_system' => true,
            ],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(
                ['name' => $r['name']],
                [
                    'display_name' => $r['display_name'],
                    'description' => $r['description'],
                    'can_manage_settings' => $r['can_manage_settings'],
                    'is_system' => $r['is_system'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // 2. SEED MODULES
        $modules = [
            // Dashboard
            [
                'code' => 'dashboard_superadmin',
                'name' => 'Dashboard Super Admin',
                'group' => 'Dashboard',
                'url' => 'dashboard/superadmin',
                'icon' => 'bx bx-home-circle',
                'order' => 1,
            ],
            [
                'code' => 'dashboard_petugas',
                'name' => 'Dashboard Petugas',
                'group' => 'Dashboard',
                'url' => 'dashboard/petugas',
                'icon' => 'bx bx-home-circle',
                'order' => 2,
            ],
            [
                'code' => 'dashboard_aset_saya',
                'name' => 'Aset Saya',
                'group' => 'Dashboard',
                'url' => 'dashboard/aset-saya',
                'icon' => 'bx bx-laptop',
                'order' => 3,
            ],

            // User Management
            [
                'code' => 'master_users',
                'name' => 'Data Users',
                'group' => 'Manajemen User',
                'url' => 'dashboard/user',
                'icon' => 'bx bx-user',
                'order' => 4,
            ],

            // Master Data
            [
                'code' => 'master_kategori',
                'name' => 'Kategori Barang',
                'group' => 'Master Data',
                'url' => 'dashboard/aset',
                'icon' => 'bx bx-purchase-tag',
                'order' => 5,
            ],
            [
                'code' => 'master_data_aset',
                'name' => 'Master Merek & Type',
                'group' => 'Master Data',
                'url' => 'dashboard/data-aset',
                'icon' => 'bx bx-package',
                'order' => 6,
            ],
            [
                'code' => 'master_karyawan',
                'name' => 'Data Karyawan',
                'group' => 'Master Data',
                'url' => 'dashboard/useraset',
                'icon' => 'bx bx-group',
                'order' => 7,
            ],
            [
                'code' => 'master_lokasi',
                'name' => 'Lokasi Penempatan',
                'group' => 'Master Data',
                'url' => 'dashboard/lokasi',
                'icon' => 'bx bx-map-pin',
                'order' => 8,
            ],
            [
                'code' => 'master_supplier',
                'name' => 'Supplier / Vendor',
                'group' => 'Master Data',
                'url' => 'dashboard/supplier',
                'icon' => 'bx bx-store-alt',
                'order' => 9,
            ],
            [
                'code' => 'master_perusahaan',
                'name' => 'Perusahaan',
                'group' => 'Master Data',
                'url' => 'dashboard/perusahaan',
                'icon' => 'bx bx-building',
                'order' => 10,
            ],

            // Transaksi Aset
            [
                'code' => 'trx_masuk',
                'name' => 'Penerimaan Aset',
                'group' => 'Transaksi Aset',
                'url' => 'dashboard/transaksi-masuk',
                'icon' => 'bx bx-log-in-circle',
                'order' => 11,
            ],
            [
                'code' => 'trx_mapping',
                'name' => 'Mapping Aset',
                'group' => 'Transaksi Aset',
                'url' => 'dashboard/maping',
                'icon' => 'bx bx-git-repo-forked',
                'order' => 12,
            ],
            [
                'code' => 'trx_peminjaman',
                'name' => 'Peminjaman Aset',
                'group' => 'Transaksi Aset',
                'url' => 'dashboard/peminjaman',
                'icon' => 'bx bx-clipboard',
                'order' => 13,
            ],
            [
                'code' => 'trx_maintenance',
                'name' => 'Service & Maintenance',
                'group' => 'Transaksi Aset',
                'url' => '/dashboard/maintenance',
                'icon' => 'bx bx-wrench',
                'order' => 14,
            ],

            // Checklist Device
            [
                'code' => 'checklist_jadwal',
                'name' => 'Jadwal Mingguan',
                'group' => 'Checklist Device',
                'url' => 'dashboard/checklist/jadwal',
                'icon' => 'bx bx-calendar',
                'order' => 15,
            ],
            [
                'code' => 'checklist_pelaksanaan',
                'name' => 'Pelaksanaan Checklist',
                'group' => 'Checklist Device',
                'url' => 'dashboard/checklist/pemeriksaan',
                'icon' => 'bx bx-check-square',
                'order' => 16,
            ],
            [
                'code' => 'checklist_item',
                'name' => 'Master Item Cek',
                'group' => 'Checklist Device',
                'url' => 'dashboard/checklist/item',
                'icon' => 'bx bx-list-check',
                'order' => 17,
            ],

            // History Tracking
            [
                'code' => 'history_perjalanan',
                'name' => 'History Tracking Device',
                'group' => 'History Tracking',
                'url' => 'dashboard/history/perjalanan-aset',
                'icon' => 'bx bx-history',
                'order' => 18,
            ],

            // Helpdesk E-Ticket
            [
                'code' => 'ticket_list',
                'name' => 'Daftar Tiket',
                'group' => 'Helpdesk E-Ticket',
                'url' => 'dashboard/e-ticket',
                'icon' => 'bx bx-receipt',
                'order' => 19,
            ],
            [
                'code' => 'ticket_category',
                'name' => 'Kategori & SLA Tiket',
                'group' => 'Helpdesk E-Ticket',
                'url' => 'dashboard/ticket-categories',
                'icon' => 'bx bx-slider-alt',
                'order' => 20,
            ],

            // Pengaturan Sistem
            [
                'code' => 'setting_modules',
                'name' => 'Setting Modul',
                'group' => 'Pengaturan Sistem',
                'url' => 'dashboard/settings/modules',
                'icon' => 'bx bx-slider',
                'order' => 21,
            ],
            [
                'code' => 'setting_roles',
                'name' => 'Manajemen Role',
                'group' => 'Pengaturan Sistem',
                'url' => 'dashboard/settings/roles',
                'icon' => 'bx bx-shield-quarter',
                'order' => 22,
            ],
        ];

        foreach ($modules as $m) {
            DB::table('modules')->updateOrInsert(
                ['code' => $m['code']],
                [
                    'name' => $m['name'],
                    'group' => $m['group'],
                    'url' => $m['url'],
                    'icon' => $m['icon'],
                    'is_active' => true,
                    'order' => $m['order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // 3. SEED ROLE MODULE PERMISSIONS
        $dbRoles = DB::table('roles')->get()->keyBy('name');
        $dbModules = DB::table('modules')->get()->keyBy('code');

        // Helper untuk assign modules
        $assign = function ($roleName, $moduleCodes) use ($dbRoles, $dbModules) {
            if (!isset($dbRoles[$roleName])) return;
            $roleId = $dbRoles[$roleName]->id;

            foreach ($moduleCodes as $code) {
                if (isset($dbModules[$code])) {
                    DB::table('role_module')->updateOrInsert(
                        [
                            'role_id' => $roleId,
                            'module_id' => $dbModules[$code]->id,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        };

        // Super Admin: Semua Modul
        $allModuleCodes = array_column($modules, 'code');
        $assign('super_admin', $allModuleCodes);

        // Petugas: Operasional, master, transaksi, checklist, history, e-ticket
        $petugasModuleCodes = [
            'dashboard_petugas',
            'master_kategori',
            'master_data_aset',
            'master_karyawan',
            'master_lokasi',
            'master_supplier',
            'trx_masuk',
            'trx_mapping',
            'trx_peminjaman',
            'trx_maintenance',
            'checklist_jadwal',
            'checklist_pelaksanaan',
            'checklist_item',
            'history_perjalanan',
            'ticket_list',
            'ticket_category',
        ];
        $assign('petugas', $petugasModuleCodes);

        // User & Karyawan
        $userModuleCodes = [
            'dashboard_aset_saya',
            'ticket_list',
        ];
        $assign('user', $userModuleCodes);
        $assign('karyawan', $userModuleCodes);
    }
}

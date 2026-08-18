<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketCategory;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Hardware & Perangkat Keras',
                'deskripsi' => 'Kerusakan atau masalah pada PC, Laptop, Monitor, Keyboard, Mouse, Printer, Scanner, dll.',
                'sla_jam' => 12,
            ],
            [
                'nama_kategori' => 'Software & Aplikasi System',
                'deskripsi' => 'Kendala aplikasi kantor, Operating System, Email client, MS Office, Antivirus, ERP.',
                'sla_jam' => 8,
            ],
            [
                'nama_kategori' => 'Jaringan & Internet / Wi-Fi',
                'deskripsi' => 'Koneksi internet lambat/putus, Wi-Fi kantor, LAN, VPN, IP Address conflict.',
                'sla_jam' => 4,
            ],
            [
                'nama_kategori' => 'Hak Akses & Akun User',
                'deskripsi' => 'Permintaan reset password, pembuat akun baru, akses folder sharing, hak akses server.',
                'sla_jam' => 6,
            ],
            [
                'nama_kategori' => 'Lain-lain / General IT Support',
                'deskripsi' => 'Pertanyaan umum IT, konsultasi teknis, atau kendala fasilitas pendukung IT lainnya.',
                'sla_jam' => 24,
            ],
        ];

        foreach ($categories as $cat) {
            TicketCategory::firstOrCreate(['nama_kategori' => $cat['nama_kategori']], $cat);
        }
    }
}

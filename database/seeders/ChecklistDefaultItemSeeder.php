<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChecklistItem;

class ChecklistDefaultItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'kategori' => 'Kebersihan & Fisik',
                'nama_item' => 'Kebersihan fisik & sirkulasi udara (Casing & Fan bersih dari debu)',
                'keterangan' => 'Pastikan ventilasi tidak tertutup debu tebal dan sirkulasi lancar',
                'urutan' => 1,
            ],
            [
                'kategori' => 'Kelistrikan',
                'nama_item' => 'Kondisi kabel power, adaptor, dan kelistrikan aman',
                'keterangan' => 'Tidak ada kabel terkelupas, longgar, atau steker terbakar',
                'urutan' => 2,
            ],
            [
                'kategori' => 'Performa & OS',
                'nama_item' => 'Booting lancar, respon sistem normal (tidak hang / lag)',
                'keterangan' => 'Komputer menyala dengan normal dan masuk ke desktop tanpa error',
                'urutan' => 3,
            ],
            [
                'kategori' => 'Performa & OS',
                'nama_item' => 'Suhu perangkat normal (tidak overheat)',
                'keterangan' => 'Kipas bekerja baik dan tidak terdengar bunyi decitan abnormal',
                'urutan' => 4,
            ],
            [
                'kategori' => 'Keamanan',
                'nama_item' => 'Antivirus aktif & database virus ter-update',
                'keterangan' => 'Windows Defender / Antivirus terpasang dalam keadaan aktif & update',
                'urutan' => 5,
            ],
            [
                'kategori' => 'Penyimpanan',
                'nama_item' => 'Kapasitas harddisk / SSD aman (free space > 15%)',
                'keterangan' => 'Drive C: tidak penuh (tidak merah) agar OS dapat bekerja optimal',
                'urutan' => 6,
            ],
            [
                'kategori' => 'Konektivitas',
                'nama_item' => 'Koneksi jaringan (LAN / Wi-Fi) stabil & lancar',
                'keterangan' => 'Dapat mengakses jaringan kantor dan internet tanpa kendala',
                'urutan' => 7,
            ],
            [
                'kategori' => 'Hardware & Port',
                'nama_item' => 'Fungsi periferal & port I/O (Keyboard, Mouse, USB, Display) normal',
                'keterangan' => 'Seluruh perangkat input dan monitor berfungsi dengan baik',
                'urutan' => 8,
            ],
        ];

        foreach ($items as $item) {
            ChecklistItem::firstOrCreate(
                ['nama_item' => $item['nama_item']],
                $item
            );
        }
    }
}

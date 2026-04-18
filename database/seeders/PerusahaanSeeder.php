<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perusahaan;
class PerusahaanSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $data = ['PT Sembilan Matahari Sakti', 'PT Padma Jarka Abadi', 'PT Semesta Mataram Sakti', 'PT Sumber Santoso Abadi'];

    $no = 1;

    foreach ($data as $nama) {
      Perusahaan::create([
        'kode_perusahaan' => 'PT' . str_pad($no++, 4, '0', STR_PAD_LEFT),
        'nama_perusahaan' => $nama,
      ]);
    }
  }
}

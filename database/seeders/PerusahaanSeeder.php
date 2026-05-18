<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perusahaan;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            // =====================================
            // PT SEMBILAN MATAHARI SAKTI
            // =====================================
            [
                'kode_perusahaan' => 'PT0001',

                'nama_perusahaan' => 'PT SEMBILAN MATAHARI SAKTI',

                // Tema biru
                'primary_color' => '#0d3b66',

                'secondary_color' => '#1d5fa3',

                // Logo
                'logo' => 'assets/img/logo_sembilan.png',
            ],


            // =====================================
            // PT PADMA JARKA ABADI
            // =====================================
            [
                'kode_perusahaan' => 'PT0002',

                'nama_perusahaan' => 'PT PADMA JARKA ABADI',

                // Tema merah maroon + gold
                'primary_color' => '#8B0000',

                'secondary_color' => '#B8860B',

                // Logo
                'logo' => 'assets/img/logopadma.png',
            ],


            // =====================================
            // PT SEMESTA MATARAM SAKTI
            // =====================================
            [
                'kode_perusahaan' => 'PT0003',

                'nama_perusahaan' => 'PT SEMESTA MATARAM SAKTI',

                // Tema merah modern
                'primary_color' => '#C1121F',

                'secondary_color' => '#FF3B3F',

                // Logo
                'logo' => 'assets/img/logosms.png',
            ],


            // =====================================
            // PT SUMBER SANTOSO ABADI
            // =====================================
            [
                'kode_perusahaan' => 'PT0004',

                'nama_perusahaan' => 'PT SUMBER SANTOSO ABADI',

                // Tema abu + merah
                'primary_color' => '#5A5A5A',

                'secondary_color' => '#D90429',

                // Logo
                'logo' => 'assets/img/logossa.png',
            ],

        ];


        // =====================================
        // INSERT / UPDATE
        // =====================================
        foreach ($data as $item) {

            Perusahaan::updateOrCreate(

                [
                    'kode_perusahaan' => $item['kode_perusahaan']
                ],

                [
                    'nama_perusahaan' => $item['nama_perusahaan'],

                    'primary_color' => $item['primary_color'],

                    'secondary_color' => $item['secondary_color'],

                    'logo' => $item['logo'],
                ]

            );
        }
    }
}
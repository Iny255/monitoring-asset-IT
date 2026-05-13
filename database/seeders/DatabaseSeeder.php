<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // =====================================
        // PERUSAHAAN
        // =====================================
        $this->call([
            PerusahaanSeeder::class,
        ]);


        // =====================================
        // USER
        // =====================================
        $this->call([
            UserSeeder::class,
        ]);


      

    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'username' => 'petugas',
            'email' => 'nurulinayah255@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'petugas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
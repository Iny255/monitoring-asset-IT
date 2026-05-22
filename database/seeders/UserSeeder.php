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
    DB::table('users')->delete(); // Hapus semua data lama untuk menghindari duplikasi

    DB::table('users')->insert([
      [
      
        'username' => 'sempad.sakti09',
        'name' => 'Super Admin',
        'email' => 'superadmin@gmail.com',
        'password' => Hash::make('panas.sun9'),

        'role' => 'super_admin',
        'id_perusahaan' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}

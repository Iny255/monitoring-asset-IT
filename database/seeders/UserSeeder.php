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
    DB::table('users')->truncate(); // 🔥 biar tidak double saat seeding ulang

    DB::table('users')->insert([
      [
        'username' => 'manager.9',
        'name' => 'Manager',
        'email' => 'manager9@gmail.com',
        'password' => Hash::make('manager'),
        'role' => 'manager',
        'id_perusahaan' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'username' => 'petugas.9',
        'name' => 'Staff IT ',
        'email' => 'petugas1@gmail.com',
        'password' => Hash::make('petugas'),
        'role' => 'petugas',
        'id_perusahaan' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'username' => 'manager.45',
        'name' => 'Manager',
        'email' => 'manager45@gmail.com',
        'password' => Hash::make('manager'),
        'role' => 'manager',
        'id_perusahaan' => 2,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'username' => 'petugas.45',
        'name' => 'Staff IT',
        'email' => 'petugas45@gmail.com',
        'password' => Hash::make('petugas'),
        'role' => 'petugas',
        'id_perusahaan' => 2,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'username' => 'superadmin',
        'name' => 'Super Admin',
        'email' => 'superadmin@gmail.com',
        'password' => Hash::make('admin'),
        'role' => 'super_admin',
        'id_perusahaan' => null, // 🔥 penting!
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}

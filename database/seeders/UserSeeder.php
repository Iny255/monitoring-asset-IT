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
        'username' => 'manager.9',
        'name' => 'Manager PT Sembilan',
        'email' => 'manager9@gmail.com',
        'password' => Hash::make('manager'),
        'role' => 'manager',
        'id_perusahaan' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'username' => 'petugas.9',
        'name' => 'Staff IT PT Sembilan',
        'email' => 'petugas1@gmail.com',
        'password' => Hash::make('petugas'),
        'role' => 'petugas',
        'id_perusahaan' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'username' => 'manager.45',
        'name' => 'Manager PT Padma',
        'email' => 'manager45@gmail.com',
        'password' => Hash::make('manager'),
        'role' => 'manager',
        'id_perusahaan' => 2,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'username' => 'petugas.45',
        'name' => 'Staff IT PT Padma',
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
        'id_perusahaan' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}

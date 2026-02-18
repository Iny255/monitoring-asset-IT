<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Keluar;

class Karyawan extends Model
{
    use HasFactory;
    protected $fillable = [
    'kode_karyawan',
    'nama_karyawan',
    'jabatan',
    'divisi',
    'perusahaan',
  ];

  public function keluar()
{
    return $this->hasMany(Keluar::class, 'id_karyawan');
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Keluar;
use App\Models\Perusahaan;

class Karyawan extends Model
{
  use HasFactory;

  protected $fillable = [
    'kode_karyawan',
    'nama_karyawan',
    'jabatan',
    'divisi',
    'id_perusahaan', // ✅ WAJIB
  ];

  // ================= RELASI =================

  public function keluar()
  {
    return $this->hasMany(Keluar::class, 'id_karyawan');
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
  }
}

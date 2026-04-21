<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;
use App\Models\Masuk;
use App\Models\Perusahaan;
use App\Models\Maping;

class Keluar extends Model
{
  use HasFactory;

  protected $fillable = [
    'id_karyawan',
    'id_masuk',
    'id_perusahaan', // ✅ WAJIB
    'kode_keluar',
    'kode_barang',
    'jumlah',
    'keterangan',
    'warna',
    'no_inventaris',
    'jenis_penerima',
    'divisi_klr',
    'perusahaan_klr',
  ];

  // ================= RELASI =================

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'id_karyawan');
  }

  public function masuk()
{
    return $this->belongsTo(Masuk::class, 'id_masuk');
      
}

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
  }

  public function maping()
  {
    return $this->hasMany(Maping::class, 'id_keluar');
  }
}

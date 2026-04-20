<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Masuk;
use App\Models\Perusahaan;

class Kategori extends Model
{
  use HasFactory;

  protected $fillable = [
    'nama_barang',
    'kode_barang',
    'perusahaan_id' // 🔥 WAJIB TAMBAH INI
  ];

  // 🔥 RELASI KE PERUSAHAAN
  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
  }

  // RELASI KE MASUK
  public function masuks()
  {
    return $this->hasMany(Masuk::class, 'id_kategori');
  }
}

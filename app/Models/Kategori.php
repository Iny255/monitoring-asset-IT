<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Masuk;
use App\Models\Perusahaan;
use App\Models\DataAset;

class Kategori extends Model
{
  protected $table = 'kategoris';

  protected $fillable = [
    'nama_barang',
    'kode_barang',
    'perusahaan_id', // 🔥 WAJIB TAMBAH INI
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
  public function dataAsets()
  {
    return $this->hasMany(DataAset::class, 'kategori_id');
  }
}

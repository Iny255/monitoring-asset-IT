<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Masuk;

class Kategori extends Model
{
     use HasFactory;
  protected $fillable = [
    'nama_barang',
    'kode_barang',
  ];

  public function masuks()
{
    return $this->hasMany(Masuk::class, 'id_kategori');
}
}

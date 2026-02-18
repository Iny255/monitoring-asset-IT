<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;
     protected $fillable = [
    'nama_lokasi',
    'kode_lokasi',
  ];

   public function maping()
{
    return $this->hasMany(Maping::class, 'id_lokasi');
}
}

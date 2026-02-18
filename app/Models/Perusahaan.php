<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;
     protected $fillable = [
    'nama_perusahaan',
    'kode_perusahaan',
    
  ];

   public function maping()
{
    return $this->hasMany(Maping::class, 'id_perusahaan');
}
}

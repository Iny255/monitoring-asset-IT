<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAset extends Model
{
  protected $fillable = [
    'perusahaan_id',
    'kategori_id',
    'supplier_id',
    'merek',
    'type',
    'warna',

  ];

  public function kategori()
  {
    return $this->belongsTo(Kategori::class);
  }


  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class);
  }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maping extends Model
{
  use HasFactory;
  protected $fillable = [
    'id_lokasi',
    'id_keluar',
    'id_perusahaan',
    'processor',
    'device_id',
    'produk_id',
    'ram',
    'system',
    'version',
    'instal_on',
    'aplikasi',
    'data_p',
    'data_n',
    'status',
  ];
  public function lokasi()
  {
    return $this->belongsTo(Lokasi::class, 'id_lokasi');
  }
  public function keluar()
  {
    return $this->belongsTo(Keluar::class, 'id_keluar');
  }
  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
  }
  public function mutasiMapings()
  {
    return $this->hasMany(MutasiMaping::class, 'id_maping');
  }
}

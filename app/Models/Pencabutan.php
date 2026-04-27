<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pencabutan extends Model
{
  use HasFactory;
  protected $fillable = [
    'id_maping',
    'id_keluar',
    'id_lokasi',
    'id_perusahaan',
    'id_karyawan',
    'tanggal_cabut',
    'kondisi',
    'alasan',
  ];
  public function maping()
  {
    return $this->belongsTo(Maping::class);
  }

  public function keluar()
  {
    return $this->belongsTo(Keluar::class, 'id_keluar');
  }
  public function lokasi()
  {
    return $this->belongsTo(Lokasi::class, 'id_lokasi');
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
  }

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'id_karyawan');
  }
  protected static function booted()
  {
    static::creating(function ($model) {
      if (auth()->check() && auth()->user()->role != 'super_admin') {
        $model->id_perusahaan = auth()->user()->id_perusahaan;
      }
    });

    static::addGlobalScope('perusahaan', function ($query) {
      if (auth()->check() && auth()->user()->role != 'super_admin') {
        $query->where('id_perusahaan', auth()->user()->id_perusahaan);
      }
    });
  }
}

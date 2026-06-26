<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lokasi;
use App\Models\Keluar;
use App\Models\Perusahaan;
use App\Models\MutasiMaping;

class Maping extends Model
{
  use HasFactory;

  protected $fillable = [
    'id_keluar',
    'id_lokasi',
    'id_perusahaan',

    'processor',
    'ram',
    'device_id',
    'produk_id',
    'system',
    'version',
    'instal_on',

    'tanggal_digunakan',
    'catatan',

    'status',
  ];

  /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

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
  public function hakAkses()
{
    return $this->hasMany(MapingAccess::class);
}

  /*
    |--------------------------------------------------------------------------
    | AUTO FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */

  protected static function booted()
  {
    static::creating(function ($model) {
      if (auth()->check() && auth()->user()->role !== 'super_admin') {
        $model->id_perusahaan = auth()->user()->id_perusahaan;
      }
    });

    static::addGlobalScope('perusahaan', function ($query) {
      if (auth()->check() && auth()->user()->role !== 'super_admin') {
        $query->where('id_perusahaan', auth()->user()->id_perusahaan);
      }
    });
  }
}

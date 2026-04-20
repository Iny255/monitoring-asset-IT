<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lokasi;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\Maping;

class MutasiMaping extends Model
{
  use HasFactory;

  protected $fillable = [
    'id_maping',
    'id_perusahaan', // 🔥 WAJIB

    'dari_lokasi',
    'ke_lokasi',
    'dari_perusahaan',
    'ke_perusahaan',
    'dari_karyawan',
    'ke_karyawan',

    'dari_no_inventaris',
    'ke_no_inventaris',

    'dari_aplikasi',
    'ke_aplikasi',
    'dari_data_ppn',
    'ke_data_ppn',
    'dari_data_non_ppn',
    'ke_data_non_ppn',

    'tanggal_mutasi',
  ];

  // 🔥 CASTING
  protected $casts = [
    'tanggal_mutasi' => 'date',
  ];

  // ================= RELASI =================

  public function maping()
  {
    return $this->belongsTo(Maping::class, 'id_maping');
  }

  public function dariLokasi()
  {
    return $this->belongsTo(Lokasi::class, 'dari_lokasi');
  }

  public function keLokasi()
  {
    return $this->belongsTo(Lokasi::class, 'ke_lokasi');
  }

  public function dariPerusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'dari_perusahaan');
  }

  public function kePerusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'ke_perusahaan');
  }

  public function dariKaryawan()
  {
    return $this->belongsTo(Karyawan::class, 'dari_karyawan');
  }

  public function keKaryawan()
  {
    return $this->belongsTo(Karyawan::class, 'ke_karyawan');
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
  }

  // ================= AUTO FILTER =================

  protected static function booted()
  {
    // 🔥 AUTO ISI PERUSAHAAN
    static::creating(function ($model) {
      if (auth()->check() && auth()->user()->role != 'super_admin') {
        $model->id_perusahaan = auth()->user()->id_perusahaan;
      }
    });

    // 🔥 FILTER DATA
    static::addGlobalScope('perusahaan', function ($query) {
      if (auth()->check() && auth()->user()->role != 'super_admin') {
        $query->where('id_perusahaan', auth()->user()->id_perusahaan);
      }
    });
  }
}

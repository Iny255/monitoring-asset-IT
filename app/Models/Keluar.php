<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;
use App\Models\Masuk;
use App\Models\Perusahaan;
use App\Models\Maping;

class Keluar extends Model
{
  use HasFactory;

  protected $fillable = [
    'inventaris_id',
    'perusahaan_id',
    'karyawan_id',
    'tgl_keluar',
    'jenis_penerima',
    'divisi_klr',
    'perusahaan_klr',
    'gambar',
  ];

  /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

  public function inventaris()
  {
    return $this->belongsTo(Inventaris::class, 'inventaris_id');
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
  }

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id');
  }
}

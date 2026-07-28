<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Maintenance extends Model
{
  use HasFactory;

  protected $fillable = [
    'kode_service',

    'inventaris_id',

    'asal',

    'maping_id',

    'peminjaman_id',

    'tanggal',

    'jenis',

    'kategori',

    'status',

    'keluhan',

    'diagnosa',

    'tindakan',

    'vendor',

    'biaya',

    'tanggal_selesai',

    'catatan',

    'gambar',

    'created_by',
  ];

  protected $casts = [
    'tanggal' => 'date',
    'tanggal_selesai' => 'date',
    'biaya' => 'decimal:2',
  ];

  /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

  public function inventaris()
  {
    return $this->belongsTo(Inventaris::class);
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'created_by');
  }
  public function maping()
  {
    return $this->belongsTo(Maping::class, 'maping_id');
  }

  public function peminjaman()
  {
    return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
  }
}

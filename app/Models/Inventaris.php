<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
  use HasFactory;
  protected $table = 'inventaris';

  protected $fillable = [
    'masuk_id',
    'perusahaan_id',
    'data_aset_id',
    'kode_aset',
    'no_inventaris',
    'status',
    'is_transfer',
  ];

  /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

  public function masuk()
  {
    return $this->belongsTo(Masuk::class);
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class);
  }

  public function dataAset()
  {
    return $this->belongsTo(DataAset::class);
  }
  public function keluars()
  {
    return $this->hasMany(Keluar::class);
  }

  public function keluarTerakhir()
  {
    return $this->hasOne(Keluar::class)->latestOfMany();
  }
  public function kategori()
  {
    return $this->belongsTo(Kategori::class);
  }
  public function peminjamans()
  {
    return $this->hasMany(Peminjaman::class);
  }
  public function maintenances()
  {
    return $this->hasMany(Maintenance::class);
  }
  /*
|--------------------------------------------------------------------------
| MAINTENANCE
|--------------------------------------------------------------------------
*/

  public function maintenanceAktif()
  {
    return $this->hasOne(Maintenance::class)->whereIn('status', ['Pengajuan', 'Diproses']);
  }

  /*
|--------------------------------------------------------------------------
| SCOPE
|--------------------------------------------------------------------------
*/

  /*
|--------------------------------------------------------------------------
| SCOPE : AVAILABLE FOR MAPPING
|--------------------------------------------------------------------------
*/

  public function scopeAvailableForMapping(\Illuminate\Database\Eloquent\Builder $query)
  {
    return $query

      // Inventaris sudah digunakan
      ->where('status', 'DIPAKAI')

      // Belum transfer perusahaan
      ->where('is_transfer', false)

      // Sudah pernah keluar
      ->whereHas('keluarTerakhir')

      // Belum pernah dimapping
      ->whereDoesntHave('keluarTerakhir.maping')

      // Tidak sedang service
      ->whereDoesntHave('maintenanceAktif');
  }

  /*
|--------------------------------------------------------------------------
| SCOPE : AVAILABLE FOR PEMINJAMAN
|--------------------------------------------------------------------------
*/

  public function scopeAvailableForPeminjaman(\Illuminate\Database\Eloquent\Builder $query)
  {
    return $query

      // Inventaris masih tersedia
      ->where('status', 'TERSEDIA')

      // Belum transfer perusahaan
      ->where('is_transfer', false)

      // Tidak sedang service
      ->whereDoesntHave('maintenanceAktif')

      // Tidak sedang dipinjam
      ->whereDoesntHave('peminjamans', function ($q) {
        $q->where('status', 'Dipinjam');
      });
  }

  /*
|--------------------------------------------------------------------------
| SCOPE : AVAILABLE FOR MAINTENANCE
|--------------------------------------------------------------------------
*/

 public function scopeAvailableForMaintenance(\Illuminate\Database\Eloquent\Builder $query)
{
    return $query

        ->whereIn('status', [

            'DIPAKAI',

            'TERSEDIA',

            'RUSAK',

        ])

        ->where('is_transfer', false)

        ->whereDoesntHave('maintenanceAktif');
}
  /*
    |--------------------------------------------------------------------------
    | MULTI COMPANY
    |--------------------------------------------------------------------------
    */

  protected static function booted()
  {
    static::addGlobalScope('perusahaan', function ($query) {
      if (!auth()->check()) {
        return;
      }

      if (auth()->user()->role != 'super_admin') {
        $query->where('perusahaan_id', auth()->user()->id_perusahaan);
      }
    });
  }
}

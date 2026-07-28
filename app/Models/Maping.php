<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Lokasi;
use App\Models\Keluar;
use App\Models\Perusahaan;
use App\Models\MutasiMaping;
use App\Models\MapingAccess;

class Maping extends Model
{
  use HasFactory;

  protected $fillable = [
    'uuid',
    'id_keluar',
    'id_lokasi',
    'id_perusahaan',
    'karyawan_id',
    'jenis_penerima',
    'divisi',
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

  public function mapingAccesses()
  {
    return $this->hasMany(MapingAccess::class, 'maping_id');
  }
  public function historyHakAkses()
  {
    return $this->hasMany(HistoryHakAkses::class, 'maping_id');
  }
  public function historyMutasis()
  {
    return $this->hasMany(HistoryMutasi::class);
  }
  public function historyPencabutans()
  {
    return $this->hasMany(HistoryPencabutan::class);
  }
  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id');
  }
  public function maintenances()
{
    return $this->hasMany(Maintenance::class, 'maping_id');
}
  /*
|--------------------------------------------------------------------------
| ACCESSOR
|--------------------------------------------------------------------------
*/

  public function getPenerimaAttribute()
  {
    // Tidak ada transaksi keluar
    if ($this->jenis_penerima == 'Perorangan') {
      return optional($this->karyawan)->nama_karyawan ?? '-';
    }

    return $this->divisi ?? '-';
  }

  public function getJenisPenerimaLabelAttribute()
  {
    return $this->jenis_penerima ?? '-';
  }
  public function isAktif(): bool
{
    return $this->status === 'aktif';
}
  /*
    |--------------------------------------------------------------------------
    | AUTO FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */

  protected static function booted()
  {
    static::creating(function ($model) {
      if (empty($model->uuid)) {
        $model->uuid = (string) Str::uuid();
      }
      if (empty($model->id_perusahaan) && auth()->check() && auth()->user()->role !== 'super_admin') {
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

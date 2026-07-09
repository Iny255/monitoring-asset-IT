<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMutasi extends Model
{
  use HasFactory;

  protected $table = 'history_mutasis';

  protected $fillable = [
    'inventaris_id',
    'maping_id',
    'maping_baru_id',

    'jenis_mutasi',

    'id_perusahaan_asal',
    'id_perusahaan_tujuan',

    'kode_aset_lama',
    'kode_aset_baru',

    'no_inventaris_lama',
    'no_inventaris_baru',

    'nama_aset',

    'lokasi_lama',
    'lokasi_baru',

    'user_lama',
    'user_baru',

    'tanggal_mutasi',

    'catatan',
    'opsi_hak_akses',

    'created_by',
  ];

  /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

  public function maping()
  {
    return $this->belongsTo(Maping::class);
  }

  public function perusahaanAsal()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan_asal');
  }

  public function perusahaanTujuan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan_tujuan');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by');
  }
  public function user()
  {
    return $this->belongsTo(User::class, 'created_by');
  }
  public function inventaris()
{
    return $this->belongsTo(Inventaris::class);
}
}

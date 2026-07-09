<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPencabutan extends Model
{
  protected $fillable = [
    'inventaris_id',
    'maping_id',

    'id_perusahaan',

    'kode_aset',

    'no_inventaris',

    'nama_aset',

    'lokasi_lama',

    'lokasi_baru',

    'user_lama',

    'tanggal_pencabutan',

    'alasan',

    'created_by',
  ];

  public function maping()
  {
    return $this->belongsTo(Maping::class);
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by');
  }
   public function inventaris()
{
    return $this->belongsTo(Inventaris::class);
}
}

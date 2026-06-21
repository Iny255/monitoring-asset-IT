<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
  use HasFactory;
  protected $table = 'inventaris';

  protected $fillable = ['masuk_id', 'perusahaan_id', 'data_aset_id', 'kode_aset', 'no_inventaris', 'status'];

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
  public function keluar()
  {
    return $this->hasOne(Keluar::class, 'inventaris_id');
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

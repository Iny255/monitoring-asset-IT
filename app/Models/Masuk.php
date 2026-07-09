<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;
use App\Models\Keluar;
use App\Models\Perusahaan;

class Masuk extends Model
{
  use HasFactory;

  protected $fillable = [
    'perusahaan_id',
    'supplier_id',
    'data_aset_id',
    'tanggal_pembelian',
    'jumlah',
    'harga_satuan',
    'garansi',
    'ket_penerimaan',
  ];

  /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class);
  }
   public function kategori()
  {
    return $this->belongsTo(Kategori::class);
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class);
  }

  public function dataAset()
  {
    return $this->belongsTo(DataAset::class);
  }

  public function inventaris()
  {
    return $this->hasMany(Inventaris::class);
  }
  public function keluars()
{
    return $this->hasMany(Keluar::class, 'id_masuk');
}


  /*
    |--------------------------------------------------------------------------
    | MULTI COMPANY
    |--------------------------------------------------------------------------
    */

  protected static function booted()
  {
    // Auto isi perusahaan
    static::creating(function ($model) {
      if (!auth()->check()) {
        return;
      }

      if (auth()->user()->role != 'super_admin') {
        $model->perusahaan_id = auth()->user()->id_perusahaan;
      }
    });

    // Filter perusahaan
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

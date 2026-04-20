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
    'kode_masuk',
    'id_kategori',
    'type',
    'merek',
    'jumlah',
    'tgl_beli',
    'supplier',
    'gambar',
    'garansi',
    'harga',
    'perusahaan_id', // ✅ TAMBAHAN
  ];

  /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

  public function kategori()
  {
    return $this->belongsTo(Kategori::class, 'id_kategori');
  }

  public function keluar()
  {
    return $this->hasMany(Keluar::class, 'id_masuk');
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
  }

  /*
    |--------------------------------------------------------------------------
    | AUTO SET + GLOBAL SCOPE
    |--------------------------------------------------------------------------
    */

  protected static function booted()
  {
    // 🔥 AUTO ISI PERUSAHAAN
    static::creating(function ($model) {
      if (auth()->check() && auth()->user()->role != 'super_admin') {
        $model->perusahaan_id = auth()->user()->id_perusahaan;
      }
    });

    // 🔥 AUTO FILTER DATA
    static::addGlobalScope('perusahaan', function ($query) {
      if (auth()->check() && auth()->user()->role != 'super_admin') {
        $query->where('perusahaan_id', auth()->user()->id_perusahaan);
      }
    });
  }
}

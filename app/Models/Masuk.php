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
    'kondisi',
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

  public function keluars()
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
    // 🔥 AUTO SET PERUSAHAAN
    static::creating(function ($model) {
      $user = auth()->user();

      if (!$user) {
        return;
      }

      if ($user->role !== 'super_admin') {
        $model->perusahaan_id = $user->id_perusahaan;
      }
    });

    // 🔥 GLOBAL SCOPE (FIX FINAL)
    static::addGlobalScope('perusahaan', function ($query) {
      // 🔥 INI KUNCI UTAMA
      if (app()->runningInConsole()) {
        return;
      }

      $user = auth()->user();

      if (!$user) {
        return;
      }

      if ($user->role !== 'super_admin') {
        $query->where('perusahaan_id', $user->id_perusahaan);
      }
    });
  }
}

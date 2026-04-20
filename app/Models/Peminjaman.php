<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Lokasi;
use App\Models\Keluar;

class Peminjaman extends Model
{
  use HasFactory;

  protected $table = 'peminjamans';

  protected $fillable = [
    'kategori_id',
    'keluar_id',
    'karyawan_id',
    'perusahaan_id',
    'lokasi_id',
    'tanggal_pinjam',
    'tanggal_rencana_kembali',
    'tanggal_kembali',
    'status',
    'keperluan',
    'catatan',
  ];

  // ================= RELASI =================

  public function kategori()
  {
    return $this->belongsTo(Kategori::class, 'kategori_id');
  }

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id');
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
  }

  public function lokasi()
  {
    return $this->belongsTo(Lokasi::class, 'lokasi_id');
  }

  public function keluar()
  {
    return $this->belongsTo(Keluar::class, 'keluar_id');
  }

  // ================= AUTO + FILTER =================

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

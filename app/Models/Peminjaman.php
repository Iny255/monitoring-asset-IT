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
    'nama_eksternal',
    'perusahaan_eksternal',
    'lokasi_manual',
    'tipe_peminjam',
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
      // ✅ SIMPAN SIAPA YANG BUAT
      if (auth()->check()) {
        $model->created_by = auth()->id();
      }

      // ✅ AUTO ISI PERUSAHAAN (NON EXTERNAL)
      if (auth()->check() && auth()->user()->role != 'super_admin' && $model->tipe_peminjam !== 'external') {
        $model->perusahaan_id = auth()->user()->id_perusahaan;
      }
    });

    static::addGlobalScope('perusahaan', function ($query) {
      if (auth()->check() && auth()->user()->role != 'super_admin') {
        $query->where(function ($q) {
          // 🔒 INTERNAL
          $q->where('perusahaan_id', auth()->user()->id_perusahaan)

            // 🔒 EXTERNAL (HANYA MILIK USER)
            ->orWhere(function ($q2) {
              $q2->where('tipe_peminjam', 'external')->where('created_by', auth()->id());
            });
        });
      }
    });
  }
}

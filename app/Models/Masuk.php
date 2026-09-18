<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;
use App\Models\Keluar;
use App\Models\Perusahaan;
use App\Models\HistoryMutasi;
use App\Models\Inventaris;
use App\Models\Maping;

class Masuk extends Model
{
  use HasFactory;

  protected $fillable = [
    'perusahaan_id',
    'supplier_id',
    'perusahaan_asal',
    'history_mutasi_id',
    'data_aset_id',
    'jenis_masuk',
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
  public function perusahaanAsal()
  {
    return $this->belongsTo(Perusahaan::class, 'perusahaan_asal');
  }
  public function historyMutasi()
  {
    return $this->belongsTo(HistoryMutasi::class, 'history_mutasi_id');
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

      if (auth()->user()->role != 'super_admin' && empty($model->perusahaan_id)) {
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

  /*
    |--------------------------------------------------------------------------
    | STATUS MAPPING & EDITABILITY
    |--------------------------------------------------------------------------
    */

  public function isMapped(): bool
  {
    $invIds = $this->inventaris()->pluck('id');
    if ($invIds->isEmpty()) {
      return false;
    }

    $inUse = Inventaris::whereIn('id', $invIds)
      ->where(function ($q) {
        $q->where('status', '!=', 'TERSEDIA')
          ->orWhere('is_transfer', true)
          ->orWhereHas('keluars')
          ->orWhereHas('peminjamans')
          ->orWhereHas('maintenances');
      })
      ->exists();

    if ($inUse) {
      return true;
    }

    return Maping::withoutGlobalScopes()
      ->whereHas('keluar', function ($q) use ($invIds) {
        $q->whereIn('inventaris_id', $invIds);
      })
      ->exists();
  }

  public function canBeEdited(): bool
  {
    return !$this->isMapped();
  }

  public function getIsMappedAttribute(): bool
  {
    return $this->isMapped();
  }

  public function getCanBeEditedAttribute(): bool
  {
    return $this->canBeEdited();
  }
}

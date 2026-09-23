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
use App\Models\HistoryMutasi;
use App\Models\HistoryPencabutan;
use App\Models\Maintenance;
use App\Models\Peminjaman;
use App\Models\ChecklistDevice;

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

  public function checklistDevices()
  {
    return $this->hasMany(ChecklistDevice::class, 'maping_id');
  }

  public function latestChecklistDevice()
  {
    return $this->hasOne(ChecklistDevice::class, 'maping_id')->latestOfMany('id');
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

    return $this->divisi ? strtoupper($this->divisi) : '-';
  }

  public function getJenisPenerimaLabelAttribute()
  {
    return $this->jenis_penerima ?? '-';
  }
  public function isAktif(): bool
  {
    return $this->status === 'aktif';
  }

  /**
   * Cek apakah data mapping ini memenuhi syarat untuk dihapus:
   * Hanya boleh dihapus jika masih "Fresh" (belum pernah ada mutasi, pencabutan, servis, atau transaksi lanjutan).
   */
  public function canBeDeleted(): bool
  {
    // Jika status bukan aktif (misal sudah selesai/dicabut atau diservis), tidak boleh dihapus langsung
    if ($this->status !== 'aktif') {
      return false;
    }

    // Cek apakah sudah pernah ada riwayat mutasi
    $hasMutasi = HistoryMutasi::where('maping_id', $this->id)
      ->orWhere('maping_baru_id', $this->id)
      ->exists();
    if ($hasMutasi) {
      return false;
    }

    // Cek apakah sudah pernah ada riwayat pencabutan
    if ($this->historyPencabutans()->exists()) {
      return false;
    }

    // Cek apakah sudah pernah ada riwayat perbaikan / servis
    if ($this->maintenances()->exists()) {
      return false;
    }

    // Cek apakah inventaris sedang dipinjam
    $inventarisId = $this->keluar?->inventaris_id;
    if ($inventarisId) {
      $isDipinjam = Peminjaman::where('inventaris_id', $inventarisId)
        ->whereIn('status', ['dipinjam', 'DIPINJAM'])
        ->exists();
      if ($isDipinjam) {
        return false;
      }
    }

    return true;
  }

  public function getCanBeDeletedAttribute(): bool
  {
    return $this->canBeDeleted();
  }

  public function getDeleteBlockReasonAttribute(): ?string
  {
    if ($this->status !== 'aktif') {
      return "Mapping ini berstatus '{$this->status}' dan tidak dapat dihapus secara langsung.";
    }

    $hasMutasi = HistoryMutasi::where('maping_id', $this->id)
      ->orWhere('maping_baru_id', $this->id)
      ->exists();
    if ($hasMutasi) {
      return 'Mapping ini sudah memiliki riwayat mutasi aset sehingga tidak dapat dihapus.';
    }

    if ($this->historyPencabutans()->exists()) {
      return 'Mapping ini sudah memiliki riwayat pencabutan perangkat.';
    }

    if ($this->maintenances()->exists()) {
      return 'Mapping ini sudah memiliki riwayat perbaikan/maintenance perangkat.';
    }

    $inventarisId = $this->keluar?->inventaris_id;
    if ($inventarisId) {
      $isDipinjam = Peminjaman::where('inventaris_id', $inventarisId)
        ->whereIn('status', ['dipinjam', 'DIPINJAM'])
        ->exists();
      if ($isDipinjam) {
        return 'Perangkat pada mapping ini sedang dalam status peminjaman aktif.';
      }
    }

    return null;
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

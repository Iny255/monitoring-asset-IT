<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
  protected $table = 'peminjamans';

  protected $fillable = [
    'kode_peminjaman',
    'inventaris_id',
    'jenis_peminjaman',
    'karyawan_id',
    'perusahaan_tujuan_id',
    'karyawan_tujuan_id',
    'id_lokasi',
    'user_id',
    'tanggal_pinjam',
    'tanggal_rencana_kembali',
    'tanggal_kembali',
    'keperluan',
    'kondisi_pinjam',
    'kondisi_kembali',
    'keterangan_kembali',
    'status',
  ];

  /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

  public function inventaris()
  {
    return $this->belongsTo(Inventaris::class);
  }

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class);
  }

  public function perusahaanTujuan()
  {
    return $this->belongsTo(Perusahaan::class, 'perusahaan_tujuan_id');
  }

  public function karyawanTujuan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_tujuan_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function lokasi()
  {
    return $this->belongsTo(Lokasi::class, 'id_lokasi');
  }

  public function checklistDevices()
  {
    return $this->hasMany(ChecklistDevice::class, 'peminjaman_id');
  }

  public function maintenanceTerakhir()
  {
    return $this->hasOne(Maintenance::class, 'peminjaman_id')
        ->latestOfMany();
  }

  public function isDipinjam(): bool
  {
    return strtolower($this->status) === 'dipinjam';
  }

  public function isDikembalikan(): bool
  {
    return in_array(strtolower($this->status), ['dikembalikan', 'selesai']);
  }

  public function getPeminjamNamaAttribute(): string
  {
    if ($this->jenis_peminjaman === 'internal') {
      return $this->karyawan?->nama_karyawan ?? '-';
    }
    $perusahaan = $this->perusahaanTujuan?->nama_perusahaan ?? '-';
    $pj = $this->karyawanTujuan?->nama_karyawan ? " ({$this->karyawanTujuan->nama_karyawan})" : '';
    return $perusahaan . $pj;
  }
}

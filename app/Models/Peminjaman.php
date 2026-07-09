<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
  protected $fillable = [
    'kode_peminjaman',
    'inventaris_id',
    'jenis_peminjaman',
    'karyawan_id',
    'perusahaan_tujuan_id',
    'karyawan_tujuan_id',
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
}

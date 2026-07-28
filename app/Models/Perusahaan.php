<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
  use HasFactory;
  protected $fillable = [
    'nama_perusahaan',
    'kode_perusahaan',
    'logo',
    'primary_color',
    'secondary_color',
    'parent_id',
    'tipe',
  ];

  public function parent()
  {
    return $this->belongsTo(Perusahaan::class, 'parent_id');
  }

  public function cabangs()
  {
    return $this->hasMany(Perusahaan::class, 'parent_id');
  }

  public function isInduk()
  {
    return $this->tipe === 'Induk' || $this->parent_id === null;
  }

  public function isCabang()
  {
    return $this->tipe === 'Cabang' || $this->parent_id !== null;
  }

  public function getAllCompanyIds()
  {
    if ($this->isInduk()) {
      return $this->cabangs()->pluck('id')->push($this->id);
    }
    return collect([$this->id]);
  }

  public function maping()
  {
    return $this->hasMany(Maping::class, 'id_perusahaan');
  }
  public function masuk()
  {
    return $this->hasMany(\App\Models\Masuk::class, 'perusahaan_id');
  }

  public function keluar()
  {
    return $this->hasMany(\App\Models\Keluar::class, 'id_perusahaan');
  }

  public function user()
  {
    return $this->hasMany(\App\Models\User::class, 'id_perusahaan');
  }
  public function mutasiMasuk()
  {
    return $this->hasMany(HistoryMutasi::class, 'id_perusahaan_tujuan');
  }

  public function mutasiKeluar()
  {
    return $this->hasMany(HistoryMutasi::class, 'id_perusahaan_asal');
  }
  public function historyPencabutans()
  {
    return $this->hasMany(HistoryPencabutan::class, 'id_perusahaan');
  }
  public function peminjamans()
  {
    return $this->hasMany(Peminjaman::class, 'perusahaan_tujuan_id');
  }
}

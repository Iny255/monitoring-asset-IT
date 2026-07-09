<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
  use HasFactory;
  protected $table = 'accesses';

  protected $fillable = ['id_perusahaan','kategori', 'jenis','nama_akses', 'status'];

  public function mapings()
  {
    return $this->hasMany(MapingAccess::class);
  }
  public function historyHakAkses()
{
    return $this->hasMany(HistoryHakAkses::class, 'access_id');
}
public function perusahaan()
{
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryHakAkses extends Model
{
  use HasFactory;

  protected $table = 'history_hak_akses';

  protected $fillable = ['maping_id', 'access_id', 'user_id', 'aksi', 'keterangan'];

  /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

  // Mapping
  public function maping()
  {
    return $this->belongsTo(Maping::class, 'maping_id');
  }

  // Master Hak Akses
  public function access()
  {
    return $this->belongsTo(Access::class, 'access_id');
  }

  // User yang melakukan aksi
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}

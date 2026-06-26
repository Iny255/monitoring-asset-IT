<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
  use HasFactory;
  protected $table = 'accesses';

  protected $fillable = ['kategori', 'jenis','nama_akses', 'status'];

  public function mapings()
  {
    return $this->hasMany(MapingAccess::class);
  }
}

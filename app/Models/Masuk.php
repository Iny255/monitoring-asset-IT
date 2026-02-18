<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;
use App\Models\Keluar;

class Masuk extends Model
{
   use HasFactory;
     protected $fillable = [
        'kode_masuk',
        'id_kategori',
        'type',
        'merek',
        'jumlah',
        'tgl_beli',
        'supplier',
        'gambar',
        'garansi',
        'harga',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }
    public function keluar()
{
    return $this->hasMany(Keluar::class, 'id_masuk');
}
}


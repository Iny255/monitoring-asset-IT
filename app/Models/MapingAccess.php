<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapingAccess extends Model
{
    use HasFactory;
    protected $fillable = [
        'maping_id',
        'access_id',
        'nama_akses',
        'kategori',
        'jenis',
        'email',
        'password',
        'status'
    ];

    public function maping()
    {
        return $this->belongsTo(Maping::class);
    }

    public function access()
    {
        return $this->belongsTo(Access::class);
    }

    public function getNamaAksesAttribute($value)
    {
        return $value ?? $this->access?->nama_akses ?? '-';
    }

    public function getKategoriAttribute($value)
    {
        return $value ?? $this->access?->kategori ?? 'Hak Akses';
    }

    public function getJenisAttribute($value)
    {
        return $value ?? $this->access?->jenis ?? 'NON PPN';
    }
}

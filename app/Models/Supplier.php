<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'perusahaan_id',
        'nama_supplier',
        'alamat',
        'telepon',
        'no_hp',
    ];

    public function getNoHpAttribute()
    {
        return $this->telepon;
    }

    public function setNoHpAttribute($value)
    {
        $this->attributes['telepon'] = $value;
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}

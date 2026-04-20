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
    ];

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pencabutan extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_maping',
        'id_keluar',
        'tanggal_cabut',
        'kondisi',
        'alasan'
    ];
    public function maping()
    {
        return $this->belongsTo(Maping::class);
    }

    public function keluar()
    {
        return $this->belongsTo(Keluar::class, 'id_keluar');
    }
}

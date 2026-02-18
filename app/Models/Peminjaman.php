<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Lokasi;

class Peminjaman extends Model
{
    use HasFactory;
    protected $table = 'peminjamans';
    protected $fillable = [
        'kategori_id',
        'keluar_id',
        'karyawan_id',
        'perusahaan_id',
        'lokasi_id',
        'tanggal_pinjam',
        'tanggal_rencana_kembali',
        'tanggal_kembali',
        'status',
        'keperluan',
        'catatan'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
    public function keluar()
    {
        return $this->belongsTo(Keluar::class, 'keluar_id');
    }
}

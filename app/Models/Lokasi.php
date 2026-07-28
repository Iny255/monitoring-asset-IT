<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Perusahaan;
use App\Models\Maping;

class Lokasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lokasi',
        'kode_lokasi',
        'id_perusahaan' // ✅ WAJIB
    ];

    // ================= RELASI =================

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function maping()
    {
        return $this->hasMany(Maping::class, 'id_lokasi');
    }

    // ================= AUTO + FILTER =================

    protected static function booted()
    {
        // 🔥 AUTO ISI PERUSAHAAN
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->role != 'super_admin') {
                $model->id_perusahaan = auth()->user()->id_perusahaan;
            }
        });

        // 🔥 AUTO FILTER DATA
        static::addGlobalScope('perusahaan', function ($query) {
            if (auth()->check() && auth()->user()->role != 'super_admin') {
                $accessibleIds = auth()->user()->getAccessibleCompanyIds();
                if ($accessibleIds && count($accessibleIds) > 0) {
                    $query->whereIn('id_perusahaan', $accessibleIds);
                } else {
                    $query->where('id_perusahaan', auth()->user()->id_perusahaan);
                }
            }
        });
    }
}

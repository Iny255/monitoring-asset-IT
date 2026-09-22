<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistJadwalRutin extends Model
{
    use HasFactory;

    protected $table = 'checklist_jadwal_rutins';

    protected $fillable = [
        'id_perusahaan',
        'id_lokasi',
        'hari',
        'assigned_to',
        'jam_mulai',
        'jam_selesai',
        'is_active',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checklistRuangans()
    {
        return $this->hasMany(ChecklistRuangan::class, 'jadwal_rutin_id');
    }

    public function getNamaHariAttribute()
    {
        $hariMap = [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu',
        ];
        return $hariMap[strtolower($this->hari)] ?? ucfirst($this->hari);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHari($query, $hari)
    {
        return $query->where('hari', strtolower($hari));
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
            if (empty($model->id_perusahaan) && auth()->check() && auth()->user()->role !== 'super_admin') {
                $model->id_perusahaan = auth()->user()->id_perusahaan;
            }
        });

        static::addGlobalScope('perusahaan', function ($query) {
            if (auth()->check() && auth()->user()->role !== 'super_admin') {
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistJadwal extends Model
{
    use HasFactory;

    protected $table = 'checklist_jadwals';

    protected $fillable = [
        'kode_jadwal',
        'id_perusahaan',
        'id_lokasi',
        'tahun',
        'bulan',
        'minggu_ke',
        'tanggal_mulai',
        'tanggal_selesai',
        'assigned_to',
        'status',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tahun' => 'integer',
        'bulan' => 'integer',
        'minggu_ke' => 'integer',
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

    public function checklistRuangan()
    {
        return $this->hasOne(ChecklistRuangan::class, 'jadwal_id');
    }

    public function getNamaBulanAttribute()
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $namaBulan[$this->bulan] ?? '';
    }

    public function getPeriodeLabelAttribute()
    {
        return "Minggu ke-{$this->minggu_ke} ({$this->nama_bulan} {$this->tahun})";
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

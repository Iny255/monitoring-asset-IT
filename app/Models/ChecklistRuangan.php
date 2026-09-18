<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistRuangan extends Model
{
    use HasFactory;

    protected $table = 'checklist_ruangans';

    protected $fillable = [
        'jadwal_id',
        'id_lokasi',
        'id_perusahaan',
        'petugas_id',
        'tanggal_cek',
        'status',
        'kondisi_ruangan',
        'total_device',
        'total_checked',
        'catatan_ruangan',
    ];

    protected $casts = [
        'tanggal_cek' => 'datetime',
        'total_device' => 'integer',
        'total_checked' => 'integer',
    ];

    public function jadwal()
    {
        return $this->belongsTo(ChecklistJadwal::class, 'jadwal_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function checklistDevices()
    {
        return $this->hasMany(ChecklistDevice::class, 'checklist_ruangan_id');
    }

    public function getPersentaseAttribute()
    {
        if ($this->total_device <= 0) {
            return 0;
        }
        return min(100, round(($this->total_checked / $this->total_device) * 100));
    }

    public function updateProgress()
    {
        $devices = $this->checklistDevices()->get();
        $totalDevice = $devices->count();
        $totalChecked = $devices->where('status_device', '!=', 'belum_dicek')->count();
        $adaKendala = $devices->where('status_device', 'ada_kendala')->count() > 0;

        $this->total_device = $totalDevice;
        $this->total_checked = $totalChecked;
        $this->kondisi_ruangan = $adaKendala ? 'ada_kendala' : 'semua_baik';

        if ($totalChecked === 0) {
            $this->status = 'belum_dicek';
        } elseif ($totalChecked < $totalDevice) {
            $this->status = 'sedang_dicek';
        } else {
            $this->status = 'selesai';
            if (empty($this->tanggal_cek)) {
                $this->tanggal_cek = now();
            }
        }

        $this->save();

        // Update status jadwal
        if ($this->jadwal) {
            if ($this->status === 'selesai') {
                $this->jadwal->status = 'selesai';
            } elseif ($this->status === 'sedang_dicek') {
                $this->jadwal->status = 'berjalan';
            }
            $this->jadwal->save();
        }
    }

    protected static function booted()
    {
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

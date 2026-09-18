<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistDevice extends Model
{
    use HasFactory;

    protected $table = 'checklist_devices';

    protected $fillable = [
        'checklist_ruangan_id',
        'maping_id',
        'inventaris_id',
        'nama_pengguna',
        'status_device',
        'catatan_kendala',
        'maintenance_id',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function checklistRuangan()
    {
        return $this->belongsTo(ChecklistRuangan::class, 'checklist_ruangan_id');
    }

    public function maping()
    {
        return $this->belongsTo(Maping::class, 'maping_id');
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id');
    }

    public function items()
    {
        return $this->hasMany(ChecklistDeviceItem::class, 'checklist_device_id');
    }
}

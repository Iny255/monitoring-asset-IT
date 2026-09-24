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
        'peminjaman_id',
        'inventaris_id',
        'nama_pengguna',
        'status_device',
        'catatan_kendala',
        'maintenance_id',
        'checked_at',
        'checked_by',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function checklistRuangan()
    {
        return $this->belongsTo(ChecklistRuangan::class, 'checklist_ruangan_id')->withoutGlobalScopes();
    }

    public function maping()
    {
        return $this->belongsTo(Maping::class, 'maping_id')->withoutGlobalScopes();
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function items()
    {
        return $this->hasMany(ChecklistDeviceItem::class, 'checklist_device_id');
    }

    public function getIsCheckedAttribute(): bool
    {
        return in_array($this->status_device, ['normal', 'ada_kendala']);
    }

    public function getIsPinjamanAttribute(): bool
    {
        return !empty($this->peminjaman_id);
    }

    public function getFormattedCheckedAtAttribute(): ?string
    {
        return $this->checked_at ? $this->checked_at->format('d M Y, H:i') : null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistDeviceItem extends Model
{
    use HasFactory;

    protected $table = 'checklist_device_items';

    protected $fillable = [
        'checklist_device_id',
        'nama_item',
        'kategori_item',
        'is_ok',
        'catatan',
    ];

    protected $casts = [
        'is_ok' => 'boolean',
    ];

    public function checklistDevice()
    {
        return $this->belongsTo(ChecklistDevice::class, 'checklist_device_id');
    }
}

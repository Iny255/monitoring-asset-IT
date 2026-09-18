<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $table = 'checklist_items';

    protected $fillable = [
        'id_perusahaan',
        'kategori',
        'nama_item',
        'keterangan',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id_perusahaan) && auth()->check() && auth()->user()->role !== 'super_admin') {
                $model->id_perusahaan = auth()->user()->id_perusahaan;
            }
        });

        static::addGlobalScope('perusahaan', function ($query) {
            if (auth()->check() && auth()->user()->role !== 'super_admin') {
                $accessibleIds = auth()->user()->getAccessibleCompanyIds();
                if ($accessibleIds && count($accessibleIds) > 0) {
                    $query->where(function ($q) use ($accessibleIds) {
                        $q->whereIn('id_perusahaan', $accessibleIds)
                          ->orWhereNull('id_perusahaan');
                    });
                } else {
                    $query->where(function ($q) {
                        $q->where('id_perusahaan', auth()->user()->id_perusahaan)
                          ->orWhereNull('id_perusahaan');
                    });
                }
            }
        });
    }
}

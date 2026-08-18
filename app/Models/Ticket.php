<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_tiket',
        'user_id',
        'karyawan_id',
        'id_perusahaan',
        'lokasi_id',
        'inventaris_id',
        'ticket_category_id',
        'judul',
        'deskripsi',
        'prioritas',
        'status',
        'assigned_to',
        'maintenance_id',
        'lampiran',
        'responded_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public static function generateNomorTiket()
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = "TKT-{$today}-";

        $lastTicket = self::where('nomor_tiket', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastTicket) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($lastTicket->nomor_tiket, -4);
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at', 'asc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
        'tindakan_perbaikan',
        'tindakan_pencegahan',
        'verifikasi',
        'assigned_to',
        'maintenance_id',
        'lampiran',
        'nama_pelapor',
        'kontak_pelapor',
        'email_pelapor',
        'is_public',
        'responded_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
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

    public function getPelaporNameAttribute(): string
    {
        if ($this->karyawan) {
            return $this->karyawan->nama_karyawan;
        }

        if ($this->nama_pelapor) {
            return $this->nama_pelapor;
        }

        if ($this->user) {
            return $this->user->name;
        }

        return 'Pelapor Publik';
    }

    public function getPelaporContactAttribute(): ?string
    {
        return $this->kontak_pelapor ?? null;
    }

    public function getPerangkatNameAttribute(): string
    {
        if ($this->inventaris) {
            $kode = $this->inventaris->kode_aset;
            $namaAset = $this->inventaris->dataAset?->nama_aset ?? $this->inventaris->dataAset?->kategori?->nama_barang ?? '';
            if ($kode && $namaAset) {
                return "{$namaAset} ({$kode})";
            }
            return $kode ?: ($namaAset ?: '-');
        }
        return '-';
    }

    public function getDivisiPelaporAttribute(): string
    {
        if ($this->karyawan && !empty($this->karyawan->divisi)) {
            return $this->karyawan->divisi;
        }
        if ($this->user && !empty($this->user->divisi)) {
            return $this->user->divisi;
        }
        return '-';
    }

    public function getTindakanPerbaikanFormattedAttribute(): string
    {
        if (!empty($this->tindakan_perbaikan)) {
            return $this->tindakan_perbaikan;
        }

        // Fallback: Cari balasan dari petugas IT / reply terakhir
        $lastItReply = $this->replies()
            ->where(function ($q) {
                $q->where('user_id', '!=', $this->user_id)
                  ->orWhere('is_internal_note', 1);
            })
            ->latest()
            ->first();

        if ($lastItReply && !empty($lastItReply->pesan)) {
            return Str::limit(strip_tags($lastItReply->pesan), 150);
        }

        if ($this->maintenance && !empty($this->maintenance->tindakan)) {
            return $this->maintenance->tindakan;
        }

        return in_array($this->status, ['resolved', 'closed']) ? 'Penanganan kendala selesai' : '-';
    }

    public function getTindakanPencegahanFormattedAttribute(): string
    {
        if (!empty($this->tindakan_pencegahan)) {
            return $this->tindakan_pencegahan;
        }
        return '-';
    }

    public function getVerifikasiFormattedAttribute(): string
    {
        if (!empty($this->verifikasi)) {
            return $this->verifikasi;
        }

        if (in_array($this->status, ['resolved', 'closed'])) {
            $tindakan = $this->tindakan_perbaikan_formatted;
            if ($tindakan && $tindakan !== '-' && $tindakan !== 'Penanganan kendala selesai') {
                return 'Telah dilakukan ' . lcfirst($tindakan);
            }
            return 'Telah diverifikasi dan selesai ditangani';
        }

        return '-';
    }

    public function isStatusOk(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }
}

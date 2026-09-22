<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ChecklistDevice;
use App\Models\ChecklistDeviceItem;
use App\Models\ChecklistItem;
use App\Models\Maping;

class ChecklistRuangan extends Model
{
    use HasFactory;

    protected $table = 'checklist_ruangans';

    protected $fillable = [
        'jadwal_id',
        'jadwal_rutin_id',
        'tanggal_pemeriksaan',
        'hari',
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
        'tanggal_pemeriksaan' => 'date',
        'tanggal_cek' => 'datetime',
        'total_device' => 'integer',
        'total_checked' => 'integer',
    ];

    public function jadwal()
    {
        return $this->belongsTo(ChecklistJadwal::class, 'jadwal_id');
    }

    public function jadwalRutin()
    {
        return $this->belongsTo(ChecklistJadwalRutin::class, 'jadwal_rutin_id');
    }

    public function getNamaHariAttribute()
    {
        if (!empty($this->hari)) {
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

        if ($this->tanggal_pemeriksaan) {
            $dayOfWeek = (int) $this->tanggal_pemeriksaan->format('N'); // 1 (Mon) - 7 (Sun)
            $map = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
            return $map[$dayOfWeek] ?? '';
        }

        return '';
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
            $this->tanggal_cek = null;
        } elseif ($totalChecked < $totalDevice) {
            $this->status = 'sedang_dicek';
            $this->tanggal_cek = null;
        } else {
            $this->status = 'selesai';
            if (empty($this->tanggal_cek)) {
                $this->tanggal_cek = \Illuminate\Support\Carbon::now('Asia/Jakarta');
            }
        }

        $this->save();

        // Update status jadwal
        if ($this->jadwal) {
            if ($this->status === 'selesai') {
                $this->jadwal->status = 'selesai';
            } elseif ($this->status === 'sedang_dicek') {
                $this->jadwal->status = 'berjalan';
            } elseif ($this->status === 'belum_dicek') {
                $this->jadwal->status = 'terjadwal';
            }
            $this->jadwal->save();
        }
    }

    /**
     * Sinkronisasi perangkat di ruangan checklist ini langsung dari data Maping aktif.
     * - Hapus device yang sudah mutasi ke perusahaan lain / pindah lokasi / mapping status bukan 'aktif' (jika belum dicek)
     * - Tambahkan mapping aktif baru di lokasi ini yang belum ada di checklist
     * - Perbarui nama penerima/pengguna jika ada perubahan di mapping
     * - Perbarui kalkulasi progress total_device dan status ruangan
     */
    public function syncDevicesWithMapping()
    {
        // 1. Ambil seluruh mapping yang benar-benar aktif di lokasi & perusahaan ruangan ini
        $activeMappings = Maping::withoutGlobalScopes()
            ->where('id_lokasi', $this->id_lokasi)
            ->where('status', 'aktif')
            ->when($this->id_perusahaan, fn($q) => $q->where('id_perusahaan', $this->id_perusahaan))
            ->whereHas('keluar.inventaris', function ($iq) {
                $iq->where('is_transfer', false);
            })
            ->with(['karyawan', 'keluar.inventaris.dataAset'])
            ->get();

        // 2. Ambil seluruh device yang saat ini tercatat di checklist ruangan ini
        $currentDevices = $this->checklistDevices()
            ->with(['maping.keluar.inventaris', 'inventaris', 'items'])
            ->get();

        foreach ($currentDevices as $device) {
            $m = $device->maping;
            $inv = $device->inventaris;

            // Kriteria perangkat tidak valid di ruangan ini lagi:
            // - Mapping ID null atau data mapping tidak ada di database
            // - Mapping status bukan 'aktif' (misal: 'selesai' karena mutasi antar perusahaan, 'ditarik', dll)
            // - Lokasi mapping sudah bukan di lokasi ruangan ini lagi
            // - Perusahaan mapping sudah bukan di perusahaan ruangan ini lagi
            // - Inventaris ditandai transfer (is_transfer = true)
            $isInvalid = !$device->maping_id
                || !$m
                || $m->status !== 'aktif'
                || $m->id_lokasi != $this->id_lokasi
                || ($this->id_perusahaan && $m->id_perusahaan != $this->id_perusahaan)
                || ($inv && $inv->is_transfer);

            if ($isInvalid) {
                // Jika belum dicek oleh teknisi (atau ruangan belum selesai), bersihkan dari checklist!
                if ($device->status_device === 'belum_dicek' || $this->status !== 'selesai') {
                    $device->items()->delete();
                    $device->delete();
                    continue;
                }
            } else {
                // Perangkat masih aktif dan valid di lokasi ini
                // Sinkronkan nama pengguna/penerima jika berbeda dari mapping terbaru
                $penerimaTerbaru = $m->penerima;
                if ($penerimaTerbaru && $device->nama_pengguna !== $penerimaTerbaru) {
                    $device->nama_pengguna = $penerimaTerbaru;
                    $device->save();
                }
            }
        }

        // 3. Tambahkan mapping aktif di lokasi ini yang belum ada di checklist ruangan
        $existingDeviceMappingIds = $this->checklistDevices()->pluck('maping_id')->filter()->toArray();
        $missingMappings = $activeMappings->whereNotIn('id', $existingDeviceMappingIds);

        if ($missingMappings->isNotEmpty()) {
            $defaultItems = ChecklistItem::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('id_perusahaan')
                      ->orWhere('id_perusahaan', $this->id_perusahaan);
                })
                ->orderBy('urutan')
                ->get();

            foreach ($missingMappings as $mapping) {
                $inventaris = $mapping->keluar?->inventaris;
                if (!$inventaris) {
                    continue;
                }

                $newDevice = ChecklistDevice::create([
                    'checklist_ruangan_id' => $this->id,
                    'maping_id' => $mapping->id,
                    'inventaris_id' => $inventaris->id,
                    'nama_pengguna' => $mapping->penerima,
                    'status_device' => 'belum_dicek',
                ]);

                foreach ($defaultItems as $item) {
                    ChecklistDeviceItem::create([
                        'checklist_device_id' => $newDevice->id,
                        'nama_item' => $item->nama_item,
                        'kategori_item' => $item->kategori,
                        'is_ok' => true,
                    ]);
                }
            }
        }

        // 4. Update progress dan total_device
        $this->updateProgress();
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

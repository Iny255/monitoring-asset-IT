<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\ChecklistDevice;
use App\Models\ChecklistDeviceItem;
use App\Models\ChecklistItem;
use App\Models\Maping;
use App\Models\Peminjaman;

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

        // 1b. Ambil seluruh peminjaman aset yang aktif di lokasi ruangan ini
        $activeLoans = Peminjaman::where('status', 'Dipinjam')
            ->where('id_lokasi', $this->id_lokasi)
            ->whereHas('inventaris', function ($iq) {
                $iq->where('is_transfer', false)
                   ->when($this->id_perusahaan, fn($q) => $q->where('perusahaan_id', $this->id_perusahaan));
            })
            ->with(['inventaris.dataAset', 'karyawan', 'karyawanTujuan', 'perusahaanTujuan'])
            ->get();

        // 2. Ambil seluruh device yang saat ini tercatat di checklist ruangan ini
        $currentDevices = $this->checklistDevices()
            ->with(['maping.keluar.inventaris', 'peminjaman.inventaris', 'inventaris', 'items'])
            ->get();

        foreach ($currentDevices as $device) {
            $inv = $device->inventaris;

            // Jika device berasal dari transaksi peminjaman
            if ($device->is_pinjaman) {
                $loan = $device->peminjaman;
                $isInvalidLoan = !$loan
                    || strtolower($loan->status) !== 'dipinjam'
                    || $loan->id_lokasi != $this->id_lokasi
                    || ($inv && $inv->is_transfer);

                if ($isInvalidLoan) {
                    if ($device->status_device === 'belum_dicek') {
                        $device->items()->delete();
                        $device->delete();
                        continue;
                    }
                } else {
                    $peminjamNama = $loan->peminjam_nama ? "[Pinjaman] {$loan->peminjam_nama}" : '[Pinjaman]';
                    if ($device->nama_pengguna !== $peminjamNama) {
                        $device->nama_pengguna = $peminjamNama;
                        $device->save();
                    }
                }
                continue;
            }

            // Jika device berasal dari mapping tetap
            $m = $device->maping;

            // Kriteria perangkat tidak valid di ruangan ini lagi:
            // - Mapping ID null atau data mapping tidak ada di database
            // - Mapping status bukan 'aktif' (misal: 'selesai' karena mutasi antar perusahaan, 'ditarik', dll)
            // - Lokasi mapping sudah bukan di lokasi ruangan ini lagi
            // - Perusahaan mapping sudah berbeda dari perusahaan ruangan ini
            // - Inventaris ditandai transfer (is_transfer = true)
            $isInvalid = !$device->maping_id
                || !$m
                || $m->status !== 'aktif'
                || $m->id_lokasi != $this->id_lokasi
                || ($this->id_perusahaan && $m->id_perusahaan && $m->id_perusahaan != $this->id_perusahaan)
                || ($inv && $inv->is_transfer);

            if ($isInvalid) {
                // JANGAN PERNAH hapus perangkat yang SUDAH DICEK (normal / ada_kendala)!
                // Hanya bersihkan jika status_device memang masih 'belum_dicek'
                if ($device->status_device === 'belum_dicek') {
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

        // 3. Tambahkan mapping aktif & peminjaman aktif di lokasi ini yang belum ada di checklist ruangan
        $existingDeviceMappingIds = $this->checklistDevices()->pluck('maping_id')->filter()->toArray();
        $existingDeviceLoanIds = $this->checklistDevices()->pluck('peminjaman_id')->filter()->toArray();
        $existingDeviceInventarisIds = $this->checklistDevices()->pluck('inventaris_id')->filter()->toArray();

        $missingMappings = $activeMappings->filter(function ($mapping) use ($existingDeviceMappingIds, $existingDeviceInventarisIds) {
            $invId = $mapping->keluar?->inventaris_id;
            return !in_array($mapping->id, $existingDeviceMappingIds) && (!$invId || !in_array($invId, $existingDeviceInventarisIds));
        });

        $missingLoans = $activeLoans->filter(function ($loan) use ($existingDeviceLoanIds, $existingDeviceInventarisIds) {
            return !in_array($loan->id, $existingDeviceLoanIds) && !in_array($loan->inventaris_id, $existingDeviceInventarisIds);
        });

        if ($missingMappings->isNotEmpty() || $missingLoans->isNotEmpty()) {
            $defaultItems = ChecklistItem::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('id_perusahaan')
                      ->orWhere('id_perusahaan', $this->id_perusahaan);
                })
                ->orderBy('urutan')
                ->get();

            // Insert missing mappings
            foreach ($missingMappings as $mapping) {
                $inventaris = $mapping->keluar?->inventaris;
                if (!$inventaris) {
                    continue;
                }

                $newDevice = ChecklistDevice::create([
                    'checklist_ruangan_id' => $this->id,
                    'maping_id' => $mapping->id,
                    'peminjaman_id' => null,
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

                $existingDeviceInventarisIds[] = $inventaris->id;
            }

            // Insert missing loans
            foreach ($missingLoans as $loan) {
                $inv = $loan->inventaris;
                if (!$inv || in_array($inv->id, $existingDeviceInventarisIds)) {
                    continue;
                }

                $newDevice = ChecklistDevice::create([
                    'checklist_ruangan_id' => $this->id,
                    'maping_id' => null,
                    'peminjaman_id' => $loan->id,
                    'inventaris_id' => $inv->id,
                    'nama_pengguna' => '[Pinjaman] ' . $loan->peminjam_nama,
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

                $existingDeviceInventarisIds[] = $inv->id;
            }
        }

        // 4. Sinkronkan status otomatis jika perangkat ini sudah diperiksa (misal dari Scan QR di lapangan hari ini)
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $targetDate = $this->tanggal_pemeriksaan ? $this->tanggal_pemeriksaan->format('Y-m-d') : $today;

        $unverifiedDevices = $this->checklistDevices()->where('status_device', 'belum_dicek')->get();
        foreach ($unverifiedDevices as $dev) {
            $invId = $dev->inventaris_id;
            $mapId = $dev->maping_id;

            // Cari apakah perangkat ini sudah memiliki riwayat cek normal/kendala hari ini (misal via scan QR di lapangan)
            $completedCheck = ChecklistDevice::withoutGlobalScopes()
                ->where('id', '!=', $dev->id)
                ->where(function ($q) use ($mapId, $invId) {
                    if ($mapId) $q->where('maping_id', $mapId);
                    if ($invId) {
                        $mapId ? $q->orWhere('inventaris_id', $invId) : $q->where('inventaris_id', $invId);
                    }
                })
                ->whereIn('status_device', ['normal', 'ada_kendala'])
                ->where(function ($q) use ($today, $targetDate) {
                    $q->whereDate('checked_at', $today)
                      ->orWhereDate('checked_at', $targetDate)
                      ->orWhereHas('checklistRuangan', function ($sub) use ($today, $targetDate) {
                          $sub->whereDate('tanggal_pemeriksaan', $today)
                              ->orWhereDate('tanggal_pemeriksaan', $targetDate)
                              ->orWhereDate('tanggal_cek', $today);
                      });
                })
                ->orderByDesc('checked_at')
                ->orderByDesc('id')
                ->first();

            if ($completedCheck) {
                $dev->update([
                    'status_device' => $completedCheck->status_device,
                    'catatan_kendala' => $completedCheck->catatan_kendala,
                    'checked_at' => $completedCheck->checked_at ?: Carbon::now('Asia/Jakarta'),
                    'checked_by' => $completedCheck->checked_by ?: auth()->id(),
                ]);

                // Sinkronkan item check
                $checkItems = $completedCheck->items()->get();
                if ($checkItems->isNotEmpty()) {
                    foreach ($dev->items as $dItem) {
                        $sourceItem = $checkItems->firstWhere('nama_item', $dItem->nama_item);
                        if ($sourceItem) {
                            $dItem->update(['is_ok' => $sourceItem->is_ok]);
                        } elseif ($completedCheck->status_device === 'normal') {
                            $dItem->update(['is_ok' => true]);
                        }
                    }
                } elseif ($completedCheck->status_device === 'normal') {
                    $dev->items()->update(['is_ok' => true]);
                }
            }
        }

        // 5. Update progress dan total_device
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

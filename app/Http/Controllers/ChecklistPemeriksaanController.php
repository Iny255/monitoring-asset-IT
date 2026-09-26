<?php

namespace App\Http\Controllers;

use App\Models\ChecklistJadwalRutin;
use App\Models\ChecklistRuangan;
use App\Models\ChecklistDevice;
use App\Models\ChecklistDeviceItem;
use App\Models\ChecklistItem;
use App\Models\Inventaris;
use App\Models\Lokasi;
use App\Models\Maping;
use App\Models\Peminjaman;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ChecklistPemeriksaanController extends Controller
{
    /**
     * Pelaksanaan Checklist Device Harian & Riwayat Pemeriksaan
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Parameter Filter
        $modeTanggal = $request->get('mode_tanggal', 'single'); // 'single' (per tanggal) atau 'all' (semua tanggal / filter hari)
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $hari = $request->get('hari'); // Opsi: 'senin', 'selasa', dst.
        $status = $request->get('status');
        $lokasiId = $request->get('id_lokasi');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $perusahaanId = $request->get('id_perusahaan', $request->get('perusahaan_id'));

        if (!$perusahaanId && $user->role !== 'super_admin') {
            $perusahaanId = $user->id_perusahaan;
        }

        // 1. Auto-generate checklist untuk tanggal yang dipilih jika mode single date
        if (!empty($tanggal) && $modeTanggal !== 'all') {
            $this->ensureChecklistGeneratedForDate($tanggal, $perusahaanId);

        }

        // 2. Auto-sync data checklist dengan status terkini di Mapping (misal device mutasi / pindah ruangan)
        $uncompletedRuangans = ChecklistRuangan::withoutGlobalScopes()
            ->where('status', '!=', 'selesai')
            ->when(!empty($tanggal) && $modeTanggal !== 'all', function ($q) use ($tanggal) {
                $q->where(function ($sub) use ($tanggal) {
                    $sub->whereDate('tanggal_pemeriksaan', $tanggal)
                        ->orWhereDate('tanggal_cek', $tanggal);
                });
            })
            ->when($perusahaanId, fn($q) => $q->where('id_perusahaan', $perusahaanId))
            ->get();

        foreach ($uncompletedRuangans as $r) {
            $r->syncDevicesWithMapping();
        }

        // Kalkulasi nama hari dan tanggal navigasi
        $carbonTanggal = !empty($tanggal) ? Carbon::parse($tanggal) : Carbon::today();
        $namaHariTanggal = $this->getNamaHariIndonesia($carbonTanggal);
        $prevDate = $carbonTanggal->copy()->subDay()->format('Y-m-d');
        $nextDate = $carbonTanggal->copy()->addDay()->format('Y-m-d');

        // Query Pelaksanaan Checklist
        $query = ChecklistRuangan::with([
            'jadwal.assignedTo.perusahaan',
            'jadwalRutin.assignedTo.perusahaan',
            'lokasi.perusahaan',
            'petugas.perusahaan',
            'perusahaan'
        ]);

        if ($user->role === 'super_admin') {
            if (!empty($perusahaanId)) {
                $query->where(function ($q) use ($perusahaanId) {
                    $q->where('id_perusahaan', $perusahaanId)
                      ->orWhereHas('lokasi', fn($lq) => $lq->where('id_perusahaan', $perusahaanId))
                      ->orWhereHas('jadwal', fn($jq) => $jq->where('id_perusahaan', $perusahaanId))
                      ->orWhereHas('jadwalRutin', fn($rq) => $rq->where('id_perusahaan', $perusahaanId));
                });
            }
        } else {
            $query->where('id_perusahaan', $user->id_perusahaan);
        }

        if (!empty($lokasiId)) {
            $query->where('id_lokasi', $lokasiId);
        }

        // Filter Berdasarkan Tanggal / Hari / Periode
        if ($modeTanggal === 'single' && !empty($tanggal) && empty($hari) && empty($bulan) && empty($tahun)) {
            // Mode default harian: Tampilkan khusus tanggal terpilih
            $query->where(function ($q) use ($tanggal) {
                $q->whereDate('tanggal_pemeriksaan', $tanggal)
                  ->orWhere(function ($sub) use ($tanggal) {
                      $sub->whereNull('tanggal_pemeriksaan')
                          ->whereDate('tanggal_cek', $tanggal);
                  });
            });
        } else {
            // Mode filter kustom (Hari, Tanggal spesifik, Bulan, Tahun)
            if (!empty($tanggal)) {
                $query->where(function ($q) use ($tanggal) {
                    $q->whereDate('tanggal_pemeriksaan', $tanggal)
                      ->orWhereDate('tanggal_cek', $tanggal);
                });
            }

            if (!empty($hari)) {
                $query->where(function ($q) use ($hari) {
                    $q->where('hari', strtolower($hari))
                      ->orWhereHas('jadwalRutin', fn($jq) => $jq->where('hari', strtolower($hari)));
                });
            }

            if (!empty($bulan)) {
                $query->where(function ($q) use ($bulan) {
                    $q->whereMonth('tanggal_pemeriksaan', $bulan)
                      ->orWhereMonth('tanggal_cek', $bulan)
                      ->orWhereHas('jadwal', fn($jq) => $jq->where('bulan', $bulan));
                });
            }

            if (!empty($tahun)) {
                $query->where(function ($q) use ($tahun) {
                    $q->whereYear('tanggal_pemeriksaan', $tahun)
                      ->orWhereYear('tanggal_cek', $tahun)
                      ->orWhereHas('jadwal', fn($jq) => $jq->where('tahun', $tahun));
                });
            }
        }

        // Stats Query (dihitung berdasarkan filter aktif kecuali status)
        $statQuery = clone $query;
        $stats = [
            'total' => (clone $statQuery)->count(),
            'selesai' => (clone $statQuery)->where('status', 'selesai')->count(),
            'sedang_dicek' => (clone $statQuery)->where('status', 'sedang_dicek')->count(),
            'belum_dicek' => (clone $statQuery)->where('status', 'belum_dicek')->count(),
        ];

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $ruangans = $query->orderBy('id_lokasi')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $lokasis = Lokasi::with('perusahaan')->orderBy('nama_lokasi')->get();
        $perusahaans = $user->role === 'super_admin' ? Perusahaan::orderBy('nama_perusahaan')->get() : collect();

        return view('content.dashboard.checklist.pemeriksaan.index', compact(
            'ruangans',
            'status',
            'lokasiId',
            'tanggal',
            'hari',
            'modeTanggal',
            'namaHariTanggal',
            'prevDate',
            'nextDate',
            'bulan',
            'tahun',
            'stats',
            'lokasis',
            'perusahaans',
            'perusahaanId'
        ));
    }

    /**
     * Otomatis inisiasi ChecklistRuangan + ChecklistDevice pada tanggal tertentu
     * berdasarkan template Master Jadwal Rutin Mingguan hari yang bersangkutan.
     */
    private function ensureChecklistGeneratedForDate($tanggal, $perusahaanId = null)
    {
        try {
            $carbon = Carbon::parse($tanggal);
            $namaHari = $this->getNamaHariIndonesia($carbon, true); // 'senin', 'selasa', etc.

            // Ambil seluruh jadwal rutin aktif di hari ini
            $rutinQuery = ChecklistJadwalRutin::where('hari', $namaHari)
                ->where('is_active', true);

            if (!empty($perusahaanId)) {
                $rutinQuery->where('id_perusahaan', $perusahaanId);
            }

            $activeRutins = $rutinQuery->get();

            if ($activeRutins->isEmpty()) {
                return;
            }

            // Ambil master item checklist aktif
            $defaultItems = ChecklistItem::where('is_active', true)->orderBy('urutan')->get();

            foreach ($activeRutins as $rutin) {
                // Cek apakah ChecklistRuangan untuk lokasi ini pada tanggal ini sudah ada
                $existing = ChecklistRuangan::withoutGlobalScopes()
                    ->where('id_lokasi', $rutin->id_lokasi)
                    ->where(function ($q) use ($tanggal) {
                        $q->whereDate('tanggal_pemeriksaan', $tanggal)
                          ->orWhere(function ($sub) use ($tanggal) {
                              $sub->whereNull('tanggal_pemeriksaan')
                                  ->whereDate('tanggal_cek', $tanggal);
                          });
                    })
                    ->first();

                if ($existing) {
                    continue; // Ruangan sudah terdaftar pada tanggal ini
                }

                DB::beginTransaction();

                $ruangan = ChecklistRuangan::create([
                    'jadwal_rutin_id' => $rutin->id,
                    'tanggal_pemeriksaan' => $tanggal,
                    'hari' => $namaHari,
                    'id_lokasi' => $rutin->id_lokasi,
                    'id_perusahaan' => $rutin->id_perusahaan,
                    'petugas_id' => $rutin->assigned_to,
                    'status' => 'belum_dicek',
                    'kondisi_ruangan' => 'semua_baik',
                    'total_device' => 0,
                    'total_checked' => 0,
                ]);

                // Ambil semua device mapping aktif di ruangan ini
                $activeMappings = Maping::withoutGlobalScopes()
                    ->where('id_lokasi', $rutin->id_lokasi)
                    ->where('status', 'aktif')
                    ->when($rutin->id_perusahaan, fn($q) => $q->where('id_perusahaan', $rutin->id_perusahaan))
                    ->with(['karyawan', 'keluar.inventaris.dataAset'])
                    ->get();

                // Ambil peminjaman aktif di ruangan ini
                $activeLoans = Peminjaman::where('status', 'Dipinjam')
                    ->where('id_lokasi', $rutin->id_lokasi)
                    ->whereHas('inventaris', function ($iq) use ($rutin) {
                        $iq->where('is_transfer', false)
                           ->when($rutin->id_perusahaan, fn($q) => $q->where('perusahaan_id', $rutin->id_perusahaan));
                    })
                    ->with(['inventaris.dataAset', 'karyawan', 'karyawanTujuan', 'perusahaanTujuan'])
                    ->get();

                $totalDevices = 0;
                $processedInventarisIds = [];

                // Filter master item sesuai perusahaan atau global
                $lokasiItems = $defaultItems->filter(function ($item) use ($rutin) {
                    return is_null($item->id_perusahaan) || $item->id_perusahaan == $rutin->id_perusahaan;
                });

                foreach ($activeMappings as $mapping) {
                    $inventaris = $mapping->keluar?->inventaris;
                    if (!$inventaris) {
                        continue;
                    }

                    $device = ChecklistDevice::create([
                        'checklist_ruangan_id' => $ruangan->id,
                        'maping_id' => $mapping->id,
                        'peminjaman_id' => null,
                        'inventaris_id' => $inventaris->id,
                        'nama_pengguna' => $mapping->penerima,
                        'status_device' => 'belum_dicek',
                    ]);

                    foreach ($lokasiItems as $item) {
                        ChecklistDeviceItem::create([
                            'checklist_device_id' => $device->id,
                            'nama_item' => $item->nama_item,
                            'kategori_item' => $item->kategori,
                            'is_ok' => true,
                        ]);
                    }

                    $processedInventarisIds[] = $inventaris->id;
                    $totalDevices++;
                }

                foreach ($activeLoans as $loan) {
                    $inventaris = $loan->inventaris;
                    if (!$inventaris || in_array($inventaris->id, $processedInventarisIds)) {
                        continue;
                    }

                    $device = ChecklistDevice::create([
                        'checklist_ruangan_id' => $ruangan->id,
                        'maping_id' => null,
                        'peminjaman_id' => $loan->id,
                        'inventaris_id' => $inventaris->id,
                        'nama_pengguna' => '[Pinjaman] ' . $loan->peminjam_nama,
                        'status_device' => 'belum_dicek',
                    ]);

                    foreach ($lokasiItems as $item) {
                        ChecklistDeviceItem::create([
                            'checklist_device_id' => $device->id,
                            'nama_item' => $item->nama_item,
                            'kategori_item' => $item->kategori,
                            'is_ok' => true,
                        ]);
                    }

                    $processedInventarisIds[] = $inventaris->id;
                    $totalDevices++;
                }

                $ruangan->update([
                    'total_device' => $totalDevices,
                ]);

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checklist auto-generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Helper nama hari bahasa Indonesia
     */
    private function getNamaHariIndonesia(Carbon $date, $lowercase = false)
    {
        $dayNum = (int) $date->format('N'); // 1 = Senin, ..., 7 = Minggu
        $map = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        $res = $map[$dayNum] ?? 'Senin';
        return $lowercase ? strtolower($res) : $res;
    }

    public function show($id)
    {
        $ruangan = ChecklistRuangan::with([
            'jadwal.assignedTo.perusahaan',
            'jadwalRutin.assignedTo.perusahaan',
            'lokasi.perusahaan',
            'petugas.perusahaan',
            'checklistDevices.inventaris.dataAset.kategori',
            'checklistDevices.maping.karyawan',
            'checklistDevices.peminjaman.karyawan',
            'checklistDevices.peminjaman.perusahaanTujuan',
            'checklistDevices.checkedBy',
            'checklistDevices.items',
            'perusahaan'
        ])->findOrFail($id);

        // Auto-sync data checklist ruangan dengan status terkini di Mapping & Peminjaman
        $ruangan->syncDevicesWithMapping();
        $ruangan->load([
            'checklistDevices.inventaris.dataAset.kategori',
            'checklistDevices.maping.karyawan',
            'checklistDevices.peminjaman.karyawan',
            'checklistDevices.peminjaman.perusahaanTujuan',
            'checklistDevices.checkedBy',
            'checklistDevices.items'
        ]);

        // Auto-sync jika ada item baru di Master Item Cek yang belum masuk ke device yang sudah terbuat (sesuai perusahaan)
        $activeMasterItems = ChecklistItem::where('is_active', true)
            ->where(function ($q) use ($ruangan) {
                $q->whereNull('id_perusahaan')
                  ->orWhere('id_perusahaan', $ruangan->id_perusahaan);
            })
            ->orderBy('urutan')
            ->get();

        if ($activeMasterItems->isNotEmpty()) {
            $hasNewItemAdded = false;
            foreach ($ruangan->checklistDevices as $device) {
                $existingItemNames = $device->items->pluck('nama_item')->toArray();
                foreach ($activeMasterItems as $masterItem) {
                    if (!in_array($masterItem->nama_item, $existingItemNames)) {
                        ChecklistDeviceItem::create([
                            'checklist_device_id' => $device->id,
                            'nama_item' => $masterItem->nama_item,
                            'kategori_item' => $masterItem->kategori,
                            'is_ok' => true,
                        ]);
                        $hasNewItemAdded = true;
                    }
                }
            }
            if ($hasNewItemAdded) {
                $ruangan->load('checklistDevices.items');
            }
        }

        return view('content.dashboard.checklist.pemeriksaan.show', compact('ruangan', 'activeMasterItems'));
    }

    public function markAllOk(Request $request, $id)
    {
        $ruangan = ChecklistRuangan::with('checklistDevices.items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Jika seluruh device sudah normal, tombol ini berfungsi untuk membatalkan semua (reset)
            $allNormal = $ruangan->checklistDevices->every(fn($d) => $d->status_device === 'normal');

            if ($allNormal) {
                foreach ($ruangan->checklistDevices as $device) {
                    $device->update([
                        'status_device' => 'belum_dicek',
                        'catatan_kendala' => null,
                        'checked_at' => null,
                        'checked_by' => null,
                    ]);
                    $device->items()->update(['is_ok' => false]);
                }

                $ruangan->updateProgress();
                DB::commit();

                $pesan = 'Status normal seluruh perangkat di lokasi ini berhasil dibatalkan (kembali Belum Dicek).';

                if ($request->ajax() || $request->wantsJson()) {
                    $devicesData = [];
                    foreach ($ruangan->checklistDevices as $d) {
                        $devicesData[$d->id] = $this->formatDevicePayload($d, $ruangan);
                    }
                    return response()->json([
                        'success' => true,
                        'message' => $pesan,
                        'action' => 'reset_all',
                        'devices' => $devicesData,
                        'ruangan' => $this->formatRuanganPayload($ruangan),
                    ]);
                }

                return redirect()->route('checklist.pemeriksaan.show', $id)
                    ->with('success', $pesan);
            }

            foreach ($ruangan->checklistDevices as $device) {
                $device->update([
                    'status_device' => 'normal',
                    'catatan_kendala' => null,
                    'checked_at' => Carbon::now('Asia/Jakarta'),
                    'checked_by' => auth()->id(),
                ]);

                // Set semua item is_ok = true
                $device->items()->update(['is_ok' => true]);
            }

            $ruangan->petugas_id = auth()->id();
            $ruangan->tanggal_cek = Carbon::now('Asia/Jakarta');
            $ruangan->updateProgress();

            DB::commit();

            $pesan = 'Semua perangkat device pada lokasi ini berhasil ditandai NORMAL (Selesai).';

            if ($request->ajax() || $request->wantsJson()) {
                $devicesData = [];
                foreach ($ruangan->checklistDevices as $d) {
                    $devicesData[$d->id] = $this->formatDevicePayload($d, $ruangan);
                }
                return response()->json([
                    'success' => true,
                    'message' => $pesan,
                    'action' => 'mark_all_normal',
                    'devices' => $devicesData,
                    'ruangan' => $this->formatRuanganPayload($ruangan),
                ]);
            }

            return redirect()->route('checklist.pemeriksaan.show', $id)
                ->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function markDeviceOk(Request $request, $ruanganId, $deviceId)
    {
        $ruangan = ChecklistRuangan::findOrFail($ruanganId);
        $device = ChecklistDevice::where('checklist_ruangan_id', $ruangan->id)->findOrFail($deviceId);

        DB::beginTransaction();
        try {
            if ($device->status_device === 'normal') {
                // TOGGLE / UNDO: Batalkan status normal kembali ke belum_dicek
                $device->update([
                    'status_device' => 'belum_dicek',
                    'catatan_kendala' => null,
                    'checked_at' => null,
                    'checked_by' => null,
                ]);

                // Reset semua item checklist is_ok = false
                $device->items()->update(['is_ok' => false]);

                $ruangan->updateProgress();
                DB::commit();

                $pesan = "Status normal pada perangkat {$device->inventaris->kode_aset} berhasil dibatalkan.";

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $pesan,
                        'action' => 'reset',
                        'device' => $this->formatDevicePayload($device, $ruangan),
                        'ruangan' => $this->formatRuanganPayload($ruangan),
                    ]);
                }

                return redirect()->route('checklist.pemeriksaan.show', $ruanganId)
                    ->with('success', $pesan);
            }

            // Tandai Normal
            $device->update([
                'status_device' => 'normal',
                'catatan_kendala' => null,
                'checked_at' => Carbon::now('Asia/Jakarta'),
                'checked_by' => auth()->id(),
            ]);

            $device->items()->update(['is_ok' => true]);

            $ruangan->petugas_id = auth()->id();
            $ruangan->updateProgress();

            DB::commit();

            $pesan = "Perangkat {$device->inventaris->kode_aset} ditandai NORMAL.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $pesan,
                    'action' => 'mark_normal',
                    'device' => $this->formatDevicePayload($device, $ruangan),
                    'ruangan' => $this->formatRuanganPayload($ruangan),
                ]);
            }

            return redirect()->route('checklist.pemeriksaan.show', $ruanganId)
                ->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function updateDevice(Request $request, $ruanganId, $deviceId)
    {
        $ruangan = ChecklistRuangan::findOrFail($ruanganId);
        $device = ChecklistDevice::where('checklist_ruangan_id', $ruangan->id)->findOrFail($deviceId);

        $request->validate([
            'status_device' => 'required|in:normal,ada_kendala,belum_dicek',
            'catatan_kendala' => 'nullable|string|max:500',
            'items' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $status = $request->status_device;
            $checkedAt = ($status === 'belum_dicek') ? null : Carbon::now('Asia/Jakarta');
            $checkedBy = ($status === 'belum_dicek') ? null : auth()->id();
            $catatan = ($status === 'belum_dicek') ? null : $request->catatan_kendala;

            $device->update([
                'status_device' => $status,
                'catatan_kendala' => $catatan,
                'checked_at' => $checkedAt,
                'checked_by' => $checkedBy,
            ]);

            // Update item checkboxes
            $submittedItems = $request->input('items', []);
            foreach ($device->items as $item) {
                if ($status === 'belum_dicek') {
                    $item->update(['is_ok' => false]);
                } else {
                    $isOk = isset($submittedItems[$item->id]) && $submittedItems[$item->id] == '1';
                    $item->update(['is_ok' => $isOk]);
                }
            }

            if ($status !== 'belum_dicek') {
                $ruangan->petugas_id = auth()->id();
            }
            $ruangan->updateProgress();

            DB::commit();

            $pesan = ($status === 'belum_dicek') 
                ? "Pemeriksaan {$device->inventaris->kode_aset} berhasil dibatalkan (Belum Dicek)." 
                : "Pengecekan {$device->inventaris->kode_aset} berhasil disimpan.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $pesan,
                    'action' => 'update',
                    'device' => $this->formatDevicePayload($device, $ruangan),
                    'ruangan' => $this->formatRuanganPayload($ruangan),
                ]);
            }

            return redirect()->route('checklist.pemeriksaan.show', $ruanganId)
                ->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint Sinkronisasi Realtime Status Ruangan & Devices
     */
    public function syncStatus($id)
    {
        $ruangan = ChecklistRuangan::withoutGlobalScopes()->findOrFail($id);
        $ruangan->syncDevicesWithMapping();
        $ruangan->load([
            'petugas',
            'checklistDevices.inventaris.dataAset.kategori',
            'checklistDevices.maping.karyawan',
            'checklistDevices.checkedBy',
            'checklistDevices.items'
        ]);

        $devicesData = [];
        foreach ($ruangan->checklistDevices as $d) {
            $devicesData[$d->id] = $this->formatDevicePayload($d, $ruangan);
        }

        return response()->json([
            'success' => true,
            'ruangan' => $this->formatRuanganPayload($ruangan),
            'devices' => $devicesData,
        ]);
    }

    /**
     * Format detail perangkat untuk respon JSON realtime
     */
    private function formatDevicePayload(ChecklistDevice $device, ChecklistRuangan $ruangan): array
    {
        $device->loadMissing(['inventaris.dataAset.kategori', 'maping.karyawan', 'peminjaman.karyawan', 'peminjaman.perusahaanTujuan', 'checkedBy', 'items']);
        $ruangan->refresh();

        $qrUrl = null;
        $qrSvg = null;
        $mapping = $device->maping ?? $device->resolved_maping;
        $qrUrl = null;
        $qrSvg = null;
        $linkLabel = 'Buka Informasi Aset';

        if ($mapping) {
            $qrUrl = route('maping.public_show', ['id' => $mapping->uuid ?? $mapping->id, 'ruangan_id' => $ruangan->id]);
            $linkLabel = 'Buka Informasi Mapping & Pengecekan';
        } elseif ($device->peminjaman_id) {
            $qrUrl = route('peminjaman.show', ['id' => $device->peminjaman_id, 'ruangan_id' => $ruangan->id]);
            $linkLabel = 'Buka Informasi Peminjaman Aset';
        } elseif ($device->inventaris) {
            $qrUrl = route('history.perjalanan.dokumen_perawatan.cetak', ['id' => $device->inventaris->id, 'tahun' => date('Y')]);
            $linkLabel = 'Buka Form Perawatan Aset';
        }

        if ($qrUrl) {
            try {
                $qrSvg = (string) QrCode::size(160)->generate($qrUrl);
            } catch (\Exception $e) {
                $qrSvg = null;
            }
        }

        $itemsDetail = $device->items->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_item' => $item->nama_item,
                'kategori_item' => $item->kategori_item,
                'is_ok' => (bool) $item->is_ok,
            ];
        });

        $failedItems = $device->items->where('is_ok', false)->pluck('nama_item')->values()->toArray();

        $dataAset = $device->inventaris?->dataAset;
        $jenisAset = $dataAset?->kategori?->nama_barang ?? ($dataAset?->kategori?->nama_kategori ?? 'Perangkat IT');
        $spekAset = trim(($dataAset?->merek ?? '') . ' ' . ($dataAset?->type ?? '') . ' ' . ($dataAset?->warna ?? ''));

        return [
            'id' => $device->id,
            'kode_aset' => $device->inventaris->kode_aset ?? '-',
            'no_inventaris' => $device->inventaris->no_inventaris ?? null,
            'nama_aset' => $jenisAset,
            'spek_aset' => $spekAset ?: '-',
            'kategori' => $jenisAset,
            'nama_pengguna' => $device->nama_pengguna ?? ($device->maping->penerima ?? '-'),
            'status_device' => $device->status_device,
            'catatan_kendala' => $device->catatan_kendala,
            'checked_at' => $device->checked_at ? $device->checked_at->format('d/m/Y H:i') : null,
            'checked_at_human' => $device->checked_at ? $device->checked_at->diffForHumans() : null,
            'checked_by_id' => $device->checked_by,
            'checked_by_name' => $device->checkedBy?->name ?? ($device->checked_at ? (auth()->user()?->name ?? 'Petugas IT') : null),
            'qr_url' => $qrUrl,
            'qr_svg' => $qrSvg,
            'qr_base64' => !empty($qrSvg) ? base64_encode($qrSvg) : null,
            'is_pinjaman' => (bool) $device->is_pinjaman,
            'peminjaman_id' => $device->peminjaman_id,
            'mapping_id' => $mapping?->id,
            'mapping_uuid' => $mapping?->uuid,
            'link_label' => $linkLabel,
            'items' => $itemsDetail,
            'failed_items' => $failedItems,
            'has_kendala' => $device->status_device === 'ada_kendala',
            'is_checked' => in_array($device->status_device, ['normal', 'ada_kendala']),
        ];
    }

    /**
     * Format data status ruangan untuk respon JSON realtime
     */
    private function formatRuanganPayload(ChecklistRuangan $ruangan): array
    {
        $ruangan->refresh();
        return [
            'id' => $ruangan->id,
            'status' => $ruangan->status,
            'kondisi_ruangan' => $ruangan->kondisi_ruangan,
            'total_device' => (int) $ruangan->total_device,
            'total_checked' => (int) $ruangan->total_checked,
            'persentase' => (int) $ruangan->persentase,
            'petugas_name' => $ruangan->petugas?->name ?? (auth()->user()?->name ?? 'Petugas IT'),
            'tanggal_cek' => $ruangan->tanggal_cek ? $ruangan->tanggal_cek->format('d/m/Y H:i') : null,
        ];
    }

    public function cetak($id)
    {
        $ruangan = ChecklistRuangan::withoutGlobalScopes()->with([
            'jadwal.assignedTo',
            'jadwalRutin.assignedTo',
            'lokasi',
            'petugas',
            'checklistDevices.inventaris.dataAset.kategori',
            'checklistDevices.maping.karyawan',
            'checklistDevices.peminjaman.karyawan',
            'checklistDevices.peminjaman.perusahaanTujuan',
            'checklistDevices.checkedBy',
            'checklistDevices.items',
            'perusahaan'
        ])->findOrFail($id);

        return view('content.dashboard.checklist.pemeriksaan.cetak', compact('ruangan'));
    }

    /**
     * Cari identitas perangkat dari hasil scan QR/Barcode (Cek sesi saat ini, mapping, atau pinjaman).
     */
    public function scanLookup(Request $request, $id)
    {
        $ruangan = ChecklistRuangan::with(['checklistDevices.inventaris'])->findOrFail($id);
        $rawInput = trim($request->input('code', ''));

        if (empty($rawInput)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode atau QR barcode tidak boleh kosong.',
            ], 422);
        }

        // Jika input berupa URL, ambil identifier di bagian akhir path
        $identifier = $rawInput;
        if (filter_var($rawInput, FILTER_VALIDATE_URL) || str_contains($rawInput, '/')) {
            $parsedPath = parse_url($rawInput, PHP_URL_PATH);
            if ($parsedPath) {
                $segments = array_values(array_filter(explode('/', $parsedPath)));
                $identifier = end($segments) ?: $rawInput;
            }
        }
        $identifier = trim($identifier);

        // 1. Cek apakah perangkat SUDAH ADA di sesi ruangan ini
        $existingDeviceInRoom = $ruangan->checklistDevices->first(function ($device) use ($identifier, $rawInput) {
            $inv = $device->inventaris;
            $kode = strtolower($inv?->kode_aset ?? '');
            $noInv = strtolower($inv?->no_inventaris ?? '');
            $devMaping = $device->maping ?? $device->resolved_maping;
            $mapUuid = strtolower($devMaping?->uuid ?? '');
            $mapId = (string) ($devMaping?->id ?? '');
            $pjmId = (string) ($device->peminjaman_id ?? '');
            $pjmKode = strtolower($device->peminjaman?->kode_peminjaman ?? '');

            $targetIdent = strtolower($identifier);
            $targetRaw = strtolower($rawInput);

            return ($kode && ($kode === $targetIdent || str_contains($targetRaw, $kode)))
                || ($noInv && ($noInv === $targetIdent || str_contains($targetRaw, $noInv)))
                || ($mapUuid && ($mapUuid === $targetIdent || str_contains($targetRaw, $mapUuid)))
                || ($mapId && ($mapId === $targetIdent || str_contains($targetRaw, "/maping/{$mapId}") || str_contains($targetRaw, "/{$mapId}")))
                || ($pjmKode && ($pjmKode === $targetIdent || str_contains($targetRaw, $pjmKode)))
                || ($pjmId && ($pjmId === $targetIdent || str_contains($targetRaw, "/peminjaman/{$pjmId}")));
        });

        if ($existingDeviceInRoom) {
            return response()->json([
                'success' => true,
                'already_in_room' => true,
                'can_add' => false,
                'device_id' => $existingDeviceInRoom->id,
                'kode_aset' => $existingDeviceInRoom->inventaris?->kode_aset ?? '-',
                'message' => 'Perangkat sudah terdaftar di sesi checklist ruangan ini.',
            ]);
        }

        // 2. Jika belum ada di ruangan ini, cari di database sistem:
        // 2a. Cari di Mapping
        $mapping = Maping::withoutGlobalScopes()->with(['keluar.inventaris.dataAset.kategori', 'lokasi', 'perusahaan'])
            ->where(function ($q) use ($identifier, $rawInput) {
                if (Str::isUuid($identifier)) {
                    $q->where('uuid', $identifier);
                } else {
                    $q->where('id', $identifier)->orWhere('uuid', $identifier);
                }
                $q->orWhereHas('keluar.inventaris', function ($iq) use ($identifier, $rawInput) {
                    $iq->where('kode_aset', $identifier)
                       ->orWhere('no_inventaris', $identifier)
                       ->orWhere('kode_aset', $rawInput)
                       ->orWhere('no_inventaris', $rawInput);
                });
            })
            ->first();

        // 2b. Cari di Peminjaman
        $loan = Peminjaman::with(['inventaris.dataAset.kategori', 'karyawan', 'karyawanTujuan', 'perusahaanTujuan', 'lokasi'])
            ->where(function ($q) use ($identifier, $rawInput) {
                $q->where('kode_peminjaman', $identifier)
                  ->orWhere('kode_peminjaman', $rawInput)
                  ->orWhere('id', $identifier);
            })
            ->orWhereHas('inventaris', function ($iq) use ($identifier, $rawInput) {
                $iq->where('kode_aset', $identifier)
                   ->orWhere('no_inventaris', $identifier)
                   ->orWhere('kode_aset', $rawInput)
                   ->orWhere('no_inventaris', $rawInput);
            })
            ->latest('id')
            ->first();

        // 2c. Cari di Inventaris langsung jika belum ketemu
        $inventaris = $mapping?->keluar?->inventaris ?? $loan?->inventaris;
        if (!$inventaris) {
            $inventaris = Inventaris::with('dataAset.kategori')
                ->where('kode_aset', $identifier)
                ->orWhere('no_inventaris', $identifier)
                ->orWhere('kode_aset', $rawInput)
                ->orWhere('no_inventaris', $rawInput)
                ->orWhere('id', $identifier)
                ->first();
        }

        if (!$inventaris) {
            return response()->json([
                'success' => false,
                'already_in_room' => false,
                'can_add' => false,
                'message' => 'Data perangkat tidak ditemukan di database sistem aset.',
            ], 404);
        }

        // Cek lagi apakah inventaris_id sudah ada di sesi ruangan ini
        $dupDevice = $ruangan->checklistDevices()->where('inventaris_id', $inventaris->id)->first();
        if ($dupDevice) {
            return response()->json([
                'success' => true,
                'already_in_room' => true,
                'can_add' => false,
                'device_id' => $dupDevice->id,
                'kode_aset' => $inventaris->kode_aset ?? '-',
                'message' => 'Perangkat sudah terdaftar di sesi checklist ruangan ini.',
            ]);
        }

        // Cek pinjaman aktif untuk inventaris ini
        $activeLoan = $loan && in_array(strtolower($loan->status), ['dipinjam', 'menunggu_pengembalian'])
            ? $loan
            : Peminjaman::with(['karyawan', 'karyawanTujuan', 'perusahaanTujuan', 'lokasi'])
                ->where('inventaris_id', $inventaris->id)
                ->whereIn('status', ['dipinjam', 'Dipinjam', 'menunggu_pengembalian'])
                ->latest('id')
                ->first();

        if (!$mapping) {
            $mapping = Maping::withoutGlobalScopes()->with(['lokasi', 'perusahaan'])
                ->whereHas('keluar', fn($q) => $q->where('inventaris_id', $inventaris->id))
                ->latest('id')
                ->first();
        }

        $dataAset = $inventaris->dataAset;
        $jenisAset = $dataAset?->kategori?->nama_barang ?? ($dataAset?->kategori?->nama_kategori ?? 'Perangkat IT');
        $merekType = trim(($dataAset?->merek ?? '') . ' ' . ($dataAset?->type ?? ''));

        $statusTeks = $activeLoan
            ? "Perangkat Pinjaman (Peminjam: {$activeLoan->peminjam_nama})"
            : ($mapping ? "Mapping Asal: " . ($mapping->lokasi?->nama_lokasi ?? '-') : 'Inventaris Perusahaan');

        return response()->json([
            'success' => true,
            'already_in_room' => false,
            'can_add' => true,
            'device_info' => [
                'inventaris_id' => $inventaris->id,
                'maping_id' => $mapping?->id,
                'peminjaman_id' => $activeLoan?->id,
                'kode_aset' => $inventaris->kode_aset ?? '-',
                'no_inventaris' => $inventaris->no_inventaris ?? null,
                'nama_barang' => $jenisAset,
                'merek_type' => $merekType,
                'is_loan' => (bool) $activeLoan,
                'peminjam' => $activeLoan ? $activeLoan->peminjam_nama : null,
                'lokasi_asal' => $mapping?->lokasi?->nama_lokasi ?? 'Tidak Terpetakan',
                'status_teks' => $statusTeks,
            ],
            'message' => 'Perangkat ditemukan di sistem dan dapat ditambahkan ke ruangan ini.',
        ]);
    }

    /**
     * Tambahkan perangkat hasil scan barcode ke sesi checklist ruangan saat ini.
     */
    public function addScannedDevice(Request $request, $id)
    {
        $ruangan = ChecklistRuangan::findOrFail($id);
        $inventarisId = $request->input('inventaris_id');

        if (!$inventarisId) {
            return response()->json(['success' => false, 'message' => 'ID inventaris tidak valid.'], 422);
        }

        // Cek jika sudah terdaftar
        $existing = $ruangan->checklistDevices()->where('inventaris_id', $inventarisId)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Perangkat sudah ada di sesi ruangan ini.',
                'device_id' => $existing->id,
            ]);
        }

        $inventaris = Inventaris::with('dataAset')->findOrFail($inventarisId);
        $peminjamanId = $request->input('peminjaman_id');
        $mapingId = $request->input('maping_id');

        // Jika tidak dikirim peminjamanId, cek apakah ada pinjaman aktif
        if (!$peminjamanId) {
            $activeLoan = Peminjaman::where('inventaris_id', $inventarisId)
                ->whereIn('status', ['dipinjam', 'Dipinjam', 'menunggu_pengembalian'])
                ->latest('id')
                ->first();
            if ($activeLoan) {
                $peminjamanId = $activeLoan->id;
            }
        }

        // Jika tidak dikirim mapingId dan bukan pinjaman, cari mapping aktifnya
        if (!$mapingId && !$peminjamanId) {
            $mapping = Maping::withoutGlobalScopes()
                ->whereHas('keluar', fn($q) => $q->where('inventaris_id', $inventarisId))
                ->latest('id')
                ->first();
            if ($mapping) {
                $mapingId = $mapping->id;
            }
        }

        $namaPengguna = '-';
        if ($peminjamanId) {
            $loan = Peminjaman::find($peminjamanId);
            $namaPengguna = $loan ? "[Pinjaman] {$loan->peminjam_nama}" : '[Pinjaman]';
            // Perbarui lokasi pinjaman ke ruangan ini agar sinkron
            if ($loan && $ruangan->id_lokasi) {
                $loan->id_lokasi = $ruangan->id_lokasi;
                $loan->save();
            }
        } elseif ($mapingId) {
            $m = Maping::withoutGlobalScopes()->find($mapingId);
            $namaPengguna = $m?->penerima ?? '-';
        }

        DB::beginTransaction();
        try {
            $device = ChecklistDevice::create([
                'checklist_ruangan_id' => $ruangan->id,
                'maping_id' => $mapingId,
                'peminjaman_id' => $peminjamanId,
                'inventaris_id' => $inventaris->id,
                'nama_pengguna' => $namaPengguna,
                'status_device' => 'belum_dicek',
            ]);

            // Tambahkan master item aktif
            $masterItems = ChecklistItem::where('is_active', true)
                ->where(function ($q) use ($ruangan) {
                    $q->whereNull('id_perusahaan')
                      ->orWhere('id_perusahaan', $ruangan->id_perusahaan);
                })
                ->orderBy('urutan')
                ->get();

            foreach ($masterItems as $item) {
                ChecklistDeviceItem::create([
                    'checklist_device_id' => $device->id,
                    'nama_item' => $item->nama_item,
                    'kategori_item' => $item->kategori,
                    'is_ok' => true,
                ]);
            }

            $ruangan->updateProgress();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perangkat berhasil ditambahkan ke sesi ruangan ini!',
                'device_id' => $device->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding scanned device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan perangkat: ' . $e->getMessage(),
            ], 500);
        }
    }
}

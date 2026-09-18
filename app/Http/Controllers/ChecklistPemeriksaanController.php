<?php

namespace App\Http\Controllers;

use App\Models\ChecklistRuangan;
use App\Models\ChecklistDevice;
use App\Models\ChecklistDeviceItem;
use App\Models\ChecklistItem;
use App\Models\Lokasi;
use App\Models\Maping;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistPemeriksaanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $status = $request->get('status');
        $lokasiId = $request->get('id_lokasi');
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        $perusahaanId = $request->get('id_perusahaan', $request->get('perusahaan_id'));

        $query = ChecklistRuangan::with([
            'jadwal.assignedTo.perusahaan',
            'lokasi.perusahaan',
            'petugas.perusahaan',
            'perusahaan'
        ])
        ->whereHas('jadwal', function ($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun);
            if (!empty($bulan)) {
                $q->where('bulan', $bulan);
            }
        });

        if ($user->role === 'super_admin') {
            if (!empty($perusahaanId)) {
                $query->where(function ($q) use ($perusahaanId) {
                    $q->where('id_perusahaan', $perusahaanId)
                      ->orWhereHas('lokasi', fn($lq) => $lq->where('id_perusahaan', $perusahaanId))
                      ->orWhereHas('jadwal', fn($jq) => $jq->where('id_perusahaan', $perusahaanId));
                });
            }
        } else {
            $query->where('id_perusahaan', $user->id_perusahaan);
        }

        if (!empty($lokasiId)) {
            $query->where('id_lokasi', $lokasiId);
        }

        // Stats Query (tidak dipengaruhi filter status agar angka stat card akurat)
        $statQuery = ChecklistRuangan::whereHas('jadwal', function ($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun);
            if (!empty($bulan)) {
                $q->where('bulan', $bulan);
            }
        });

        if ($user->role === 'super_admin') {
            if (!empty($perusahaanId)) {
                $statQuery->where(function ($q) use ($perusahaanId) {
                    $q->where('id_perusahaan', $perusahaanId)
                      ->orWhereHas('lokasi', fn($lq) => $lq->where('id_perusahaan', $perusahaanId))
                      ->orWhereHas('jadwal', fn($jq) => $jq->where('id_perusahaan', $perusahaanId));
                });
            }
        } else {
            $statQuery->where('id_perusahaan', $user->id_perusahaan);
        }

        if (!empty($lokasiId)) {
            $statQuery->where('id_lokasi', $lokasiId);
        }

        $stats = [
            'total' => (clone $statQuery)->count(),
            'selesai' => (clone $statQuery)->where('status', 'selesai')->count(),
            'sedang_dicek' => (clone $statQuery)->where('status', 'sedang_dicek')->count(),
            'belum_dicek' => (clone $statQuery)->where('status', 'belum_dicek')->count(),
        ];

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $ruangans = $query->latest('id')->paginate(12)->withQueryString();

        $lokasis = Lokasi::with('perusahaan')->orderBy('nama_lokasi')->get();

        $perusahaans = $user->role === 'super_admin' ? Perusahaan::orderBy('nama_perusahaan')->get() : collect();

        return view('content.dashboard.checklist.pemeriksaan.index', compact(
            'ruangans',
            'status',
            'lokasiId',
            'bulan',
            'tahun',
            'stats',
            'lokasis',
            'perusahaans',
            'perusahaanId'
        ));
    }

    public function show($id)
    {
        $ruangan = ChecklistRuangan::with([
            'jadwal.assignedTo.perusahaan',
            'lokasi.perusahaan',
            'petugas.perusahaan',
            'checklistDevices.inventaris.dataAset.kategori',
            'checklistDevices.maping.karyawan',
            'checklistDevices.items',
            'perusahaan'
        ])->findOrFail($id);

        // Auto-sync jika ada mapping aktif baru di ruangan ini yang belum terdaftar di checklist
        $existingMappingIds = $ruangan->checklistDevices->pluck('maping_id')->filter()->toArray();
        $newActiveMappings = Maping::withoutGlobalScopes()
            ->where('id_lokasi', $ruangan->id_lokasi)
            ->where('status', 'aktif')
            ->when($ruangan->id_perusahaan, fn($q) => $q->where('id_perusahaan', $ruangan->id_perusahaan))
            ->whereNotIn('id', $existingMappingIds)
            ->with(['karyawan', 'keluar.inventaris.dataAset'])
            ->get();

        if ($newActiveMappings->isNotEmpty()) {
            $defaultItems = ChecklistItem::where('is_active', true)
                ->where(function ($q) use ($ruangan) {
                    $q->whereNull('id_perusahaan')
                      ->orWhere('id_perusahaan', $ruangan->id_perusahaan);
                })
                ->orderBy('urutan')
                ->get();

            foreach ($newActiveMappings as $mapping) {
                $inventaris = $mapping->keluar?->inventaris;
                if (!$inventaris) {
                    continue;
                }

                $device = ChecklistDevice::create([
                    'checklist_ruangan_id' => $ruangan->id,
                    'maping_id' => $mapping->id,
                    'inventaris_id' => $inventaris->id,
                    'nama_pengguna' => $mapping->penerima,
                    'status_device' => 'belum_dicek',
                ]);

                foreach ($defaultItems as $item) {
                    ChecklistDeviceItem::create([
                        'checklist_device_id' => $device->id,
                        'nama_item' => $item->nama_item,
                        'kategori_item' => $item->kategori,
                        'is_ok' => true,
                    ]);
                }
            }

            $ruangan->updateProgress();
            $ruangan->load('checklistDevices.inventaris.dataAset.kategori', 'checklistDevices.maping.karyawan', 'checklistDevices.items');
        }

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

    public function markAllOk($id)
    {
        $ruangan = ChecklistRuangan::with('checklistDevices.items')->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($ruangan->checklistDevices as $device) {
                $device->update([
                    'status_device' => 'normal',
                    'catatan_kendala' => null,
                    'checked_at' => now(),
                ]);

                // Set semua item is_ok = true
                $device->items()->update(['is_ok' => true]);
            }

            $ruangan->petugas_id = auth()->id();
            $ruangan->tanggal_cek = now();
            $ruangan->updateProgress();

            DB::commit();

            return redirect()->route('checklist.pemeriksaan.show', $id)
                ->with('success', 'Semua perangkat device pada lokasi ini berhasil ditandai NORMAL (Selesai).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function markDeviceOk($ruanganId, $deviceId)
    {
        $ruangan = ChecklistRuangan::findOrFail($ruanganId);
        $device = ChecklistDevice::where('checklist_ruangan_id', $ruangan->id)->findOrFail($deviceId);

        DB::beginTransaction();
        try {
            $device->update([
                'status_device' => 'normal',
                'catatan_kendala' => null,
                'checked_at' => now(),
            ]);

            $device->items()->update(['is_ok' => true]);

            $ruangan->petugas_id = auth()->id();
            $ruangan->updateProgress();

            DB::commit();

            return redirect()->route('checklist.pemeriksaan.show', $ruanganId)
                ->with('success', "Perangkat {$device->inventaris->kode_aset} ditandai NORMAL.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function updateDevice(Request $request, $ruanganId, $deviceId)
    {
        $ruangan = ChecklistRuangan::findOrFail($ruanganId);
        $device = ChecklistDevice::where('checklist_ruangan_id', $ruangan->id)->findOrFail($deviceId);

        $request->validate([
            'status_device' => 'required|in:normal,ada_kendala',
            'catatan_kendala' => 'nullable|string|max:500',
            'items' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $device->update([
                'status_device' => $request->status_device,
                'catatan_kendala' => $request->catatan_kendala,
                'checked_at' => now(),
            ]);

            // Update item checkboxes
            $submittedItems = $request->input('items', []);
            foreach ($device->items as $item) {
                $isOk = isset($submittedItems[$item->id]) && $submittedItems[$item->id] == '1';
                $item->update(['is_ok' => $isOk]);
            }

            $ruangan->petugas_id = auth()->id();
            $ruangan->updateProgress();

            DB::commit();

            return redirect()->route('checklist.pemeriksaan.show', $ruanganId)
                ->with('success', "Pengecekan {$device->inventaris->kode_aset} berhasil disimpan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function cetak($id)
    {
        $ruangan = ChecklistRuangan::withoutGlobalScopes()->with([
            'jadwal.assignedTo',
            'lokasi',
            'petugas',
            'checklistDevices.inventaris.dataAset.kategori',
            'checklistDevices.maping.karyawan',
            'checklistDevices.items',
            'perusahaan'
        ])->findOrFail($id);

        return view('content.dashboard.checklist.pemeriksaan.cetak', compact('ruangan'));
    }
}

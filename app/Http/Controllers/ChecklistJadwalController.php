<?php

namespace App\Http\Controllers;

use App\Models\ChecklistJadwal;
use App\Models\ChecklistRuangan;
use App\Models\ChecklistDevice;
use App\Models\ChecklistDeviceItem;
use App\Models\ChecklistItem;
use App\Models\Lokasi;
use App\Models\Maping;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChecklistJadwalController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('n'));
        $lokasiId = $request->get('id_lokasi');
        $status = $request->get('status');
        $perusahaanId = $request->get('id_perusahaan', $request->get('perusahaan_id'));

        $query = ChecklistJadwal::with(['lokasi.perusahaan', 'assignedTo.perusahaan', 'perusahaan', 'checklistRuangan'])
            ->where('tahun', $tahun);

        if (!empty($bulan)) {
            $query->where('bulan', $bulan);
        }

        if (!empty($lokasiId)) {
            $query->where('id_lokasi', $lokasiId);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if ($user->role === 'super_admin') {
            if (!empty($perusahaanId)) {
                $query->where(function ($q) use ($perusahaanId) {
                    $q->where('id_perusahaan', $perusahaanId)
                      ->orWhereHas('lokasi', fn($lq) => $lq->where('id_perusahaan', $perusahaanId));
                });
            }
        } else {
            $query->where('id_perusahaan', $user->id_perusahaan);
        }

        $jadwals = $query->orderBy('minggu_ke')->orderBy('tanggal_mulai')->paginate(15)->withQueryString();

        // Statistik
        $baseStat = ChecklistJadwal::where('tahun', $tahun);
        if (!empty($bulan)) {
            $baseStat->where('bulan', $bulan);
        }
        if ($user->role === 'super_admin') {
            if (!empty($perusahaanId)) {
                $baseStat->where(function ($q) use ($perusahaanId) {
                    $q->where('id_perusahaan', $perusahaanId)
                      ->orWhereHas('lokasi', fn($lq) => $lq->where('id_perusahaan', $perusahaanId));
                });
            }
        } else {
            $baseStat->where('id_perusahaan', $user->id_perusahaan);
        }

        $stats = [
            'total' => (clone $baseStat)->count(),
            'selesai' => (clone $baseStat)->where('status', 'selesai')->count(),
            'berjalan' => (clone $baseStat)->where('status', 'berjalan')->count(),
            'terjadwal' => (clone $baseStat)->where('status', 'terjadwal')->count(),
        ];

        $lokasis = Lokasi::with('perusahaan')->orderBy('nama_lokasi')->get();

        $perusahaans = $user->role === 'super_admin' ? Perusahaan::orderBy('nama_perusahaan')->get() : collect();

        $petugasQuery = User::with('perusahaan')->whereIn('role', ['petugas', 'super_admin'])->orderBy('name');
        if ($user->role !== 'super_admin') {
            $petugasQuery->where(function ($q) use ($user) {
                $q->where('id_perusahaan', $user->id_perusahaan)->orWhere('role', 'super_admin');
            });
        }
        $petugasList = $petugasQuery->get();

        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');
        $masterItems = ChecklistItem::where('is_active', true)->orderBy('urutan')->get();

        return view('content.dashboard.checklist.jadwal.index', compact(
            'jadwals',
            'tahun',
            'bulan',
            'stats',
            'lokasis',
            'perusahaans',
            'perusahaanId',
            'petugasList',
            'currentMonth',
            'currentYear',
            'masterItems'
        ));
    }

    public function create()
    {
        return redirect()->route('checklist.jadwal.index', ['tambah' => 1]);
    }

    public function store(Request $request)
    {
        $validationRules = [
            'id_lokasi' => 'required|exists:lokasis,id',
            'tahun' => 'required|integer|min:2020|max:2099',
            'bulan' => 'required|integer|min:1|max:12',
            'minggu_ke' => 'required|integer|min:1|max:5',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'assigned_to' => 'nullable|exists:users,id',
            'catatan' => 'nullable|string|max:1000',
        ];

        if (auth()->user()->role === 'super_admin') {
            $validationRules['id_perusahaan'] = 'nullable|exists:perusahaans,id';
        }

        $request->validate($validationRules);

        $lokasi = Lokasi::findOrFail($request->id_lokasi);
        $idPerusahaan = $lokasi->id_perusahaan
            ?? ($request->filled('id_perusahaan') ? $request->id_perusahaan : auth()->user()->id_perusahaan);

        DB::beginTransaction();
        try {
            // Generate Kode Jadwal unik
            $prefix = sprintf('JDW-%04d%02d-W%d', $request->tahun, $request->bulan, $request->minggu_ke);
            $count = ChecklistJadwal::where('kode_jadwal', 'like', "{$prefix}-%")->count() + 1;
            $kodeJadwal = sprintf('%s-%03d', $prefix, $count);

            $jadwal = ChecklistJadwal::create([
                'kode_jadwal' => $kodeJadwal,
                'id_perusahaan' => $idPerusahaan,
                'id_lokasi' => $request->id_lokasi,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'minggu_ke' => $request->minggu_ke,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'assigned_to' => $request->assigned_to,
                'status' => 'terjadwal',
                'catatan' => $request->catatan,
                'created_by' => auth()->id(),
            ]);

            // Otomatis Inisiasi Data Pemeriksaan Ruangan
            $ruangan = ChecklistRuangan::create([
                'jadwal_id' => $jadwal->id,
                'id_lokasi' => $request->id_lokasi,
                'id_perusahaan' => $idPerusahaan,
                'petugas_id' => $request->assigned_to,
                'status' => 'belum_dicek',
                'kondisi_ruangan' => 'semua_baik',
                'total_device' => 0,
                'total_checked' => 0,
            ]);

            // Ambil semua device mapping aktif di ruangan ini (sesuai perusahaan terkait)
            $activeMappings = Maping::withoutGlobalScopes()
                ->where('id_lokasi', $request->id_lokasi)
                ->where('status', 'aktif')
                ->when($idPerusahaan, fn($q) => $q->where('id_perusahaan', $idPerusahaan))
                ->with(['karyawan', 'keluar.inventaris.dataAset'])
                ->get();

            // Default items checklist (hanya ambil global atau milik perusahaan yang bersangkutan)
            $defaultItems = ChecklistItem::where('is_active', true)
                ->where(function ($q) use ($idPerusahaan) {
                    $q->whereNull('id_perusahaan')
                      ->orWhere('id_perusahaan', $idPerusahaan);
                })
                ->orderBy('urutan')
                ->get();

            $totalDevices = 0;

            foreach ($activeMappings as $mapping) {
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

                $totalDevices++;
            }

            $ruangan->update([
                'total_device' => $totalDevices,
            ]);

            DB::commit();

            return redirect()->route('checklist.jadwal.index')
                ->with('success', "Jadwal {$jadwal->kode_jadwal} untuk lokasi {$lokasi->nama_lokasi} berhasil dibuat dengan {$totalDevices} device.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat jadwal: ' . $e->getMessage());
        }
    }

    public function edit(ChecklistJadwal $jadwal)
    {
        $jadwal->load(['lokasi.perusahaan', 'perusahaan', 'assignedTo']);

        $lokasis = Lokasi::with('perusahaan')
            ->when($jadwal->id_perusahaan, fn($q) => $q->where('id_perusahaan', $jadwal->id_perusahaan))
            ->orderBy('nama_lokasi')->get();

        $petugasList = User::with('perusahaan')
            ->whereIn('role', ['petugas', 'super_admin'])
            ->when($jadwal->id_perusahaan, function ($q) use ($jadwal) {
                $q->where(function ($sub) use ($jadwal) {
                    $sub->where('id_perusahaan', $jadwal->id_perusahaan)->orWhere('role', 'super_admin');
                });
            })
            ->orderBy('name')->get();

        return view('content.dashboard.checklist.jadwal.edit', compact('jadwal', 'lokasis', 'petugasList'));
    }

    public function update(Request $request, ChecklistJadwal $jadwal)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:terjadwal,berjalan,selesai,terlewat',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $jadwal->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'assigned_to' => $request->assigned_to,
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        if ($jadwal->checklistRuangan) {
            $jadwal->checklistRuangan->update([
                'petugas_id' => $request->assigned_to,
            ]);
        }

        return redirect()->route('checklist.jadwal.index')
            ->with('success', "Jadwal {$jadwal->kode_jadwal} berhasil diperbarui.");
    }

    public function destroy(ChecklistJadwal $jadwal)
    {
        $kode = $jadwal->kode_jadwal;
        $jadwal->delete();

        return redirect()->route('checklist.jadwal.index')
            ->with('success', "Jadwal {$kode} berhasil dihapus.");
    }
}

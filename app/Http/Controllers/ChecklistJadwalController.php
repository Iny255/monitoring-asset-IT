<?php

namespace App\Http\Controllers;

use App\Models\ChecklistJadwalRutin;
use App\Models\ChecklistRuangan;
use App\Models\ChecklistItem;
use App\Models\Lokasi;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistJadwalController extends Controller
{
    /**
     * Tampilkan Master Jadwal Rutin Mingguan
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $filterHari = $request->get('hari');
        $lokasiId = $request->get('id_lokasi');
        $petugasId = $request->get('assigned_to');
        $perusahaanId = $request->get('id_perusahaan', $request->get('perusahaan_id'));

        $query = ChecklistJadwalRutin::with(['lokasi.perusahaan', 'assignedTo.perusahaan', 'perusahaan', 'creator']);

        if (!empty($filterHari)) {
            $query->where('hari', strtolower($filterHari));
        }

        if (!empty($lokasiId)) {
            $query->where('id_lokasi', $lokasiId);
        }

        if (!empty($petugasId)) {
            $query->where('assigned_to', $petugasId);
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

        $allRutins = $query->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu')")
            ->orderBy('id_lokasi')
            ->get();

        // Kelompokkan per hari (Senin s/d Minggu)
        $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $jadwalPerHari = [];
        foreach ($hariList as $h) {
            $jadwalPerHari[$h] = $allRutins->where('hari', $h);
        }

        // Statistik
        $stats = [
            'total_jadwal' => $allRutins->count(),
            'total_aktif' => $allRutins->where('is_active', true)->count(),
            'total_lokasi' => $allRutins->pluck('id_lokasi')->unique()->count(),
            'hari_terisi' => collect($jadwalPerHari)->filter(fn($col) => $col->count() > 0)->count(),
        ];

        $lokasis = Lokasi::with('perusahaan')->orderBy('nama_lokasi')->get();
        $perusahaans = $user->role === 'super_admin' ? Perusahaan::orderBy('nama_perusahaan')->get() : collect();

        $petugasQuery = User::with(['perusahaan', 'karyawan', 'roleDefinition'])
            ->where(function ($q) {
                $q->whereIn('role', ['petugas', 'teknisi', 'super_admin'])
                  ->orWhereHas('roleDefinition', function ($rq) {
                      $rq->whereIn('name', ['petugas', 'teknisi', 'super_admin']);
                  })
                  ->orWhereHas('karyawan', function ($kq) {
                      $kq->where('divisi', 'LIKE', '%IT%')
                         ->orWhere('divisi', 'LIKE', '%teknisi%')
                         ->orWhere('jabatan', 'LIKE', '%IT%')
                         ->orWhere('jabatan', 'LIKE', '%teknisi%');
                  });
            })
            ->orderBy('name');

        if ($user->role !== 'super_admin') {
            $petugasQuery->where(function ($q) use ($user) {
                $q->where('id_perusahaan', $user->id_perusahaan)->orWhere('role', 'super_admin');
            });
        }
        $petugasList = $petugasQuery->get();

        $masterItems = ChecklistItem::where('is_active', true)->orderBy('urutan')->get();

        return view('content.dashboard.checklist.jadwal.index', compact(
            'allRutins',
            'jadwalPerHari',
            'hariList',
            'stats',
            'lokasis',
            'perusahaans',
            'perusahaanId',
            'petugasList',
            'filterHari',
            'lokasiId',
            'petugasId',
            'masterItems'
        ));
    }

    public function create()
    {
        return redirect()->route('checklist.jadwal.index', ['tambah' => 1]);
    }

    /**
     * Simpan Jadwal Rutin Mingguan Baru (Input Sekali Berlaku Selamanya)
     */
    public function store(Request $request)
    {
        $validationRules = [
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'id_lokasi' => 'required|exists:lokasis,id',
            'assigned_to' => 'nullable|exists:users,id',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'catatan' => 'nullable|string|max:1000',
        ];

        if (auth()->user()->role === 'super_admin') {
            $validationRules['id_perusahaan'] = 'nullable|exists:perusahaans,id';
        }

        $request->validate($validationRules);

        $lokasi = Lokasi::findOrFail($request->id_lokasi);
        $idPerusahaan = $lokasi->id_perusahaan
            ?? ($request->filled('id_perusahaan') ? $request->id_perusahaan : auth()->user()->id_perusahaan);

        // Cek duplikasi: apakah ruangan ini sudah dijadwalkan di hari yang sama untuk perusahaan terkait
        $exists = ChecklistJadwalRutin::where('id_perusahaan', $idPerusahaan)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('hari', strtolower($request->hari))
            ->exists();

        $hariIndo = ucfirst($request->hari);

        if ($exists) {
            return back()->withInput()->with('error', "Ruangan '{$lokasi->nama_lokasi}' sudah terdaftar dalam jadwal rutin hari {$hariIndo}.");
        }

        try {
            ChecklistJadwalRutin::create([
                'id_perusahaan' => $idPerusahaan,
                'id_lokasi' => $request->id_lokasi,
                'hari' => strtolower($request->hari),
                'assigned_to' => $request->assigned_to,
                'jam_mulai' => $request->jam_mulai ?: '08:00',
                'jam_selesai' => $request->jam_selesai ?: '17:00',
                'is_active' => true,
                'catatan' => $request->catatan,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('checklist.jadwal.index')
                ->with('success', "Jadwal rutin untuk {$lokasi->nama_lokasi} pada hari {$hariIndo} berhasil disimpan. Jadwal ini akan otomatis digunakan setiap minggu.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan jadwal rutin: ' . $e->getMessage());
        }
    }

    /**
     * Form Edit Jadwal Rutin
     */
    public function edit($id)
    {
        $jadwal = ChecklistJadwalRutin::with(['lokasi.perusahaan', 'perusahaan', 'assignedTo'])->findOrFail($id);

        $lokasis = Lokasi::with('perusahaan')
            ->when($jadwal->id_perusahaan, fn($q) => $q->where('id_perusahaan', $jadwal->id_perusahaan))
            ->orderBy('nama_lokasi')->get();

        $petugasList = User::with(['perusahaan', 'karyawan', 'roleDefinition'])
            ->where(function ($q) {
                $q->whereIn('role', ['petugas', 'teknisi', 'super_admin'])
                  ->orWhereHas('roleDefinition', function ($rq) {
                      $rq->whereIn('name', ['petugas', 'teknisi', 'super_admin']);
                  })
                  ->orWhereHas('karyawan', function ($kq) {
                      $kq->where('divisi', 'LIKE', '%IT%')
                         ->orWhere('divisi', 'LIKE', '%teknisi%')
                         ->orWhere('jabatan', 'LIKE', '%IT%')
                         ->orWhere('jabatan', 'LIKE', '%teknisi%');
                  });
            })
            ->when($jadwal->id_perusahaan, function ($q) use ($jadwal) {
                $q->where(function ($sub) use ($jadwal) {
                    $sub->where('id_perusahaan', $jadwal->id_perusahaan)->orWhere('role', 'super_admin');
                });
            })
            ->orderBy('name')->get();

        return view('content.dashboard.checklist.jadwal.edit', compact('jadwal', 'lokasis', 'petugasList'));
    }

    /**
     * Update Jadwal Rutin
     */
    public function update(Request $request, $id)
    {
        $jadwal = ChecklistJadwalRutin::findOrFail($id);

        $validationRules = [
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'id_lokasi' => 'required|exists:lokasis,id',
            'assigned_to' => 'nullable|exists:users,id',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'is_active' => 'required|boolean',
            'catatan' => 'nullable|string|max:1000',
        ];

        $request->validate($validationRules);

        // Cek duplikasi jika mengubah hari atau lokasi
        $exists = ChecklistJadwalRutin::where('id_perusahaan', $jadwal->id_perusahaan)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('hari', strtolower($request->hari))
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', "Ruangan tersebut sudah terdaftar pada jadwal hari " . ucfirst($request->hari) . ".");
        }

        $jadwal->update([
            'hari' => strtolower($request->hari),
            'id_lokasi' => $request->id_lokasi,
            'assigned_to' => $request->assigned_to,
            'jam_mulai' => $request->jam_mulai ?: '08:00',
            'jam_selesai' => $request->jam_selesai ?: '17:00',
            'is_active' => (bool) $request->is_active,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('checklist.jadwal.index')
            ->with('success', "Jadwal rutin {$jadwal->lokasi->nama_lokasi} hari " . ucfirst($jadwal->hari) . " berhasil diperbarui.");
    }

    /**
     * Hapus Jadwal Rutin
     */
    public function destroy($id)
    {
        $jadwal = ChecklistJadwalRutin::findOrFail($id);
        $namaLokasi = $jadwal->lokasi->nama_lokasi ?? 'Ruangan';
        $hariIndo = ucfirst($jadwal->hari);

        $jadwal->delete();

        return redirect()->route('checklist.jadwal.index')
            ->with('success', "Jadwal rutin {$namaLokasi} pada hari {$hariIndo} berhasil dihapus. Riwayat pelaksanaan checklist yang sudah ada tetap tersimpan aman.");
    }

    /**
     * Toggle status aktif/non-aktif jadwal rutin
     */
    public function toggleStatus($id)
    {
        $jadwal = ChecklistJadwalRutin::findOrFail($id);
        $jadwal->is_active = !$jadwal->is_active;
        $jadwal->save();

        $statusText = $jadwal->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Jadwal rutin {$jadwal->lokasi->nama_lokasi} hari " . ucfirst($jadwal->hari) . " berhasil {$statusText}.");
    }
}

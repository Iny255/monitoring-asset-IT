<?php

namespace App\Http\Controllers;

use App\Models\ChecklistDevice;
use App\Models\ChecklistItem;
use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\Keluar;
use App\Models\Maintenance;
use App\Models\Maping;
use App\Models\Peminjaman;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DokumenPerawatanDeviceExport;

class ChecklistDokumenPerawatanController extends Controller
{
    /**
     * Tampilan Cetak / Print PDF (A4 Landscape, multi-page: F-IT-001/00 & F-IT-002/00)
     */
    public function cetak(Request $request, $id)
    {
        $user = auth()->user();
        $tahun = (int) $request->input('tahun', date('Y'));
        $isBlank = (bool) $request->boolean('blank', false);
        $page = $request->input('page', 'all'); // 'all', 'f001', 'f002'

        $data = $this->buildDokumenData($id, $tahun, $isBlank, $user);

        if (!$data) {
            abort(404, 'Data perangkat inventaris tidak ditemukan.');
        }

        return view('content.dashboard.checklist.dokumen.cetak', array_merge($data, [
            'tahun' => $tahun,
            'isBlank' => $isBlank,
            'page' => $page,
            'user' => $user,
        ]));
    }

    /**
     * Download Excel (.xlsx) dengan 2 Sheet (Sheet 1: F-IT-001/00, Sheet 2: F-IT-002/00)
     */
    public function exportExcel(Request $request, $id)
    {
        $user = auth()->user();
        $tahun = (int) $request->input('tahun', date('Y'));
        $isBlank = (bool) $request->boolean('blank', false);

        $data = $this->buildDokumenData($id, $tahun, $isBlank, $user);

        if (!$data) {
            abort(404, 'Data perangkat inventaris tidak ditemukan.');
        }

        $kodeAsetClean = str_replace(['/', '\\', ' '], '_', $data['unit']->kode_aset ?? 'Device');
        $filename = "Dokumen_Perawatan_{$kodeAsetClean}_{$tahun}.xlsx";

        return Excel::download(new DokumenPerawatanDeviceExport($data), $filename);
    }

    /**
     * Compile complete data structure for both F-IT-001/00 and F-IT-002/00
     */
    public function buildDokumenData($id, int $tahun, bool $isBlank = false, $user = null)
    {
        $user = $user ?? auth()->user();

        // 1. Resolve Target Inventaris
        $baseQuery = Inventaris::withoutGlobalScopes()->with([
            'dataAset.kategori',
            'perusahaan',
            'keluarTerakhir.maping.karyawan',
            'keluarTerakhir.karyawan',
            'keluarTerakhir.lokasi',
            'peminjamans' => function ($q) {
                $q->withoutGlobalScopes()->with(['karyawan', 'karyawanTujuan', 'lokasi'])->latest();
            },
        ]);

        $unit = null;
        if (is_numeric($id)) {
            $unit = (clone $baseQuery)->where('id', $id)->first();
        }
        if (!$unit) {
            $unit = (clone $baseQuery)->where('kode_aset', $id)->first();
        }

        if (!$unit) {
            return null;
        }

        // Cari semua ID inventaris yang terkait (misal hasil mutasi / kode lama)
        $allInvIds = [$unit->id];
        if (!empty($unit->kode_aset)) {
            $relatedIds = Inventaris::withoutGlobalScopes()
                ->where('kode_aset', $unit->kode_aset)
                ->pluck('id')
                ->toArray();
            $allInvIds = array_unique(array_merge($allInvIds, $relatedIds));
        }

        // 2. Kategori Checkbox Detection
        $katNama = strtolower($unit->dataAset?->kategori?->nama_barang ?? '');
        $isLaptop = str_contains($katNama, 'laptop') || str_contains($katNama, 'notebook');
        $isPrinter = str_contains($katNama, 'printer');
        $isHpTablet = str_contains($katNama, 'hp') || str_contains($katNama, 'tablet') || str_contains($katNama, 'smartphone') || str_contains($katNama, 'gadget');
        
        $kategoriLain = null;
        if (!$isLaptop && !$isPrinter && !$isHpTablet) {
            $kategoriLain = $unit->dataAset?->kategori?->nama_barang ?: 'Perangkat IT';
        }

        // 3. Nama Device, Pengguna & Divisi
        $namaDevice = trim(($unit->dataAset?->merek ?? '') . ' ' . ($unit->dataAset?->type ?? ''));
        if (empty($namaDevice)) {
            $namaDevice = $unit->dataAset?->kategori?->nama_barang ?? 'Perangkat IT';
        }
        $kodeAsetTampil = $unit->kode_aset ?? $unit->no_inventaris ?? '-';

        // Deteksi Pengguna & Divisi Aktif secara komprehensif
        $namaPengguna = null;
        $divisi = null;

        // A. Cek Peminjaman Aktif (Dipinjam)
        $activeLoan = Peminjaman::withoutGlobalScopes()
            ->with(['karyawan', 'karyawanTujuan'])
            ->whereIn('inventaris_id', $allInvIds)
            ->where('status', 'Dipinjam')
            ->latest('id')
            ->first();

        if ($activeLoan) {
            $namaPengguna = $activeLoan->peminjam_nama;
            $divisi = $activeLoan->karyawan?->divisi 
                ?? $activeLoan->karyawanTujuan?->divisi 
                ?? 'Peminjaman';
        }

        // B. Cek Mapping Aktif
        if (empty($namaPengguna) || empty($divisi)) {
            $activeMaping = Maping::withoutGlobalScopes()
                ->with(['karyawan', 'lokasi', 'keluar.karyawan'])
                ->whereHas('keluar', function ($q) use ($allInvIds) {
                    $q->withoutGlobalScopes()->whereIn('inventaris_id', $allInvIds);
                })
                ->whereIn('status', ['aktif', 'servis', 'maintenance'])
                ->latest('id')
                ->first();

            if ($activeMaping) {
                if (empty($namaPengguna)) {
                    $namaPengguna = $activeMaping->jenis_penerima == 'Perorangan'
                        ? ($activeMaping->karyawan?->nama_karyawan ?? $activeMaping->penerima)
                        : ($activeMaping->divisi ?? $activeMaping->penerima);
                }

                if (empty($divisi)) {
                    $divisi = $activeMaping->divisi
                        ?? $activeMaping->karyawan?->divisi
                        ?? $activeMaping->keluar?->divisi_klr
                        ?? $activeMaping->keluar?->karyawan?->divisi;
                }
            }
        }

        // C. Cek Transaksi Keluar Terakhir
        if (empty($namaPengguna) || empty($divisi)) {
            $latestKeluar = Keluar::withoutGlobalScopes()
                ->with(['karyawan', 'maping.karyawan'])
                ->whereIn('inventaris_id', $allInvIds)
                ->latest('tgl_keluar')
                ->latest('id')
                ->first();

            if ($latestKeluar) {
                if (empty($namaPengguna)) {
                    $namaPengguna = $latestKeluar->jenis_penerima == 'Perorangan'
                        ? ($latestKeluar->karyawan?->nama_karyawan ?? $latestKeluar->maping?->penerima)
                        : ($latestKeluar->divisi_klr ?? $latestKeluar->maping?->divisi);
                }

                if (empty($divisi)) {
                    $divisi = $latestKeluar->divisi_klr
                        ?? $latestKeluar->karyawan?->divisi
                        ?? $latestKeluar->maping?->divisi
                        ?? $latestKeluar->maping?->karyawan?->divisi;
                }
            }
        }

        // D. Cek dari Checklist Device Terakhir
        if (empty($namaPengguna) || empty($divisi)) {
            $lastChecklistDevice = ChecklistDevice::withoutGlobalScopes()
                ->with(['maping.karyawan', 'peminjaman.karyawan', 'peminjaman.karyawanTujuan'])
                ->whereIn('inventaris_id', $allInvIds)
                ->latest('id')
                ->first();

            if ($lastChecklistDevice) {
                if (empty($namaPengguna)) {
                    $namaPengguna = $lastChecklistDevice->nama_pengguna;
                }

                if (empty($divisi)) {
                    $divisi = $lastChecklistDevice->maping?->divisi
                        ?? $lastChecklistDevice->maping?->karyawan?->divisi
                        ?? $lastChecklistDevice->peminjaman?->karyawan?->divisi
                        ?? $lastChecklistDevice->peminjaman?->karyawanTujuan?->divisi;
                }
            }
        }

        // E. Jika Nama Pengguna diketahui tapi Divisi masih kosong, cari ke master Karyawan
        if ((empty($divisi) || $divisi === '-') && !empty($namaPengguna) && $namaPengguna !== '-') {
            $karyawanMatch = Karyawan::withoutGlobalScopes()
                ->where('nama_karyawan', $namaPengguna)
                ->orWhere('nama_karyawan', 'like', "%{$namaPengguna}%")
                ->first();

            if ($karyawanMatch && !empty($karyawanMatch->divisi)) {
                $divisi = $karyawanMatch->divisi;
            }
        }

        // Normalisasi Nilai
        $namaPengguna = !empty($namaPengguna) ? trim($namaPengguna) : '-';
        $divisi = !empty($divisi) ? trim($divisi) : '-';

        // 4. Data Item Check Perangkat (Jenis Perawatan)
        // Ambil item yang pernah dicek pada device ini atau dari Master ChecklistItem perusahaan
        $perusahaanId = $unit->perusahaan_id;
        $masterItems = ChecklistItem::where('is_active', true)
            ->where(function ($q) use ($perusahaanId) {
                $q->whereNull('id_perusahaan')
                  ->orWhere('id_perusahaan', $perusahaanId);
            })
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $jenisPerawatanList = [];
        foreach ($masterItems as $mItem) {
            $jenisPerawatanList[] = $mItem->nama_item;
        }

        // Tambahkan item unik dari ChecklistDeviceItem jika belum tercatat
        $historicalItems = \DB::table('checklist_device_items')
            ->join('checklist_devices', 'checklist_device_items.checklist_device_id', '=', 'checklist_devices.id')
            ->whereIn('checklist_devices.inventaris_id', $allInvIds)
            ->distinct()
            ->pluck('checklist_device_items.nama_item')
            ->filter()
            ->toArray();

        foreach ($historicalItems as $hItem) {
            if (!in_array($hItem, $jenisPerawatanList)) {
                $jenisPerawatanList[] = $hItem;
            }
        }

        // Fallback default jika master item belum ada sama sekali
        if (empty($jenisPerawatanList)) {
            $jenisPerawatanList = [
                'Kebersihan fisik & sirkulasi udara (Casing & Fan bersih dari debu)',
                'Kondisi kabel power, adaptor, dan kelistrikan aman',
                'Booting lancar, respon sistem normal (tidak hang / lag)',
                'Suhu perangkat normal (tidak overheat)',
                'Antivirus aktif & database virus ter-update',
                'Kapasitas harddisk / SSD aman (free space > 15%)',
                'Koneksi jaringan (LAN / Wi-Fi) stabil & lancar',
                'Fungsi periferal & port I/O (Keyboard, Mouse, USB, Display) normal',
            ];
        }

        // 5. Matriks 12 Bulan x 4 Minggu (Form F-IT-001/00)
        // Inisialisasi matriks kosong: $matrix[item][bulan][minggu] = null
        $matrix = [];
        foreach ($jenisPerawatanList as $item) {
            for ($b = 1; $b <= 12; $b++) {
                for ($m = 1; $m <= 4; $m++) {
                    $matrix[$item][$b][$m] = null;
                }
            }
        }

        // 6. Ambil data pengecekan ChecklistDevice pada tahun tersebut
        $deviceChecks = ChecklistDevice::withoutGlobalScopes()
            ->with(['checklistRuangan', 'items', 'checkedBy', 'maintenance'])
            ->whereIn('inventaris_id', $allInvIds)
            ->where(function ($q) use ($tahun) {
                $q->whereYear('checked_at', $tahun)
                  ->orWhereHas('checklistRuangan', function ($rq) use ($tahun) {
                      $rq->whereYear('tanggal_pemeriksaan', $tahun);
                  });
            })
            ->get();

        // 7. Ambil data Maintenance pada tahun tersebut
        $maintenances = Maintenance::withoutGlobalScopes()
            ->with(['creator'])
            ->whereIn('inventaris_id', $allInvIds)
            ->where(function ($q) use ($tahun) {
                $q->whereYear('tanggal', $tahun)
                  ->orWhereYear('created_at', $tahun);
            })
            ->get();

        // Riwayat untuk Form F-IT-002/00 (Kartu History Device)
        $historyLogs = [];

        if (!$isBlank) {
            // A. Petakan hasil checklist ke Matriks F-IT-001/00 dan F-IT-002/00
            foreach ($deviceChecks as $dev) {
                // Abaikan jika device belum dicek
                if ($dev->status_device === 'belum_dicek') {
                    continue;
                }

                $tgl = $dev->checked_at
                    ? Carbon::parse($dev->checked_at)
                    : ($dev->checklistRuangan?->tanggal_pemeriksaan ? Carbon::parse($dev->checklistRuangan->tanggal_pemeriksaan) : null);

                if (!$tgl || $tgl->year != $tahun) {
                    continue;
                }

                $bulan = (int) $tgl->format('n'); // 1 s/d 12
                $minggu = min(4, (int) ceil($tgl->day / 7)); // 1 s/d 4
                $tglVal = $tgl->format('j/n'); // Format tgl/bln (misal: '24/9', '5/9')

                // Matriks F-IT-001/00
                if ($dev->items->isNotEmpty()) {
                    foreach ($dev->items as $devItem) {
                        $itemName = $devItem->nama_item;
                        // Otomatis diisi tanggal pengecekan jika selesai/OK (bukan centang), ✖ jika ada kendala
                        $symbol = $devItem->is_ok ? $tglVal : '✖';
                        $matrix[$itemName][$bulan][$minggu] = $symbol;
                    }
                } else {
                    // Fallback jika tidak ada sub-item
                    $symbol = ($dev->status_device === 'normal') ? $tglVal : ($dev->status_device === 'ada_kendala' ? '✖' : null);
                    if ($symbol) {
                        foreach ($jenisPerawatanList as $itemName) {
                            if (empty($matrix[$itemName][$bulan][$minggu])) {
                                $matrix[$itemName][$bulan][$minggu] = $symbol;
                            }
                        }
                    }
                }

                // Log F-IT-002/00 (Kartu History Device)
                $isNormal = ($dev->status_device === 'normal');
                $temuan = $isNormal ? 'Tidak ada kendala' : ($dev->catatan_kendala ?: 'Ditemukan kendala saat pemeriksaan berkala');
                
                $tindakan = '-';
                if ($dev->maintenance) {
                    $tindakan = $dev->maintenance->tindakan ?: ($dev->maintenance->diagnosa ?: 'Diajukan penanganan teknisi');
                } elseif (!$isNormal) {
                    $tindakan = 'Pengecekan teknisi / tindak lanjut kendala';
                }

                $isOk = $isNormal || ($dev->maintenance && in_array(strtolower($dev->maintenance->status), ['selesai', 'ok']));
                $isNg = !$isOk;

                $petugasNama = $dev->checkedBy?->name 
                    ?? $dev->checklistRuangan?->petugas?->name 
                    ?? 'Petugas IT';

                $historyLogs[] = [
                    'tanggal' => $tgl,
                    'temuan' => $temuan,
                    'tindakan' => $tindakan,
                    'is_ok' => $isOk,
                    'is_ng' => $isNg,
                    'diperiksa_oleh' => $petugasNama,
                    'mengetahui' => '', // Dikosongkan khusus untuk Document Control sesuai instruksi user
                ];
            }

            // B. Masukkan Maintenance independen yang belum tercatat via checklist_devices
            $checkedMaintIds = $deviceChecks->pluck('maintenance_id')->filter()->toArray();
            foreach ($maintenances as $maint) {
                if (in_array($maint->id, $checkedMaintIds)) {
                    continue;
                }

                $tglMaint = $maint->tanggal ? Carbon::parse($maint->tanggal) : Carbon::parse($maint->created_at);
                if ($tglMaint->year != $tahun) {
                    continue;
                }

                $isSelesai = in_array(strtolower($maint->status), ['selesai', 'ok']);

                $historyLogs[] = [
                    'tanggal' => $tglMaint,
                    'temuan' => $maint->keluhan ?: 'Servis perbaikan unit',
                    'tindakan' => $maint->tindakan ?: ($maint->diagnosa ?: '-'),
                    'is_ok' => $isSelesai,
                    'is_ng' => !$isSelesai,
                    'diperiksa_oleh' => $maint->creator?->name ?? 'Petugas Servis',
                    'mengetahui' => '', // Dikosongkan untuk Document Control
                ];
            }

            // Urutkan riwayat berdasarkan tanggal
            usort($historyLogs, function ($a, $b) {
                return $a['tanggal']->timestamp <=> $b['tanggal']->timestamp;
            });
        }

        // Minimum 12 baris pada Kartu History agar saat dicetak kertas memiliki tinggi standar
        $minRows = max(12, count($historyLogs));
        $finalHistoryLogs = [];
        for ($i = 0; $i < $minRows; $i++) {
            if (isset($historyLogs[$i])) {
                $finalHistoryLogs[] = $historyLogs[$i];
            } else {
                $finalHistoryLogs[] = [
                    'tanggal' => null,
                    'temuan' => '',
                    'tindakan' => '',
                    'is_ok' => false,
                    'is_ng' => false,
                    'diperiksa_oleh' => '',
                    'mengetahui' => '',
                ];
            }
        }

        $bulanNames = [
            1 => 'JANUARI',
            2 => 'FEBRUARI',
            3 => 'MARET',
            4 => 'APRIL',
            5 => 'MEI',
            6 => 'JUNI',
            7 => 'JULI',
            8 => 'AGUSTUS',
            9 => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOVEMBER',
            12 => 'DESEMBER',
        ];

        return [
            'unit' => $unit,
            'tahun' => $tahun,
            'isLaptop' => $isLaptop,
            'isPrinter' => $isPrinter,
            'isHpTablet' => $isHpTablet,
            'kategoriLain' => $kategoriLain,
            'namaDevice' => $namaDevice,
            'kodeAset' => $kodeAsetTampil,
            'namaPengguna' => $namaPengguna,
            'divisi' => $divisi,
            'jenisPerawatanList' => $jenisPerawatanList,
            'matrix' => $matrix,
            'historyLogs' => $finalHistoryLogs,
            'bulanNames' => $bulanNames,
        ];
    }
}

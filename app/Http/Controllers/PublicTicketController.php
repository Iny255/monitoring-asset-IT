<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Maping;
use App\Models\Peminjaman;
use App\Models\Keluar;
use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PublicTicketController extends Controller
{
    /**
     * Tampilan form pengajuan tiket helpdesk publik (tanpa login).
     */
    public function create()
    {
        $categories = TicketCategory::orderBy('nama_kategori')->get();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('content.public_ticket.create', compact('categories', 'perusahaans'));
    }

    /**
     * Endpoint API pencarian nama karyawan secara realtime (autocomplete).
     */
    public function searchKaryawan(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        $perusahaanId = $request->get('perusahaan_id');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $karyawans = Karyawan::with('perusahaan')
            ->when($perusahaanId, function ($q) use ($perusahaanId) {
                $q->where('id_perusahaan', $perusahaanId);
            })
            ->where(function ($q) use ($query) {
                $q->where('nama_karyawan', 'like', "%{$query}%")
                  ->orWhere('kode_karyawan', 'like', "%{$query}%")
                  ->orWhere('divisi', 'like', "%{$query}%");
            })
            ->orderBy('nama_karyawan')
            ->limit(15)
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->id,
                    'nama_karyawan' => $k->nama_karyawan,
                    'kode_karyawan' => $k->kode_karyawan,
                    'divisi' => $k->divisi ?? '-',
                    'jabatan' => $k->jabatan ?? '-',
                    'id_perusahaan' => $k->id_perusahaan,
                    'nama_perusahaan' => $k->perusahaan?->nama_perusahaan ?? '-',
                ];
            });

        return response()->json($karyawans);
    }

    /**
     * Endpoint API untuk mengambil perangkat/aset aktif yang dipegang oleh karyawan tertentu.
     */
    public function getKaryawanAssets($karyawanId): JsonResponse
    {
        $karyawan = Karyawan::with('perusahaan')->find($karyawanId);

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan.'
            ], 404);
        }

        $assets = collect();

        // 1. Ambil dari Mapping Aset Aktif Perorangan (Perangkat Tetap)
        $mappings = Maping::withoutGlobalScopes()->with([
            'keluar.inventaris.dataAset.kategori',
            'lokasi'
        ])
        ->where('status', 'aktif')
        ->where(function ($q) use ($karyawanId) {
            $q->where(function ($qp) use ($karyawanId) {
                $qp->where('karyawan_id', $karyawanId)
                   ->where(function ($jq) {
                       $jq->where('jenis_penerima', 'Perorangan')
                          ->orWhereNull('jenis_penerima');
                   });
            })
            ->orWhereHas('keluar', function ($qk) use ($karyawanId) {
                $qk->where('karyawan_id', $karyawanId)
                   ->where(function ($jq) {
                       $jq->where('jenis_penerima', 'Perorangan')
                          ->orWhereNull('jenis_penerima');
                   });
            });
        })
        ->get();

        foreach ($mappings as $map) {
            $inv = $map->keluar?->inventaris;
            if ($inv && !$assets->contains('inventaris_id', $inv->id)) {
                $catName = $inv->dataAset?->kategori?->nama_barang ?? 'Perangkat IT';
                $assets->push([
                    'inventaris_id' => $inv->id,
                    'kode_aset' => $inv->kode_aset ?? '-',
                    'no_inventaris' => $inv->no_inventaris ?? '-',
                    'kategori' => $catName,
                    'category_group' => $this->detectCategoryGroup($catName),
                    'merek' => $inv->dataAset?->merek ?? '-',
                    'type' => $inv->dataAset?->type ?? '-',
                    'lokasi' => $map->lokasi?->nama_lokasi ?? '-',
                    'lokasi_id' => $map->id_lokasi,
                    'tipe_alokasi' => 'Perangkat Pribadi (Mapping)',
                    'badge_class' => 'bg-label-primary',
                    'icon' => $this->detectCategoryIcon($catName),
                ]);
            }
        }

        // 2. Fallback: Transaksi Keluar langsung Perorangan (jika ada unit DIPAKAI belum dimapping)
        $keluars = Keluar::with(['inventaris.dataAset.kategori', 'lokasi'])
            ->where('karyawan_id', $karyawanId)
            ->where(function ($jq) {
                $jq->where('jenis_penerima', 'Perorangan')
                   ->orWhereNull('jenis_penerima');
            })
            ->whereHas('inventaris', function ($q) {
                $q->where('status', 'DIPAKAI');
            })
            ->whereDoesntHave('maping')
            ->get();

        foreach ($keluars as $klr) {
            $inv = $klr->inventaris;
            if ($inv && !$assets->contains('inventaris_id', $inv->id)) {
                $catName = $inv->dataAset?->kategori?->nama_barang ?? 'Perangkat IT';
                $assets->push([
                    'inventaris_id' => $inv->id,
                    'kode_aset' => $inv->kode_aset ?? '-',
                    'no_inventaris' => $inv->no_inventaris ?? '-',
                    'kategori' => $catName,
                    'category_group' => $this->detectCategoryGroup($catName),
                    'merek' => $inv->dataAset?->merek ?? '-',
                    'type' => $inv->dataAset?->type ?? '-',
                    'lokasi' => $klr->lokasi?->nama_lokasi ?? '-',
                    'lokasi_id' => $klr->lokasi_id,
                    'tipe_alokasi' => 'Perangkat Diserahkan',
                    'badge_class' => 'bg-label-success',
                    'icon' => $this->detectCategoryIcon($catName),
                ]);
            }
        }

        // 3. Ambil dari Peminjaman Aktif (Perangkat Sementara)
        $loans = Peminjaman::with([
            'inventaris.dataAset.kategori'
        ])
        ->where(function ($q) use ($karyawanId) {
            $q->where('karyawan_id', $karyawanId)
              ->orWhere('karyawan_tujuan_id', $karyawanId);
        })
        ->whereIn('status', ['dipinjam', 'DIPINJAM'])
        ->get();

        foreach ($loans as $loan) {
            $inv = $loan->inventaris;
            if ($inv && !$assets->contains('inventaris_id', $inv->id)) {
                $catName = $inv->dataAset?->kategori?->nama_barang ?? 'Perangkat IT';
                $assets->push([
                    'inventaris_id' => $inv->id,
                    'kode_aset' => $inv->kode_aset ?? '-',
                    'no_inventaris' => $inv->no_inventaris ?? '-',
                    'kategori' => $catName,
                    'category_group' => $this->detectCategoryGroup($catName),
                    'merek' => $inv->dataAset?->merek ?? '-',
                    'type' => $inv->dataAset?->type ?? '-',
                    'lokasi' => '-',
                    'lokasi_id' => null,
                    'tipe_alokasi' => 'Perangkat Pinjaman',
                    'badge_class' => 'bg-label-info',
                    'icon' => $this->detectCategoryIcon($catName),
                ]);
            }
        }

        // 4. Kumpulkan grup kategori perangkat personal yang sudah dipegang karyawan secara perorangan/pinjaman
        $ownedPersonalGroups = $assets->pluck('category_group')->unique()->toArray();

        // 5. Ambil dari Mapping Perdivisi yang sesuai divisi karyawan
        if (!empty($karyawan->divisi)) {
            $divisiClean = trim($karyawan->divisi);
            $divisiAliases = $this->getDivisionAliases($divisiClean);
            $escapedDivisi = preg_quote($divisiClean, '/');

            $divisiMappings = Maping::withoutGlobalScopes()->with([
                'keluar.inventaris.dataAset.kategori',
                'lokasi'
            ])
            ->where('status', 'aktif')
            ->where('id_perusahaan', $karyawan->id_perusahaan)
            ->where('jenis_penerima', 'Perdivisi')
            ->where(function ($q) use ($divisiAliases, $escapedDivisi) {
                // Exact match case-insensitive atau match dari daftar alias resmi
                $q->whereIn(DB::raw('LOWER(TRIM(divisi))'), $divisiAliases)
                  // ATAU word boundary regex (contoh: "R. IT" atau "DIVISI IT", tapi BUKAN "SECURITY" atau "KITIR")
                  ->orWhereRaw("divisi REGEXP ?", ['(^|[^a-zA-Z0-9])' . $escapedDivisi . '([^a-zA-Z0-9]|$)']);
            })
            ->get();

            foreach ($divisiMappings as $dmap) {
                $inv = $dmap->keluar?->inventaris;
                if ($inv && !$assets->contains('inventaris_id', $inv->id)) {
                    $catName = $inv->dataAset?->kategori?->nama_barang ?? 'Perangkat IT';
                    $group = $this->detectCategoryGroup($catName);

                    // ATURAN HIRARKI (Dedicated vs Shared Asset):
                    // Jika perangkat bertipe personal (seperti smartphone/HP, laptop, PC)
                    // dan karyawan SUDAH memiliki perangkat sejenis secara perorangan/pinjaman,
                    // maka jangan tampilkan perangkat divisi sejenis tersebut (sembunyikan).
                    // Perangkat divisi hanya muncul jika karyawan BELUM memiliki perangkat personal tersebut,
                    // atau jika perangkat bersifat shared/bersama (seperti printer, scanner, jaringan, monitor).
                    $isPersonalDevice = in_array($group, ['smartphone', 'laptop', 'pc']);
                    if ($isPersonalDevice && in_array($group, $ownedPersonalGroups)) {
                        continue;
                    }

                    $assets->push([
                        'inventaris_id' => $inv->id,
                        'kode_aset' => $inv->kode_aset ?? '-',
                        'no_inventaris' => $inv->no_inventaris ?? '-',
                        'kategori' => $catName,
                        'category_group' => $group,
                        'merek' => $inv->dataAset?->merek ?? '-',
                        'type' => $inv->dataAset?->type ?? '-',
                        'lokasi' => $dmap->lokasi?->nama_lokasi ?? ($dmap->divisi ?? '-'),
                        'lokasi_id' => $dmap->id_lokasi,
                        'tipe_alokasi' => 'Perangkat Divisi (' . ($dmap->divisi ?? 'Divisi') . ')',
                        'badge_class' => 'bg-label-warning text-dark',
                        'icon' => $this->detectCategoryIcon($catName),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'karyawan' => [
                'id' => $karyawan->id,
                'nama_karyawan' => $karyawan->nama_karyawan,
                'kode_karyawan' => $karyawan->kode_karyawan,
                'divisi' => $karyawan->divisi ?? '-',
                'jabatan' => $karyawan->jabatan ?? '-',
                'id_perusahaan' => $karyawan->id_perusahaan,
                'nama_perusahaan' => $karyawan->perusahaan?->nama_perusahaan ?? '-',
            ],
            'assets' => $assets->values(),
        ]);
    }

    /**
     * Simpan pengajuan tiket helpdesk publik ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:low,medium,high,urgent',
            'kontak_pelapor' => 'required|string|max:30',
            'email_pelapor' => 'nullable|email|max:100',
            'inventaris_id' => 'nullable|exists:inventaris,id',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,zip|max:5120',
        ], [
            'karyawan_id.required' => 'Nama karyawan pelapor wajib dipilih.',
            'ticket_category_id.required' => 'Kategori kendala wajib dipilih.',
            'judul.required' => 'Judul kendala wajib diisi.',
            'deskripsi.required' => 'Penjelasan kendala wajib diisi.',
            'kontak_pelapor.required' => 'Nomor WhatsApp / HP wajib diisi agar tim IT dapat menghubungi Anda.',
            'lampiran.max' => 'Ukuran file lampiran maksimal 5MB.',
        ]);

        $karyawan = Karyawan::findOrFail($request->karyawan_id);

        // Jika karyawan memiliki akun user internal, relasikan
        $linkedUser = User::where('karyawan_id', $karyawan->id)->first();

        // Cari lokasi dari mapping perangkat jika ada
        $lokasiId = null;
        if ($request->filled('inventaris_id')) {
            $mapping = Maping::whereHas('keluar', function ($q) use ($request) {
                $q->where('inventaris_id', $request->inventaris_id);
            })->first();
            $lokasiId = $mapping?->id_lokasi;
        }

        // Upload lampiran
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('tickets', 'public');
        }

        $nomorTiket = Ticket::generateNomorTiket();

        $ticket = Ticket::create([
            'nomor_tiket' => $nomorTiket,
            'user_id' => $linkedUser?->id,
            'karyawan_id' => $karyawan->id,
            'nama_pelapor' => $karyawan->nama_karyawan,
            'kontak_pelapor' => $request->kontak_pelapor,
            'email_pelapor' => $request->email_pelapor,
            'id_perusahaan' => $karyawan->id_perusahaan,
            'lokasi_id' => $lokasiId,
            'inventaris_id' => $request->inventaris_id ?: null,
            'ticket_category_id' => $request->ticket_category_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'prioritas' => $request->prioritas,
            'status' => 'open',
            'is_public' => true,
            'lampiran' => $lampiranPath,
        ]);

        return redirect()
            ->route('public.ticket.tracking', $ticket->nomor_tiket)
            ->with('success', "Tiket helpdesk {$nomorTiket} berhasil diajukan ke Tim IT!");
    }

    /**
     * Halaman pelacakan status tiket oleh pelapor publik.
     */
    public function tracking($nomorTiket)
    {
        $ticket = Ticket::with([
            'karyawan',
            'perusahaan',
            'lokasi',
            'inventaris.dataAset.kategori',
            'category',
            'assignee',
            'replies.user'
        ])
        ->where('nomor_tiket', $nomorTiket)
        ->firstOrFail();

        return view('content.public_ticket.success', compact('ticket'));
    }

    /**
     * Helper deteksi grup kategori perangkat.
     */
    private function detectCategoryGroup($catName): string
    {
        $k = strtolower($catName);
        if (str_contains($k, 'laptop') || str_contains($k, 'notebook') || str_contains($k, 'macbook')) {
            return 'laptop';
        }
        if (str_contains($k, 'pc') || str_contains($k, 'komputer') || str_contains($k, 'desktop') || str_contains($k, 'aio') || str_contains($k, 'all in one')) {
            return 'pc';
        }
        if (str_contains($k, 'printer') || str_contains($k, 'scanner') || str_contains($k, 'cetak')) {
            return 'printer';
        }
        if (str_contains($k, 'hp') || str_contains($k, 'handphone') || str_contains($k, 'smartphone') || str_contains($k, 'phone') || str_contains($k, 'ponsel') || str_contains($k, 'tablet') || str_contains($k, 'ipad')) {
            return 'smartphone';
        }
        if (str_contains($k, 'monitor') || str_contains($k, 'display') || str_contains($k, 'layar')) {
            return 'monitor';
        }
        if (str_contains($k, 'jaringan') || str_contains($k, 'router') || str_contains($k, 'switch') || str_contains($k, 'wifi') || str_contains($k, 'access point')) {
            return 'network';
        }
        return 'other';
    }

    /**
     * Helper icon Boxicons berdasarkan nama kategori.
     */
    private function detectCategoryIcon($catName): string
    {
        $k = strtolower($catName);
        if (str_contains($k, 'laptop') || str_contains($k, 'notebook') || str_contains($k, 'macbook')) {
            return 'bx bx-laptop';
        }
        if (str_contains($k, 'pc') || str_contains($k, 'komputer') || str_contains($k, 'desktop') || str_contains($k, 'aio') || str_contains($k, 'all in one')) {
            return 'bx bx-desktop';
        }
        if (str_contains($k, 'printer') || str_contains($k, 'scanner') || str_contains($k, 'cetak')) {
            return 'bx bx-printer';
        }
        if (str_contains($k, 'hp') || str_contains($k, 'handphone') || str_contains($k, 'smartphone') || str_contains($k, 'phone') || str_contains($k, 'ponsel') || str_contains($k, 'tablet') || str_contains($k, 'ipad')) {
            return 'bx bx-mobile-alt';
        }
        if (str_contains($k, 'monitor') || str_contains($k, 'display') || str_contains($k, 'layar')) {
            return 'bx bx-tv';
        }
        if (str_contains($k, 'jaringan') || str_contains($k, 'router') || str_contains($k, 'switch') || str_contains($k, 'wifi')) {
            return 'bx bx-wifi';
        }
        return 'bx bx-cube';
    }

    /**
     * Dapatkan daftar variasi atau alias nama divisi agar pencocokan akurat.
     */
    private function getDivisionAliases(string $divisi): array
    {
        $clean = trim($divisi);
        $lower = strtolower($clean);

        $aliasMap = [
            'it' => ['it', 'it support', 'information technology', 'ti', 'teknologi informasi', 'dept it', 'divisi it', 'r. it', 'ruang it'],
            'ti' => ['it', 'it support', 'information technology', 'ti', 'teknologi informasi'],
            'hr' => ['hr', 'hrd', 'human resource', 'human resources', 'personalia', 'sdm'],
            'hrd' => ['hr', 'hrd', 'human resource', 'human resources', 'personalia', 'sdm'],
            'ga' => ['ga', 'general affair', 'general affairs', 'umum'],
            'fa' => ['fa', 'finance', 'finance & accounting', 'keuangan', 'accounting', 'akuntansi'],
            'finance' => ['fa', 'finance', 'finance & accounting', 'keuangan', 'accounting', 'akuntansi'],
            'accounting' => ['fa', 'finance', 'finance & accounting', 'keuangan', 'accounting', 'akuntansi'],
            'qa' => ['qa', 'qc', 'quality assurance', 'quality control'],
            'qc' => ['qa', 'qc', 'quality assurance', 'quality control'],
            'security' => ['security', 'satpam', 'keamanan', 'pos security'],
            'satpam' => ['security', 'satpam', 'keamanan', 'pos security'],
            'logistik' => ['logistik', 'logistic', 'logistics', 'gudang', 'warehouse'],
            'produksi' => ['produksi', 'production', 'pabrik'],
            'marketing' => ['marketing', 'pemasaran', 'sales', 'penjualan'],
        ];

        $targets = [$lower];
        if (isset($aliasMap[$lower])) {
            $targets = array_unique(array_merge($targets, $aliasMap[$lower]));
        }

        return $targets;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Peminjaman;
use App\Models\ChecklistDevice;
use App\Models\ChecklistRuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Exports\PeminjamanExport;
use Maatwebsite\Excel\Facades\Excel;
class PeminjamanController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $query = Peminjaman::with([
      'inventaris.dataAset',
      'karyawan',
      'karyawanTujuan',
      'perusahaanTujuan',
      'lokasi',
      'maintenanceTerakhir',
    ]);

    /*
    |--------------------------------------------------------------------------
    | FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if (auth()->user()->role != 'super_admin') {
      $query->whereHas('inventaris', function ($q) {
        $q->where('perusahaan_id', auth()->user()->id_perusahaan);
      });
    }
    if (auth()->user()->role == 'super_admin' && $request->filled('perusahaan')) {
      $query->whereHas('inventaris', function ($q) use ($request) {
        $q->where('perusahaan_id', $request->perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal')) {
      $tglAwal = $request->tanggal_awal;
      $query->where(function ($q) use ($tglAwal) {
        $q->whereDate('tanggal_pinjam', '>=', $tglAwal)
          ->orWhere(function ($sub) use ($tglAwal) {
            $sub->whereNull('tanggal_pinjam')
              ->whereDate('created_at', '>=', $tglAwal);
          });
      });
    }

    if ($request->filled('tanggal_akhir')) {
      $tglAkhir = $request->tanggal_akhir;
      $query->where(function ($q) use ($tglAkhir) {
        $q->whereDate('tanggal_pinjam', '<=', $tglAkhir)
          ->orWhere(function ($sub) use ($tglAkhir) {
            $sub->whereNull('tanggal_pinjam')
              ->whereDate('created_at', '<=', $tglAkhir);
          });
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER JENIS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('jenis')) {
      $query->where('jenis_peminjaman', $request->jenis);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER STATUS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER LOKASI
    |--------------------------------------------------------------------------
    */

    if ($request->filled('id_lokasi')) {
      $query->where('id_lokasi', $request->id_lokasi);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER PENCARIAN
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_peminjaman', 'like', "%{$search}%")

          ->orWhereHas('inventaris', function ($inv) use ($search) {
            $inv->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
          })

          ->orWhereHas('karyawan', function ($kar) use ($search) {
            $kar->where('nama_karyawan', 'like', "%{$search}%")->orWhere('kode_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('karyawanTujuan', function ($kar) use ($search) {
            $kar->where('nama_karyawan', 'like', "%{$search}%")->orWhere('kode_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('perusahaanTujuan', function ($per) use ($search) {
            $per->where('nama_perusahaan', 'like', "%{$search}%");
          })

          ->orWhereHas('lokasi', function ($lok) use ($search) {
            $lok->where('nama_lokasi', 'like', "%{$search}%");
          });
      });
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    $peminjamans = $query
      ->latest()
      ->paginate(10)
      ->appends(request()->query());
    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    if (auth()->user()->role == 'super_admin') {
      $lokasis = Lokasi::orderBy('nama_lokasi')->get();
    } else {
      $lokasis = Lokasi::where('id_perusahaan', auth()->user()->id_perusahaan)
        ->orderBy('nama_lokasi')
        ->get();
    }

    return view('content.dashboard.peminjaman.index', compact('peminjamans', 'perusahaans', 'lokasis'));
  }
  public function exportExcel(Request $request)
  {
    return Excel::download(
      new PeminjamanExport($request),
      'Laporan_Peminjaman_Aset_' . now()->format('Ymd_His') . '.xlsx'
    );
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    if (auth()->user()->role == 'super_admin') {
      $kategoris = Kategori::orderBy('nama_barang')->get();
    } else {
      $kategoris = Kategori::where('perusahaan_id', auth()->user()->id_perusahaan)
        ->orderBy('nama_barang')
        ->get();
    }
    $inventaris = collect(); // awalnya kosong

    if (auth()->user()->role == 'super_admin') {
      $karyawans = collect();
    } else {
      $karyawans = Karyawan::where('id_perusahaan', auth()->user()->id_perusahaan)
        ->orderBy('nama_karyawan')
        ->get();
    }
    if (auth()->user()->role == 'super_admin') {
      $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
      $lokasis = Lokasi::orderBy('nama_lokasi')->get();
    } else {
      $perusahaans = Perusahaan::where('id', '!=', auth()->user()->id_perusahaan)
        ->orderBy('nama_perusahaan')
        ->get();
      $lokasis = Lokasi::where('id_perusahaan', auth()->user()->id_perusahaan)
        ->orderBy('nama_lokasi')
        ->get();
    }
    return view('content.dashboard.peminjaman.create', compact('kategoris', 'inventaris', 'karyawans', 'perusahaans', 'lokasis'));
  }
  public function kategoriByPerusahaan($id)
  {
    return Kategori::where('perusahaan_id', $id)
      ->orderBy('nama_barang')
      ->get();
  }
  public function searchKaryawan(Request $request): JsonResponse
  {
    $keyword = trim($request->keyword);

    $query = Karyawan::query();
    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan')) {
        $query->where('id_perusahaan', $request->perusahaan);
      }
    } else {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    // Super Admin melihat semua perusahaan
    if (auth()->user()->role != 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    // Pencarian
    if ($keyword) {
      $query->where(function ($q) use ($keyword) {
        $q->where('kode_karyawan', 'like', "%{$keyword}%")
          ->orWhere('nama_karyawan', 'like', "%{$keyword}%")
          ->orWhere('divisi', 'like', "%{$keyword}%");
      });
    }

    $karyawans = $query
      ->orderBy('nama_karyawan')
      ->limit(10)
      ->get(['id', 'kode_karyawan', 'nama_karyawan', 'divisi']);

    return response()->json($karyawans);
  }
  public function inventarisByKategori(Request $request, string $kategoriId)
  {
    $query = Inventaris::with(['perusahaan', 'dataAset.kategori'])

      ->availableForPeminjaman()
      ->whereHas('dataAset', function ($q) use ($kategoriId) {
        $q->where('kategori_id', $kategoriId);
      });
    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan')) {
        $query->where('perusahaan_id', $request->perusahaan);
      }
    } else {
      $query->where('perusahaan_id', auth()->user()->id_perusahaan);
    }

    // Selain super admin hanya melihat inventaris perusahaan sendiri
    if (auth()->user()->role != 'super_admin') {
      $query->where('perusahaan_id', auth()->user()->id_perusahaan);
    }

    $inventaris = $query
      ->orderBy('kode_aset')
      ->get()
      ->map(function ($item) {
        return [
          'id' => $item->id,

          'kode_aset' => $item->kode_aset,

          'no_inventaris' => $item->no_inventaris,

          'status' => $item->status,

          'perusahaan' => [
            'nama_perusahaan' => $item->perusahaan?->nama_perusahaan ?? '-',
          ],

          'data_aset' => [
            'nama_barang' => $item->dataAset?->kategori?->nama_barang ?? '-',

            'merek' => $item->dataAset?->merek ?? '-',

            'type' => $item->dataAset?->type ?? '-',

            'warna' => $item->dataAset?->warna ?? '-',
          ],
        ];
      });

    return response()->json($inventaris);
  }
  public function getKaryawanPerusahaan(string $id)
  {
    $karyawans = Karyawan::where('id_perusahaan', $id)
      ->orderBy('nama_karyawan')
      ->get(['id', 'kode_karyawan', 'nama_karyawan', 'divisi']);

    return response()->json($karyawans);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'jenis_peminjaman' => 'required|in:internal,antar_perusahaan',
      'inventaris_id' => 'required|exists:inventaris,id',
      'tanggal_pinjam' => 'required|date',
      'tanggal_rencana_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
      'keperluan' => 'required|string|max:500',
      'id_lokasi' => 'nullable|exists:lokasis,id',
      'kondisi_pinjam' => 'nullable|string|max:100',

      'karyawan_id' => 'required_if:jenis_peminjaman,internal|nullable|exists:karyawans,id',

      'perusahaan_tujuan_id' => 'required_if:jenis_peminjaman,antar_perusahaan|nullable|exists:perusahaans,id',

      'karyawan_tujuan_id' => 'required_if:jenis_peminjaman,antar_perusahaan|nullable|exists:karyawans,id',
    ]);

    DB::beginTransaction();

    try {
      $inventaris = Inventaris::findOrFail($request->inventaris_id);

      /*
        |--------------------------------------------------------------------------
        | Pastikan inventaris tersedia
        |--------------------------------------------------------------------------
        */

      if ($inventaris->status != 'TERSEDIA') {
        return back()
          ->withInput()
          ->with('error', 'Inventaris tidak tersedia untuk dipinjam.');
      }

      /*
|--------------------------------------------------------------------------
| GENERATE KODE PEMINJAMAN
|--------------------------------------------------------------------------
*/

      $tanggal = \Carbon\Carbon::parse($request->tanggal_pinjam)->format('Ymd');

      $last = Peminjaman::whereHas('inventaris', function ($q) use ($inventaris) {
        $q->where('perusahaan_id', $inventaris->perusahaan_id);
      })
        ->whereDate('tanggal_pinjam', $request->tanggal_pinjam)
        ->latest('id')
        ->first();

      $nomor = 1;

      if ($last) {
        $nomor = ((int) substr($last->kode_peminjaman, -5)) + 1;
      }

      $kode = 'PJM-' . $tanggal . '-' . str_pad($nomor, 5, '0', STR_PAD_LEFT);

      /*
        |--------------------------------------------------------------------------
        | Simpan Peminjaman
        |--------------------------------------------------------------------------
        */

      $peminjaman = Peminjaman::create([
        'kode_peminjaman' => $kode,

        'inventaris_id' => $inventaris->id,

        'jenis_peminjaman' => $request->jenis_peminjaman,

        'karyawan_id' => $request->jenis_peminjaman == 'internal' ? $request->karyawan_id : null,

        'perusahaan_tujuan_id' =>
          $request->jenis_peminjaman == 'antar_perusahaan' ? $request->perusahaan_tujuan_id : null,

        'karyawan_tujuan_id' => $request->jenis_peminjaman == 'antar_perusahaan' ? $request->karyawan_tujuan_id : null,

        'id_lokasi' => $request->id_lokasi,

        'user_id' => auth()->id(),

        'tanggal_pinjam' => $request->tanggal_pinjam,

        'tanggal_rencana_kembali' => $request->tanggal_rencana_kembali,

        'keperluan' => $request->keperluan,

        'kondisi_pinjam' => $request->kondisi_pinjam ?: 'Baik',

        'status' => 'Dipinjam',
      ]);

      /*
        |--------------------------------------------------------------------------
        | Update Status Inventaris
        |--------------------------------------------------------------------------
        */

      $inventaris->update([
        'status' => 'DIPINJAM',
      ]);

      // Sinkronkan ke checklist ruangan jika ruangan sedang aktif / belum selesai
      if ($peminjaman->id_lokasi) {
        $activeRuangans = ChecklistRuangan::withoutGlobalScopes()
          ->where('id_lokasi', $peminjaman->id_lokasi)
          ->where('status', '!=', 'selesai')
          ->get();
        foreach ($activeRuangans as $r) {
          $r->syncDevicesWithMapping();
        }
      }

      DB::commit();

      return redirect()
        ->route('peminjaman.index')
        ->with('success', 'Peminjaman berhasil disimpan.');
    } catch (\Exception $e) {
      DB::rollBack();

      return back()
        ->withInput()
        ->with('error', $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Peminjaman $peminjaman)
  {
    $peminjaman->load([
      'inventaris.dataAset.kategori',
      'inventaris.perusahaan',
      'karyawan',
      'karyawanTujuan',
      'perusahaanTujuan',
      'lokasi',
      'user',
    ]);

    return view('content.dashboard.peminjaman.show', compact('peminjaman'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Peminjaman $peminjaman)
  {
    $peminjaman->load([
      'inventaris.dataAset.kategori',
      'inventaris.perusahaan',
      'karyawan',
      'karyawanTujuan',
      'perusahaanTujuan',
      'lokasi',
    ]);

    return view('content.dashboard.peminjaman.pengembalian', compact('peminjaman'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Peminjaman $peminjaman)
  {
    $request->validate([
      'tanggal_kembali' => 'required|date',
      'kondisi_kembali' => 'required|in:Baik,Rusak,Hilang',
      'keterangan_kembali' => 'nullable|string|max:500',
    ]);

    DB::beginTransaction();

    try {
      /*
        |--------------------------------------------------------------------------
        | Update transaksi peminjaman
        |--------------------------------------------------------------------------
        */

      $peminjaman->update([
        'tanggal_kembali' => $request->tanggal_kembali,
        'kondisi_kembali' => $request->kondisi_kembali,
        'keterangan_kembali' => $request->keterangan_kembali,
        'status' => $request->kondisi_kembali == 'Hilang' ? 'Hilang' : 'Dikembalikan',
      ]);

      $statusInventaris = match ($request->kondisi_kembali) {
        'Baik' => 'TERSEDIA',
        'Rusak' => 'RUSAK',
        'Hilang' => 'RUSAK',
      };

      $peminjaman->inventaris->update([
        'status' => $statusInventaris,
      ]);

      // Bersihkan perangkat checklist yang belum dicek di ruangan asal peminjaman
      $affectedRuanganIds = ChecklistDevice::where('peminjaman_id', $peminjaman->id)
        ->where('status_device', 'belum_dicek')
        ->pluck('checklist_ruangan_id')
        ->unique()
        ->toArray();

      ChecklistDevice::where('peminjaman_id', $peminjaman->id)
        ->where('status_device', 'belum_dicek')
        ->each(function ($dev) {
          $dev->items()->delete();
          $dev->delete();
        });

      foreach ($affectedRuanganIds as $rId) {
        $ruangan = ChecklistRuangan::withoutGlobalScopes()->find($rId);
        $ruangan?->updateProgress();
      }

      DB::commit();

      return redirect()
        ->route('peminjaman.index')
        ->with('success', 'Pengembalian aset berhasil disimpan.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Pengembalian aset error: ' . $e->getMessage());
      return back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
    }
  }
  public function cetak(Request $request)
  {
    $query = Peminjaman::with([
      'inventaris.dataAset',
      'inventaris.perusahaan',
      'karyawan',
      'karyawanTujuan',
      'perusahaanTujuan',
      'lokasi',
    ]);

    /*
    |--------------------------------------------------------------------------
    | MULTI COMPANY
    |--------------------------------------------------------------------------
    */

    if (auth()->user()->role != 'super_admin') {
      $query->whereHas('inventaris', function ($q) {
        $q->where('perusahaan_id', auth()->user()->id_perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER PERUSAHAAN (SUPER ADMIN)
    |--------------------------------------------------------------------------
    */

    if ($request->filled('perusahaan')) {
      $query->whereHas('inventaris', function ($q) use ($request) {
        $q->where('perusahaan_id', $request->perusahaan);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal')) {
      $tglAwal = $request->tanggal_awal;
      $query->where(function ($q) use ($tglAwal) {
        $q->whereDate('tanggal_pinjam', '>=', $tglAwal)
          ->orWhere(function ($sub) use ($tglAwal) {
            $sub->whereNull('tanggal_pinjam')
              ->whereDate('created_at', '>=', $tglAwal);
          });
      });
    }

    if ($request->filled('tanggal_akhir')) {
      $tglAkhir = $request->tanggal_akhir;
      $query->where(function ($q) use ($tglAkhir) {
        $q->whereDate('tanggal_pinjam', '<=', $tglAkhir)
          ->orWhere(function ($sub) use ($tglAkhir) {
            $sub->whereNull('tanggal_pinjam')
              ->whereDate('created_at', '<=', $tglAkhir);
          });
      });
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER JENIS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('jenis')) {
      $query->where('jenis_peminjaman', $request->jenis);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER STATUS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_peminjaman', 'like', "%{$search}%")

          ->orWhereHas('inventaris', function ($i) use ($search) {
            $i->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
          })

          ->orWhereHas('karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('karyawanTujuan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%{$search}%");
          })

          ->orWhereHas('perusahaanTujuan', function ($p) use ($search) {
            $p->where('nama_perusahaan', 'like', "%{$search}%");
          });
      });
    }

    $laporan = $query->orderBy('tanggal_pinjam')->get();

    /*
    |--------------------------------------------------------------------------
    | NAMA PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if (in_array(auth()->user()->role, ['super_admin', '1', 1]) || !auth()->user()->id_perusahaan) {
      if ($request->filled('perusahaan')) {
        $namaPerusahaan = optional(Perusahaan::find($request->perusahaan))->nama_perusahaan ?? 'SEMUA PERUSAHAAN';
      } else {
        $namaPerusahaan = 'SEMUA PERUSAHAAN';
      }
    } else {
      $namaPerusahaan = auth()->user()->perusahaan?->nama_perusahaan ?? 'Perusahaan';
    }

    return view('content.dashboard.peminjaman.cetak', compact('laporan', 'namaPerusahaan'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    DB::beginTransaction();
    try {
      $peminjaman = Peminjaman::findOrFail($id);
      $inventaris = $peminjaman->inventaris;

      // Hapus checklist devices yang terkait dan belum dicek
      $affectedRuanganIds = ChecklistDevice::where('peminjaman_id', $peminjaman->id)
        ->where('status_device', 'belum_dicek')
        ->pluck('checklist_ruangan_id')
        ->unique()
        ->toArray();

      ChecklistDevice::where('peminjaman_id', $peminjaman->id)
        ->where('status_device', 'belum_dicek')
        ->each(function ($dev) {
          $dev->items()->delete();
          $dev->delete();
        });

      foreach ($affectedRuanganIds as $rId) {
        $ruangan = ChecklistRuangan::withoutGlobalScopes()->find($rId);
        $ruangan?->updateProgress();
      }

      // Kembalikan status inventaris jika masih berstatus DIPINJAM
      if ($inventaris && $inventaris->status === 'DIPINJAM') {
        $inventaris->update(['status' => 'TERSEDIA']);
      }

      $peminjaman->delete();
      DB::commit();

      return redirect()
        ->route('peminjaman.index')
        ->with('success', 'Data peminjaman berhasil dihapus.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Gagal menghapus peminjaman: ' . $e->getMessage());
      return back()->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
    }
  }
}

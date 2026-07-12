<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
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

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_akhir);
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

    return view('content.dashboard.peminjaman.index', compact('peminjamans'));
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

    $karyawans = Karyawan::where('id_perusahaan', auth()->user()->id_perusahaan)->get();
    if (auth()->user()->role == 'super_admin') {
      $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    } else {
      $perusahaans = Perusahaan::where('id', '!=', auth()->user()->id_perusahaan)
        ->orderBy('nama_perusahaan')
        ->get();
    }
    return view('content.dashboard.peminjaman.create', compact('kategoris', 'inventaris', 'karyawans', 'perusahaans'));
  }
  public function searchKaryawan(Request $request): JsonResponse
  {
    $keyword = trim($request->keyword);

    $query = Karyawan::query();

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
  public function inventarisByKategori(string $kategoriId)
  {
    $query = Inventaris::with(['perusahaan', 'dataAset.kategori'])

      ->availableForPeminjaman()
      ->whereHas('dataAset', function ($q) use ($kategoriId) {
        $q->where('kategori_id', $kategoriId);
      });

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
            'nama_perusahaan' => optional($item->perusahaan)->nama_perusahaan,
          ],

          'data_aset' => [
            'nama_barang' => optional($item->dataAset->kategori)->nama_barang,

            'merek' => $item->dataAset->merek,

            'type' => $item->dataAset->type,

            'warna' => $item->dataAset->warna,
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

      Peminjaman::create([
        'kode_peminjaman' => $kode,

        'inventaris_id' => $inventaris->id,

        'jenis_peminjaman' => $request->jenis_peminjaman,

        'karyawan_id' => $request->jenis_peminjaman == 'internal' ? $request->karyawan_id : null,

        'perusahaan_tujuan_id' =>
          $request->jenis_peminjaman == 'antar_perusahaan' ? $request->perusahaan_tujuan_id : null,

        'karyawan_tujuan_id' => $request->jenis_peminjaman == 'antar_perusahaan' ? $request->karyawan_tujuan_id : null,

        'user_id' => auth()->id(),

        'tanggal_pinjam' => $request->tanggal_pinjam,

        'tanggal_rencana_kembali' => $request->tanggal_rencana_kembali,

        'keperluan' => $request->keperluan,

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

      DB::commit();

      return redirect()
        ->route('peminjaman.index')
        ->with('success', 'Pengembalian aset berhasil disimpan.');
    } catch (\Exception $e) {
      DB::rollBack();

      dd($e->getMessage(), $e->getFile(), $e->getLine());
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
      $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_akhir);
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

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan')) {
        $namaPerusahaan = optional(Perusahaan::find($request->perusahaan))->nama_perusahaan ?? 'SEMUA PERUSAHAAN';
      } else {
        $namaPerusahaan = 'SEMUA PERUSAHAAN';
      }
    } else {
      $namaPerusahaan = auth()->user()->perusahaan->nama_perusahaan;
    }

    return view('content.dashboard.peminjaman.cetak', compact('laporan', 'namaPerusahaan'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}

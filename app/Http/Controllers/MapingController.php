<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Maping;
use App\Models\Lokasi;
use App\Models\Keluar;
use App\Models\Perusahaan;
use App\Models\Kategori;
use App\Models\Karyawan;
use App\Models\MutasiMaping;
use App\Models\Pencabutan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
class MapingController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $status = $request->get('status', 'aktif');
    $query = Maping::with(['lokasi', 'perusahaan', 'keluar.masuk.kategori', 'keluar.karyawan'])->where(
      'status',
      $status
    );

    if (auth()->user()->role !== 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }
    /*
    |====================================================
    | FILTER CEPAT (EXACT MATCH → SUPER CEPAT)
    |====================================================
    */

    // Filter Lokasi
    if ($request->filled('lokasi')) {
      $query->where('id_lokasi', $request->lokasi);
    }

    // Filter Perusahaan
    if ($request->filled('perusahaan')) {
      $query->where('id_perusahaan', $request->perusahaan);
    }

    // Filter Tahun Pembelian
    if ($request->filled('tahun')) {
      $query->whereHas('keluar.masuk', function ($q) use ($request) {
        $q->whereYear('tgl_beli', $request->tahun);
      });
    }

    // Filter Nama Barang
    if ($request->filled('barang')) {
      $query->whereHas('keluar.masuk.kategori', function ($q) use ($request) {
        $q->where('id', $request->barang);
      });
    }
    // Filter Merek
    if ($request->filled('merek')) {
      $query->whereHas('keluar.masuk', function ($q) use ($request) {
        $q->where('merek', 'like', '%' . $request->merek . '%');
      });
    }

    // Filter Type
    if ($request->filled('type')) {
      $query->whereHas('keluar.masuk', function ($q) use ($request) {
        $q->where('type', 'like', '%' . $request->type . '%');
      });
    }

    /*
    |====================================================
    | GLOBAL SEARCH (OPTIONAL)
    |====================================================
    */
    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('processor', 'like', "%$search%")
          ->orWhere('device_id', 'like', "%$search%")
          ->orWhere('produk_id', 'like', "%$search%")

          ->orWhereHas('lokasi', function ($l) use ($search) {
            $l->where('nama_lokasi', 'like', "%$search%");
          })

          ->orWhereHas('perusahaan', function ($p) use ($search) {
            $p->where('nama_perusahaan', 'like', "%$search%");
          })

          ->orWhereHas('keluar.karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%$search%");
          })

          ->orWhereHas('keluar.masuk.kategori', function ($b) use ($search) {
            $b->where('nama_barang', 'like', "%$search%");
          });
      });
    }

    $mapings = $query
      ->latest()
      ->paginate(5)
      ->appends(request()->query());

    // Data untuk dropdown filter
    $lokasis = Lokasi::orderBy('nama_lokasi')->get();
    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    $barangs = Kategori::orderBy('nama_barang')->get();

    return view('content.dashboard.maping.index', compact('mapings', 'lokasis', 'perusahaans', 'barangs', 'status'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $user = auth()->user();

    if ($user->role === 'super_admin') {
      $lokasis = collect();

      $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    } else {
      $lokasis = Lokasi::where('perusahaan_id', $user->id_perusahaan)
        ->orderBy('nama_lokasi')
        ->get();

      $perusahaans = collect();
    }

    return view('content.dashboard.maping.create', compact('lokasis', 'perusahaans'));
  }

  public function getBarangByKeluar(Request $request)
  {
    try {
      $request->validate([
        'kode_barang' => 'required',
      ]);

      // 🔍 Cari TANPA filter dulu
      $query = Keluar::with(['masuk.kategori', 'karyawan'])->where('kode_barang', $request->kode_barang);

      // 🔥 SUPER ADMIN FILTER PERUSAHAAN
      if (auth()->user()->role === 'super_admin') {
        if ($request->perusahaan_id) {
          $query->where('id_perusahaan', $request->perusahaan_id);
        }
      } else {
        $query->where('id_perusahaan', auth()->user()->id_perusahaan);
      }

      $keluar = $query->first();

      // ❌ Tidak ada sama sekali
      if (!$keluar) {
        return response()->json([
          'status' => false,
          'message' => 'Kode barang tidak ditemukan',
        ]);
      }

      // ❌ Ada tapi beda perusahaan
      if (auth()->user()->role !== 'super_admin' && $keluar->id_perusahaan != auth()->user()->id_perusahaan) {
        return response()->json([
          'status' => false,
          'message' => 'Kode barang bukan milik perusahaan Anda',
        ]);
      }

      // ❌ Sudah dipakai
      if (Maping::where('id_keluar', $keluar->id)->exists()) {
        return response()->json([
          'status' => true,
          'used' => true,
          'message' => 'Kode barang sudah digunakan',
        ]);
      }

      // ✅ OK
      return response()->json([
        'status' => true,
        'data' => [
          'id_keluar' => $keluar->id,
          'nama_barang' => optional($keluar->masuk->kategori)->nama_barang ?? '-',
          'type' => optional($keluar->masuk)->type ?? '-',
          'merek' => optional($keluar->masuk)->merek ?? '-',
          'warna' => $keluar->warna ?? '-',
          'nama_karyawan' => optional($keluar->karyawan)->nama_karyawan ?? '-',
        ],
      ]);
    } catch (\Throwable $e) {
      return response()->json(
        [
          'status' => false,
          'message' => 'Terjadi kesalahan server',
          'error' => $e->getMessage(),
        ],
        500
      );
    }
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = auth()->user();

    $perusahaanId = $user->role === 'super_admin' ? $request->id_perusahaan : $user->id_perusahaan;
    $request->validate([
      'id_keluar' => [
        'required',
        'exists:keluars,id',
        function ($attribute, $value, $fail) {
          $exists = \App\Models\Maping::where('id_keluar', $value)->exists();
          if ($exists) {
            $fail('Kode barang ini sudah digunakan dan tidak bisa dipakai lagi.');
          }
        },
      ],
      'id_lokasi' => 'required|exists:lokasis,id',
      'id_perusahaan' => $user->role === 'super_admin' ? 'required|exists:perusahaans,id' : 'nullable',
      'processor' => 'nullable|string|max:100',
      'device_id' => [
        'nullable',
        'string',
        'max:50',
        Rule::unique('mapings')->where(function ($q) use ($request) {
          return $q->where('id_perusahaan', $request->id_perusahaan);
        }),
      ],

      'produk_id' => [
        'nullable',
        'string',
        'max:50',
        Rule::unique('mapings')->where(function ($q) use ($request) {
          return $q->where('id_perusahaan', $request->id_perusahaan);
        }),
      ],
      'ram' => 'nullable|integer|min:1',
      'system' => 'nullable|string|max:50',
      'version' => 'nullable|string|max:5',
      'instal_on' => 'nullable|string|max:50',
      'aplikasi' => 'nullable|string|max:100',
      'data_p' => 'nullable|string|max:100',
      'data_n' => 'nullable|string|max:100',
    ]);

    DB::beginTransaction();

    try {
      Maping::create([
        'id_keluar' => $request->id_keluar,
        'id_lokasi' => $request->id_lokasi,
        'id_perusahaan' => $perusahaanId,
        'processor' => $request->processor,
        'device_id' => $request->device_id,
        'produk_id' => $request->produk_id,
        'ram' => $request->ram,
        'system' => $request->system,
        'version' => $request->version,
        'instal_on' => $request->instal_on,
        'aplikasi' => $request->aplikasi,
        'data_p' => $request->data_p,
        'data_n' => $request->data_n,
        'status' => 'aktif',
      ]);

      DB::commit();

      return redirect()
        ->route('maping.index')
        ->with('success', 'Data Maping berhasil disimpan');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('Gagal simpan Maping', [
        'error' => $e->getMessage(),
      ]);

      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat menyimpan data');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show($id)
  {
    $query = Maping::with(['lokasi', 'perusahaan', 'keluar.masuk.kategori', 'keluar.karyawan']);

    if (auth()->user()->role !== 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $maping = $query->findOrFail($id);

    return view('content.dashboard.maping.show', compact('maping'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit($id)
  {
    $user = auth()->user();

    $maping = Maping::with(['keluar.masuk.kategori', 'keluar.karyawan', 'lokasi', 'perusahaan'])->findOrFail($id);

    // proteksi perusahaan
    if ($user->role !== 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }

    // lokasi sesuai perusahaan
    $lokasis = Lokasi::where('id_perusahaan', $maping->id_perusahaan)->get();

    // perusahaan untuk super admin
    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    return view('content.dashboard.maping.edit', compact('maping', 'lokasis', 'perusahaans'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Maping $maping)
  {
    $request->validate([
      'id_keluar' => [
        'required',
        'exists:keluars,id',
        function ($attribute, $value, $fail) use ($maping) {
          $exists = Maping::where('id_keluar', $value)
            ->where('id', '!=', $maping->id) // selain record ini
            ->exists();

          if ($exists) {
            $fail('Kode barang ini sudah digunakan oleh data lain.');
          }
        },
      ],

      'id_lokasi' => 'nullable|exists:lokasis,id',
      'id_perusahaan' => 'nullable|exists:perusahaans,id',

      // spesifikasi nullable
      'processor' => 'nullable|string|max:100',
      'device_id' => [
        'nullable',
        'string',
        'max:50',
        Rule::unique('mapings')
          ->where(fn($q) => $q->where('id_perusahaan', $request->id_perusahaan))
          ->ignore($maping->id),
      ],

      'produk_id' => [
        'nullable',
        'string',
        'max:50',
        Rule::unique('mapings')
          ->where(fn($q) => $q->where('id_perusahaan', $request->id_perusahaan))
          ->ignore($maping->id),
      ],

      'ram' => 'nullable|integer|min:1',
      'system' => 'nullable|string|max:50',
      'version' => 'nullable|string|max:10',
      'instal_on' => 'nullable|date',
      'aplikasi' => 'nullable|string|max:100',
      'data_p' => 'nullable|string|max:255',
      'data_n' => 'nullable|string|max:255',
      'status' => 'required|in:aktif,dicabut',
    ]);

    DB::beginTransaction();

    try {
      $maping->update([
        'id_keluar' => $request->id_keluar,
        'id_lokasi' => $request->id_lokasi,
        'id_perusahaan' => $request->id_perusahaan,
        'processor' => $request->processor,
        'device_id' => $request->device_id,
        'produk_id' => $request->produk_id,
        'ram' => $request->ram,
        'system' => $request->system,
        'version' => $request->version,
        'instal_on' => $request->instal_on,
        'aplikasi' => $request->aplikasi,
        'data_p' => $request->data_p,
        'data_n' => $request->data_n,
        'status' => $request->status,
      ]);

      DB::commit();

      return redirect()
        ->route('maping.index')
        ->with('success', 'Data Maping berhasil diperbarui');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('Gagal update Maping', [
        'error' => $e->getMessage(),
      ]);

      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat update data');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $maping = Maping::findOrFail($id);
    $maping->delete();

    return redirect()
      ->back()
      ->with('success', 'Maping  berhasil dihapus');
  }

  public function print(Request $request)
  {
    $user = auth()->user();

    $query = Maping::with(['lokasi', 'perusahaan', 'keluar.masuk.kategori', 'keluar.karyawan'])->where(
      'status',
      $request->get('status', 'aktif')
    );

    /*
    |====================================================
    | FILTER (SAMA DENGAN INDEX)
    |====================================================
    */

    // Lokasi
    if ($request->filled('lokasi')) {
      $query->where('id_lokasi', $request->lokasi);
    }

    // 🔒 Perusahaan (HANYA SUPER ADMIN BOLEH)
    if ($user->role === 'super_admin' && $request->filled('perusahaan')) {
      $query->where('id_perusahaan', $request->perusahaan);
    }

    // Tahun
    if ($request->filled('tahun')) {
      $query->whereHas('keluar.masuk', function ($q) use ($request) {
        $q->whereYear('tgl_beli', $request->tahun);
      });
    }

    // Barang
    if ($request->filled('barang')) {
      $query->whereHas('keluar.masuk.kategori', function ($q) use ($request) {
        $q->where('id', $request->barang);
      });
    }

    // Merek
    if ($request->filled('merek')) {
      $query->whereHas('keluar.masuk', function ($q) use ($request) {
        $q->where('merek', 'like', '%' . $request->merek . '%');
      });
    }

    // Type
    if ($request->filled('type')) {
      $query->whereHas('keluar.masuk', function ($q) use ($request) {
        $q->where('type', 'like', '%' . $request->type . '%');
      });
    }

    /*
    |====================================================
    | SEARCH GLOBAL
    |====================================================
    */
    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('processor', 'like', "%$search%")
          ->orWhere('device_id', 'like', "%$search%")
          ->orWhere('produk_id', 'like', "%$search%")

          ->orWhereHas('lokasi', function ($l) use ($search) {
            $l->where('nama_lokasi', 'like', "%$search%");
          })

          ->orWhereHas('perusahaan', function ($p) use ($search) {
            $p->where('nama_perusahaan', 'like', "%$search%");
          })

          ->orWhereHas('keluar.karyawan', function ($k) use ($search) {
            $k->where('nama_karyawan', 'like', "%$search%");
          })

          ->orWhereHas('keluar.masuk.kategori', function ($b) use ($search) {
            $b->where('nama_barang', 'like', "%$search%");
          });
      });
    }

    /*
    |====================================================
    | GET DATA
    |====================================================
    */
    $mapings = $query->latest()->get();

    /*
    |====================================================
    | NAMA PERUSAHAAN
    |====================================================
    */
    if ($user->role === 'super_admin') {
      $namaPerusahaan = $request->filled('perusahaan')
        ? optional(Perusahaan::find($request->perusahaan))->nama_perusahaan
        : 'SEMBILAN GROUP';
    } else {
      $namaPerusahaan = $user->perusahaan->nama_perusahaan ?? 'Perusahaan';
    }

    return view('content.dashboard.maping.print', compact('mapings', 'namaPerusahaan'));
  }

  public function getLokasiByPerusahaan($id)
  {
    $lokasis = \App\Models\Lokasi::where('id_perusahaan', $id)
      ->orderBy('nama_lokasi')
      ->get();

    return response()->json($lokasis);
  }
  public function mutasiForm($id)
  {
    $maping = Maping::findOrFail($id);
    $lokasi = Lokasi::all();

    $user = auth()->user();

    if ($user->role === 'super_admin') {
      $perusahaan = Perusahaan::all();
      $modePerusahaan = 'select';
    } else {
      $perusahaan = $user->perusahaan; // single object
      $modePerusahaan = 'fixed';
    }

    return view('content.dashboard.maping.mutasi', compact('maping', 'lokasi', 'perusahaan', 'modePerusahaan'));
  }

  public function mutasiStore(Request $request, $id)
  {
    $request->validate([
      'ke_lokasi' => 'required|exists:lokasis,id',
      'ke_perusahaan' => 'required|exists:perusahaans,id',
      'tanggal_mutasi' => 'required|date',
    ]);

    $maping = Maping::with('keluar')->findOrFail($id);

    DB::beginTransaction();

    try {
      // =====================================
      // SIMPAN HISTORY MUTASI
      // =====================================

      MutasiMaping::create([
        // RELASI
        'id_maping' => $maping->id,

        // 🔥 WAJIB UNTUK MULTI PERUSAHAAN
        'id_perusahaan' => $request->ke_perusahaan,

        // =================================
        // DARI
        // =================================

        'dari_lokasi' => $maping->id_lokasi,
        'dari_perusahaan' => $maping->id_perusahaan,
        'dari_karyawan' => optional($maping->keluar)->id_karyawan,

        'dari_no_inventaris' => optional($maping->keluar)->no_inventaris,
        'dari_aplikasi' => $maping->aplikasi,
        'dari_data_ppn' => $maping->data_p,
        'dari_data_non_ppn' => $maping->data_n,

        // =================================
        // KE
        // =================================

        'ke_lokasi' => $request->ke_lokasi,
        'ke_perusahaan' => $request->ke_perusahaan,

        'ke_karyawan' => $request->ke_karyawan ?: optional($maping->keluar)->id_karyawan,

        'ke_no_inventaris' => $request->ke_no_inventaris,

        'ke_aplikasi' => $request->ke_aplikasi,

        'ke_data_ppn' => $request->ke_data_ppn,

        'ke_data_non_ppn' => $request->ke_data_non_ppn,

        // =================================
        // LAINNYA
        // =================================

        'tanggal_mutasi' => $request->tanggal_mutasi,

        'keterangan' => $request->keterangan,
      ]);

      // =====================================
      // UPDATE MAPING
      // =====================================

      $maping->update([
        'id_lokasi' => $request->ke_lokasi,

        'id_perusahaan' => $request->ke_perusahaan,

        'aplikasi' => $request->ke_aplikasi,

        'data_p' => $request->ke_data_ppn,

        'data_n' => $request->ke_data_non_ppn,
      ]);

      // =====================================
      // UPDATE KELUAR
      // =====================================

      if ($maping->keluar) {
        $maping->keluar->update([
          'id_karyawan' => $request->ke_karyawan ?: $maping->keluar->id_karyawan,

          'no_inventaris' => $request->ke_no_inventaris ?: $maping->keluar->no_inventaris,
        ]);
      }

      DB::commit();

      return redirect()
        ->route('maping.historyGlobal')
        ->with('success', 'Mutasi berhasil disimpan');
    } catch (\Exception $e) {
      DB::rollBack();

      dd($e->getMessage());
    }
  }
  public function searchKaryawan(Request $request)
  {
    $q = $request->q;
    $perusahaanId = $request->perusahaan_id;

    $karyawan = \App\Models\Karyawan::query()

      ->when($perusahaanId, function ($query) use ($perusahaanId) {
        $query->where('id_perusahaan', $perusahaanId);
      })

      ->when($q, function ($query) use ($q) {
        $query->where('nama_karyawan', 'like', '%' . $q . '%');
      })

      ->limit(10)

      ->get(['id', 'nama_karyawan']);

    return response()->json($karyawan);
  }

  public function historyGlobal(Request $request)
  {
    $query = Maping::with(['keluar.masuk.kategori', 'perusahaan']);

    // =====================================
    // FILTER SUPER ADMIN
    // =====================================

    if (auth()->user()->role === 'super_admin') {
      if ($request->perusahaan) {
        $query->where('id_perusahaan', $request->perusahaan);
      }

      $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    } else {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);

      $perusahaans = [];
    }

    // =====================================
    // PAGINATION
    // =====================================

    $mapings = $query
      ->latest()
      ->paginate(5)
      ->appends($request->query());

    return view('content.dashboard.maping.history_global', compact('mapings', 'perusahaans'));
  }

  public function destroyMutasi($id)
  {
    $mutasi = \App\Models\MutasiMaping::findOrFail($id);
    $mutasi->delete();

    if (request()->ajax()) {
      return response()->json([
        'success' => true,
      ]);
    }

    return back()->with('success', 'Data berhasil dihapus');
  }
  public function cabut(Request $request, $id)
  {
    $maping = Maping::with(['keluar.karyawan'])->findOrFail($id);

    DB::beginTransaction();

    try {
      Pencabutan::create([
        'id_maping' => $maping->id,
        'id_keluar' => $maping->id_keluar,
        'id_lokasi' => $maping->id_lokasi,
        'id_perusahaan' => $maping->id_perusahaan,
        'id_karyawan' => optional($maping->keluar)->id_karyawan,
        'tanggal_cabut' => $request->tanggal_cabut,
        'kondisi' => $request->kondisi,
        'alasan' => $request->alasan,
      ]);

      $maping->update([
        'status' => 'dicabut',
      ]);

      DB::commit();

      return redirect()
        ->route('maping.historyCabut')
        ->with('success', 'Inventaris berhasil dicabut');
    } catch (\Exception $e) {
      DB::rollBack();

      return back()->with('error', $e->getMessage());
    }
  }
  public function historyCabut()
  {
    $query = Pencabutan::with(['lokasi', 'perusahaan', 'karyawan', 'keluar.masuk.kategori']);

    // 🔒 FILTER PERUSAHAAN
    if (auth()->user()->role !== 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $pencabutans = $query->latest()->paginate(10);

    return view('content.dashboard.maping.history_cabut', compact('pencabutans'));
  }
  public function hapusCabut($id)
  {
    $pencabutan = Pencabutan::findOrFail($id);

    $pencabutan->delete();

    return back()->with('success', 'History pencabutan berhasil dihapus');
  }
  public function historyUser($id)
  {
    // dd(auth()->user()->role);
    $query = MutasiMaping::with([
      'dariLokasi',
      'keLokasi',
      'dariPerusahaan',
      'kePerusahaan',
      'dariKaryawan',
      'keKaryawan',
      'maping', // 🔥 WAJIB
    ])->where('id_maping', $id);

    // 🔥 FILTER PERUSAHAAN
    if (auth()->user()->role !== 'super_admin') {
      $query->whereHas('maping', function ($q) {
        $q->where('id_perusahaan', auth()->user()->id_perusahaan);
      });
    }

    $histories = $query->get();

    return view('content.dashboard.maping.history_user', compact('histories'));
  }
  public function detailAjax($id)
  {
    $query = Maping::with(['lokasi', 'perusahaan', 'keluar.masuk.kategori', 'keluar.karyawan']);

    if (auth()->user()->role !== 'super_admin') {
      $query->where('id_perusahaan', auth()->user()->id_perusahaan);
    }

    $maping = $query->findOrFail($id);

    return view('content.dashboard.maping.detail_ajax', compact('maping'));
  }
}

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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Maping::with(['lokasi', 'perusahaan', 'keluar.masuk.kategori', 'keluar.karyawan']);

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
            ->paginate(7)
            ->appends(request()->query());

        // Data untuk dropdown filter
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
        $barangs = Kategori::orderBy('nama_barang')->get();

        return view('content.dashboard.maping.index', compact('mapings', 'lokasis', 'perusahaans', 'barangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.dashboard.maping.create', [
            'lokasis' => Lokasi::orderBy('nama_lokasi')->get(),
            'perusahaans' => Perusahaan::orderBy('nama_perusahaan')->get(),
        ]);
    }

    public function getBarangByKeluar(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required',
        ]);

        $keluar = Keluar::with(['masuk.kategori', 'karyawan'])
            ->where('kode_barang', $request->kode_barang)
            ->first();

        if (!$keluar) {
            return response()->json([
                'status' => false,
                'message' => 'Kode barang tidak ditemukan',
            ]);
        }

        if (Maping::where('id_keluar', $keluar->id)->exists()) {
            return response()->json([
                'status' => true, // ← ubah jadi TRUE
                'used' => true,
                'message' => 'Kode barang sudah digunakan',
            ]);
        }

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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            'id_perusahaan' => 'required|exists:perusahaans,id',
            'processor' => 'nullable|string|max:100',
            'device_id' => 'nullable|string|max:50|unique:mapings,device_id',
            'produk_id' => 'nullable|string|max:50|unique:mapings,produk_id',
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
        $maping = Maping::with(['lokasi', 'keluar', 'perusahaan'])->findOrFail($id);

        return view('content.dashboard.maping.show', compact('maping'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maping $maping)
    {
        return view('content.dashboard.maping.edit', [
            'maping' => $maping->load(['keluar', 'lokasi', 'perusahaan']),
            'lokasis' => Lokasi::orderBy('nama_lokasi')->get(),
            'perusahaans' => Perusahaan::orderBy('nama_perusahaan')->get(),
        ]);
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

            'device_id' => ['nullable', 'string', 'max:50', 'unique:mapings,device_id,' . $maping->id],

            'produk_id' => ['nullable', 'string', 'max:50', 'unique:mapings,produk_id,' . $maping->id],

            'ram' => 'nullable|integer|min:1',
            'system' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:10',
            'instal_on' => 'nullable|date',
            'aplikasi' => 'nullable|string|max:100',
            'data_p' => 'nullable|string|max:255',
            'data_n' => 'nullable|string|max:255',
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
        $query = Maping::with(['lokasi', 'perusahaan', 'keluar.masuk.kategori', 'keluar.karyawan']);

        // FILTER SAMA DENGAN INDEX
        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        if ($request->filled('perusahaan')) {
            $query->where('id_perusahaan', $request->perusahaan);
        }

        if ($request->filled('tahun')) {
            $query->whereHas('keluar.masuk', function ($q) use ($request) {
                $q->whereYear('tgl_beli', $request->tahun);
            });
        }

        if ($request->filled('barang')) {
            $query->whereHas('keluar.masuk.kategori', function ($q) use ($request) {
                $q->where('id', $request->barang);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('processor', 'like', "%$search%")
                    ->orWhere('device_id', 'like', "%$search%")
                    ->orWhere('produk_id', 'like', "%$search%");
            });
        }

        $mapings = $query->get();

        // ===============================
        // PENENTU JUDUL PERUSAHAAN (FIX)
        // ===============================
        if ($request->filled('perusahaan') && $mapings->count()) {
            $namaPerusahaan = optional($mapings->first()->perusahaan)->nama_perusahaan ?? 'Sembilan Group';
        } else {
            $namaPerusahaan = 'Sembilan Group';
        }

        return view('content.dashboard.maping.print', compact('mapings', 'namaPerusahaan'));
    }
    public function mutasiForm($id)
    {
        $maping = Maping::findOrFail($id);
        $lokasi = Lokasi::all();
        $perusahaan = Perusahaan::all();
        $karyawan = Karyawan::all();

        return view('content.dashboard.maping.mutasi', compact('maping', 'lokasi', 'perusahaan', 'karyawan'));
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
            MutasiMaping::create([
                'id_maping' => $maping->id,

                'dari_lokasi' => $maping->id_lokasi,
                'ke_lokasi' => $request->ke_lokasi,

                'dari_perusahaan' => $maping->id_perusahaan,
                'ke_perusahaan' => $request->ke_perusahaan,

                'dari_karyawan' => optional($maping->keluar)->id_karyawan,
                'ke_karyawan' => $request->ke_karyawan ?: optional($maping->keluar)->id_karyawan,

                'dari_no_inventaris' => optional($maping->keluar)->no_inventaris,
                'ke_no_inventaris' => $request->ke_no_inventaris,

                'dari_aplikasi' => $maping->aplikasi,
                'ke_aplikasi' => $request->ke_aplikasi,

                'dari_data_ppn' => $maping->data_p,
                'ke_data_ppn' => $request->ke_data_ppn,

                'dari_data_non_ppn' => $maping->data_n,
                'ke_data_non_ppn' => $request->ke_data_non_ppn,

                'tanggal_mutasi' => $request->tanggal_mutasi,
                'keterangan' => $request->keterangan,
            ]);

            // UPDATE MAPING
            $maping->update([
                'id_lokasi' => $request->ke_lokasi,
                'id_perusahaan' => $request->ke_perusahaan,
                'aplikasi' => $request->ke_aplikasi,
                'data_p' => $request->ke_data_ppn,
                'data_n' => $request->ke_data_non_ppn,
            ]);

            // UPDATE KELUAR
            if ($maping->keluar) {
                $maping->keluar->update([
                    'id_karyawan' => $request->ke_karyawan ?: $maping->keluar->id_karyawan,
                    'no_inventaris' => $request->ke_no_inventaris ?: $maping->keluar->no_inventaris,
                ]);
            }

            DB::commit();

            return redirect()->route('maping.historyGlobal')
                ->with('success', 'Mutasi berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }



    public function historyGlobal()
    {
        $mutasis = MutasiMaping::with([
            'maping.keluar.masuk.kategori',
            'dariLokasi',
            'keLokasi',
            'dariPerusahaan',
            'kePerusahaan',
            'dariKaryawan',
            'keKaryawan'
        ])
            ->latest()
            ->paginate(10);

        return view('content.dashboard.maping.history_global', compact('mutasis'));
    }


    public function searchKaryawan(Request $request)
    {
        $q = $request->q;

        return \App\Models\Karyawan::where('nama_karyawan', 'like', "%$q%")
            ->limit(10)
            ->get(['id', 'nama_karyawan']);
    }

    public function destroyMutasi($id)
    {
        $mutasi = \App\Models\MutasiMaping::findOrFail($id);
        $mutasi->delete();

        return redirect()->back()->with('success', 'Data mutasi berhasil dihapus');
    }
}

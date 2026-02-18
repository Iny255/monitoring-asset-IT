<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Keluar;
use App\Models\Masuk;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Keluar::with([
            'masuk.kategori',
            'karyawan'
        ]);

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // ================= KOLOM DI TABEL KELUAR =================
                $q->where('kode_keluar', 'like', "%$search%")
                    ->orWhere('kode_barang', 'like', "%$search%")
                    ->orWhere('warna', 'like', "%$search%");

                // ================= RELASI MASUK =================
                $q->orWhereHas('masuk', function ($m) use ($search) {
                    $m->where('kode_masuk', 'like', "%$search%")
                        ->orWhere('type', 'like', "%$search%")
                        ->orWhere('merek', 'like', "%$search%");
                });

                // ================= RELASI KATEGORI (NAMA BARANG) =================
                $q->orWhereHas('masuk.kategori', function ($k) use ($search) {
                    $k->where('nama_barang', 'like', "%$search%");
                });

                // ================= RELASI KARYAWAN =================
                $q->orWhereHas('karyawan', function ($k) use ($search) {
                    $k->where('nama_karyawan', 'like', "%$search%")
                        ->orWhere('divisi', 'like', "%$search%")
                        ->orWhere('perusahaan', 'like', "%$search%");
                });
            });
        }

        $keluars = $query->orderByDesc('id')->paginate(5);

        return view('content.dashboard.transaksi-keluar.index', compact('keluars'));
    }

    private function generateKodeKeluar()
    {
        $tahun = Carbon::now()->year;

        $last = Keluar::whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->kode_keluar, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'KLR-' . $tahun . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kodeKeluar = $this->generateKodeKeluar();
        $karyawans  = Karyawan::orderBy('nama_karyawan')->get();

        return view(
            'content.dashboard.transaksi-keluar.create',
            compact('kodeKeluar', 'karyawans')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_keluar'   => 'required|unique:keluars,kode_keluar',
            'id_masuk'      => 'required|exists:masuks,id',
            'kode_barang'   => 'required|unique:keluars,kode_barang',
            'jumlah'        => 'required|integer|min:1',
            'keterangan'    => 'required|string|max:100',
            'warna'         => 'required|string|max:50',
            'no_inventaris' => 'required|string|max:50',
            'jenis_penerima' => 'required|in:Perorangan,Perdivisi',
        ]);

        // PERORANGAN
        if ($request->jenis_penerima == 'Perorangan') {
            $request->validate([
                'id_karyawan' => 'required|exists:karyawans,id'
            ]);
        }

        // DIVISI
        if ($request->jenis_penerima == 'Perdivisi') {
            $request->validate([
                'divisi_klr' => 'required',
                'perusahaan_klr' => 'required'
            ]);
        }

        DB::beginTransaction();

        try {
            // 🔒 LOCK STOK
            $masuk = Masuk::where('id', $request->id_masuk)
                ->lockForUpdate()
                ->firstOrFail();

            // ❌ VALIDASI STOK
            if ($request->jumlah > $masuk->jumlah) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('error', 'Jumlah keluar melebihi stok tersedia!');
            }

            // ✅ SIMPAN
            $keluar = Keluar::create([
                'kode_keluar'    => $request->kode_keluar,
                'id_masuk'       => $request->id_masuk,
                'kode_barang'    => $request->kode_barang,
                'jumlah'         => $request->jumlah,
                'jenis_penerima' => $request->jenis_penerima,

                // PERORANGAN
                'id_karyawan'    => $request->jenis_penerima == 'Perorangan'
                    ? $request->id_karyawan
                    : null,

                // DIVISI MANUAL
                'divisi_klr'         => $request->jenis_penerima == 'Perdivisi'
                    ? $request->divisi_klr
                    : null,

                'perusahaan_klr'     => $request->jenis_penerima == 'Perdivisi'
                    ? $request->perusahaan_klr
                    : null,

                'keterangan'     => $request->keterangan,
                'warna'          => $request->warna,
                'no_inventaris'  => $request->no_inventaris,
            ]);


            // ➖ KURANGI STOK
            $masuk->decrement('jumlah', $request->jumlah);

            DB::commit();

            return redirect()
                ->route('transaksi-keluar.index')
                ->with('success', 'Transaksi keluar berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan, silakan ulangi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $keluar = Keluar::with([
            'masuk.kategori',
            'karyawan'
        ])->findOrFail($id);

        return view('content.dashboard.transaksi-keluar.show', compact('keluar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $keluar = Keluar::with(['masuk.kategori', 'karyawan'])->findOrFail($id);

        return view(
            'content.dashboard.transaksi-keluar.edit',
            compact('keluar')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_masuk'      => 'required|exists:masuks,id',
            'kode_barang'   => 'required|unique:keluars,kode_barang,' . $id,
            'jumlah'        => 'required|integer|min:1',
            'keterangan'    => 'required|max:50',
            'warna'         => 'required|max:50',
            'no_inventaris' => 'required|max:50',
            'jenis_penerima' => 'required|in:Perorangan,Perdivisi',
        ]);

        // PERORANGAN
        if ($request->jenis_penerima == 'Perorangan') {
            $request->validate([
                'id_karyawan' => 'required|exists:karyawans,id'
            ]);
        }

        // PERDIVISI
        if ($request->jenis_penerima == 'Perdivisi') {
            $request->validate([
                'divisi_klr' => 'required',
                'perusahaan_klr' => 'required'
            ]);
        }


        DB::beginTransaction();

        try {
            $keluar = Keluar::findOrFail($id);

            // 🔄 KEMBALIKAN STOK LAMA
            $masukLama = Masuk::lockForUpdate()->find($keluar->id_masuk);
            $masukLama->increment('jumlah', $keluar->jumlah);

            // 🔒 LOCK STOK BARU
            $masukBaru = Masuk::lockForUpdate()->findOrFail($request->id_masuk);

            if ($request->jumlah > $masukBaru->jumlah) {
                DB::rollBack();
                return back()->with('error', 'Jumlah keluar melebihi stok!');
            }

            // ✏️ UPDATE DATA
            $keluar->update([
                'id_masuk' => $request->id_masuk,
                'kode_barang' => $request->kode_barang,
                'jumlah' => $request->jumlah,
                'jenis_penerima' => $request->jenis_penerima,

                'id_karyawan' => $request->jenis_penerima == 'Perorangan'
                    ? $request->id_karyawan
                    : null,

                'divisi_klr' => $request->jenis_penerima == 'Perdivisi'
                    ? $request->divisi_klr
                    : null,

                'perusahaan_klr' => $request->jenis_penerima == 'Perdivisi'
                    ? $request->perusahaan_klr
                    : null,

                'keterangan' => $request->keterangan,
                'warna' => $request->warna,
                'no_inventaris' => $request->no_inventaris,
            ]);


            // ➖ KURANGI STOK BARU
            $masukBaru->decrement('jumlah', $request->jumlah);

            DB::commit();

            return redirect()
                ->route('transaksi-keluar.index')
                ->with('success', 'Transaksi keluar berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $keluar = Keluar::with('masuk')->findOrFail($id);

            // 🔒 LOCK DATA MASUK
            $masuk = Masuk::where('id', $keluar->id_masuk)
                ->lockForUpdate()
                ->first();

            // ➕ KEMBALIKAN STOK
            if ($masuk) {
                $masuk->increment('jumlah', $keluar->jumlah);
            }

            // 🗑️ HAPUS TRANSAKSI KELUAR
            $keluar->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Transaksi keluar berhasil dihapus dan stok dikembalikan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus data');
        }
    }
    public function getMasukByKode($kode)
    {
        $masuk = Masuk::with('kategori')
            ->where('kode_masuk', $kode)
            ->first();

        if (!$masuk) {
            return response()->json(['status' => false]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id_masuk'   => $masuk->id,
                'nama_barang' => $masuk->kategori->nama_barang ?? '-',
                'type'       => $masuk->type,
                'merek'      => $masuk->merek,
                'tgl_beli'   => $masuk->tgl_beli,
                'stok'       => $masuk->jumlah
            ]
        ]);
    }
    public function autofillByKodeMasuk(Request $request)
    {
        $masuk = \App\Models\Masuk::where('kode_masuk', $request->kode_masuk)->first();

        if (!$masuk) {
            return response()->json([
                'status' => false
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id_masuk'    => $masuk->id,
                'nama_barang' => $masuk->kategori->nama_barang,   // <- seperti dulu
                'type'        => $masuk->type,
                'merek'       => $masuk->merek,
                'tgl_beli'    => $masuk->tgl_beli,
            ]
        ]);
    }


    public function getKaryawanByNama(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required'
        ]);

        $karyawan = Karyawan::where('nama_karyawan', $request->nama_karyawan)->first();

        if (!$karyawan) {
            return response()->json(['status' => false]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id'         => $karyawan->id,
                'divisi'     => $karyawan->divisi,
                'perusahaan' => $karyawan->perusahaan,
            ]
        ]);
    }
}

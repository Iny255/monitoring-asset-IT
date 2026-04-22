<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Kategori;
use App\Models\Keluar;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with(['karyawan', 'kategori', 'keluar.masuk.kategori', 'perusahaan', 'lokasi']);

        // ================= SEARCH =================
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Cari KODE BARANG (dari tabel keluar)
                $q->whereHas('keluar', function ($k) use ($search) {
                    $k->where('kode_barang', 'like', "%{$search}%");
                })

                    // Cari NAMA BARANG (dari kategori lewat masuk)
                    ->orWhereHas('keluar.masuk.kategori', function ($k) use ($search) {
                        $k->where('nama_barang', 'like', "%{$search}%");
                    })

                    // Cari NAMA KARYAWAN
                    ->orWhereHas('karyawan', function ($k) use ($search) {
                        $k->where('nama_karyawan', 'like', "%{$search}%");
                    });
            });
        }

        // ================= ORDER & PAGINATION =================
        $peminjamans = $query->orderBy('created_at', 'desc')->paginate(5);

        return view('content.dashboard.peminjaman.index', compact('peminjamans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $keluars = Keluar::orderBy('kode_barang')->get();
        $kategoris = Kategori::orderBy('nama_barang')->get();
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();

        return view(
            'content.dashboard.peminjaman.create',
            compact('keluars', 'kategoris', 'karyawans', 'perusahaans', 'lokasis')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ================= VALIDASI =================
        $data = $request->all();

        if ($request->tipe_peminjam == 'internal') {
            $data['karyawan_id'] = $request->karyawan_id;
            $data['nama_eksternal'] = null;
            $data['perusahaan_eksternal'] = null;
        } else {
            $data['karyawan_id'] = null;
        }

        Peminjaman::create($data);

        return redirect()->route('peminjaman.index')
            ->with('success', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with(['keluar.masuk.kategori', 'karyawan', 'perusahaan', 'lokasi'])->findOrFail($id);

        return view('content.dashboard.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $peminjaman = Peminjaman::with(['keluar.masuk.kategori', 'karyawan', 'perusahaan', 'lokasi'])->findOrFail($id);

        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();

        return view('content.dashboard.peminjaman.edit', compact('peminjaman', 'perusahaans', 'lokasis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $request->validate([
            'keluar_id' => 'required|exists:keluars,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'perusahaan_id' => 'required|exists:perusahaans,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_rencana_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'status' => 'required|in:Dipinjam,Dikembalikan,Hilang,Rusak',
            'keperluan' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:255',
        ]);

        $dataUpdate = [
            'keluar_id' => $request->keluar_id,
            'karyawan_id' => $request->karyawan_id,
            'perusahaan_id' => $request->perusahaan_id,
            'lokasi_id' => $request->lokasi_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_rencana_kembali' => $request->tanggal_rencana_kembali,
            'status' => $request->status,
            'keperluan' => $request->keperluan,
            'catatan' => $request->catatan,
        ];

        // Jika status DIKEMBALIKAN → isi tanggal_kembali
        if ($request->status === 'Dikembalikan') {
            $dataUpdate['tanggal_kembali'] = now();
        } else {
            $dataUpdate['tanggal_kembali'] = null;
        }

        $peminjaman->update($dataUpdate);

        return redirect()
            ->route('peminjaman.index')
            ->with('success', 'Data peminjaman berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        return redirect()
            ->route('peminjaman.index')
            ->with('success', 'Data peminjaman berhasil dihapus');
    }

    public function getNamaBarang($kode)
    {
        $data = Keluar::with('masuk.kategori')
            ->where('kode_barang', $kode)
            ->first();

        if (!$data || !$data->masuk || !$data->masuk->kategori) {
            return response()->json([
                'status' => 'not_found',
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'nama_barang' => $data->masuk->kategori->nama_barang,
            'keluar_id' => $data->id,
        ]);
    }

    public function searchKaryawan(Request $request)
    {
        $keyword = $request->q;

        return Karyawan::where('nama_karyawan', 'like', "%$keyword%")
            ->limit(10)
            ->get(['id', 'nama_karyawan']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Maping;
use App\Models\Lokasi;
use App\Models\Perusahaan;
use App\Models\Kategori;
use Illuminate\Http\Request;

class ManagerMapingController extends Controller
{
    public function maping(Request $request)
    {
        $status = $request->get('status', 'aktif');

        $query = Maping::with([
            'lokasi',
            'perusahaan',
            'keluar.masuk.kategori',
            'keluar.karyawan'
        ])->where('status', $status);

        // FILTER
        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        if ($request->filled('perusahaan')) {
            $query->where('id_perusahaan', $request->perusahaan);
        }

        if ($request->filled('barang')) {
            $query->whereHas('keluar.masuk.kategori', function ($q) use ($request) {
                $q->where('id', $request->barang);
            });
        }

        if ($request->filled('tahun')) {
            $query->whereHas('keluar.masuk', function ($q) use ($request) {
                $q->whereYear('tgl_beli', $request->tahun);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('processor', 'like', "%$search%")
                    ->orWhere('device_id', 'like', "%$search%");
            });
        }

        $mapings = $query->latest()->paginate(5)->appends(request()->query());

        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
        $barangs = Kategori::orderBy('nama_barang')->get();

        return view('content.dashboard.maping.index', compact(
            'mapings',
            'lokasis',
            'perusahaans',
            'barangs'
        ));
    }

    public function showmaping($id)
    {
        $maping = Maping::with([
            'lokasi',
            'perusahaan',
            'keluar.masuk.kategori',
            'keluar.karyawan'
        ])->findOrFail($id);

        return view('content.dashboard.maping.show', compact('maping'));
    }

    public function cetakmaping(Request $request)
    {
        $query = Maping::with([
            'lokasi',
            'perusahaan',
            'keluar.masuk.kategori',
            'keluar.karyawan'
        ]);

        // FILTER SAMA
        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        if ($request->filled('perusahaan')) {
            $query->where('id_perusahaan', $request->perusahaan);
        }

        $mapings = $query->get();

        return view('content.dashboard.maping.print', compact('mapings'));
    }
}

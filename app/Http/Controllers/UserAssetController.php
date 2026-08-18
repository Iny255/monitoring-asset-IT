<?php

namespace App\Http\Controllers;

use App\Models\Maping;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAssetController extends Controller
{
    /**
     * Display a listing of assets assigned to the logged-in employee/user.
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $mapings = collect();
        $peminjamans = collect();

        if ($user->karyawan_id) {
            // Ambil data mapping aset yang dialokasikan untuk karyawan ini
            $mapings = Maping::with([
                'keluar.inventaris.dataAset.kategori',
                'lokasi',
                'perusahaan'
            ])
            ->where('karyawan_id', $user->karyawan_id)
            ->where('status', '!=', 'Batal')
            ->orderBy('id', 'desc')
            ->get();

            // Ambil data peminjaman aset sementara
            $peminjamans = Peminjaman::with([
                'inventaris.dataAset.kategori',
                'perusahaanTujuan'
            ])
            ->where(function ($q) use ($user) {
                $q->where('karyawan_id', $user->karyawan_id)
                  ->orWhere('karyawan_tujuan_id', $user->karyawan_id);
            })
            ->orderBy('id', 'desc')
            ->get();
        }

        return view('content.dashboard.user_asset.index', compact('user', 'karyawan', 'mapings', 'peminjamans'));
    }
}

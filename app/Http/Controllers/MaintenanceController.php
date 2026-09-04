<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Inventaris;
use App\Models\Maping;
use App\Models\Peminjaman;
use App\Models\Keluar;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class MaintenanceController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $query = Maintenance::with(['inventaris.dataAset.kategori', 'inventaris.perusahaan', 'creator']);

    // Super Admin
    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $query->whereHas('inventaris', function ($q) use ($request) {
          $q->where('perusahaan_id', $request->perusahaan_id);
        });
      }
    } else {
      $accessibleIds = auth()->user()->getAccessibleCompanyIds();
      $query->whereHas('inventaris', function ($q) use ($accessibleIds) {
        $q->whereIn('perusahaan_id', $accessibleIds);
      });
    }

    // Search
    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_service', 'like', "%{$search}%")->orWhereHas('inventaris', function ($qq) use ($search) {
          $qq->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
        });
      });
    }

    // Filter Status
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    // Filter Jenis
    if ($request->filled('jenis')) {
      $query->where('jenis', $request->jenis);
    }
    /*
|--------------------------------------------------------------------------
| FILTER TANGGAL
|--------------------------------------------------------------------------
*/

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
    }

    $maintenances = $query
      ->latest()
      ->paginate(10)
      ->appends($request->query());
    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    return view('content.dashboard.maintenance.index', compact('maintenances', 'perusahaans'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $user = auth()->user();

    if ($user->role == 'super_admin') {
      $inventarisList = collect();
      $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
      $kategoris = collect();
    } else {
      $inventarisList = collect();
      $perusahaans = collect();

      $accessibleIds = $user->getAccessibleCompanyIds();
      if ($accessibleIds) {
        $kategoris = \App\Models\Kategori::whereIn('perusahaan_id', $accessibleIds)
          ->orderBy('nama_barang')
          ->get();
      } elseif ($user->id_perusahaan) {
        $kategoris = \App\Models\Kategori::where('perusahaan_id', $user->id_perusahaan)
          ->orderBy('nama_barang')
          ->get();
      } else {
        $kategoris = collect();
      }
    }

    return view('content.dashboard.maintenance.create', [
      'inventaris' => null,
      'inventarisList' => $inventarisList,
      'perusahaans' => $perusahaans,
      'kategoris' => $kategoris,
    ]);
  }
  public function createFromMapping(Maping $maping)
  {
    if ($maping->status != 'aktif') {
      return redirect()
        ->route('maping.index')
        ->with('error', 'Mapping sudah tidak aktif sehingga tidak dapat dilakukan Service / Maintenance.');
    }

    $maping->load(['keluar.inventaris.dataAset', 'keluar.inventaris.perusahaan']);

    $inventaris = $maping->keluar->inventaris;

    if (!$inventaris) {
      return redirect()
        ->route('maping.index')
        ->with('error', 'Inventaris tidak ditemukan.');
    }

    $serviceAktif = Maintenance::where('inventaris_id', $inventaris->id)
      ->whereIn('status', ['Pengajuan', 'Diproses'])
      ->exists();

    if ($serviceAktif) {
      return redirect()
        ->route('maintenance.index')
        ->with('warning', 'Inventaris sedang dalam proses Service & Maintenance.');
    }

    return view('content.dashboard.maintenance.create', [
      'perusahaans' => Perusahaan::orderBy('nama_perusahaan')->get(),
      'inventaris' => $inventaris,
      'inventarisList' => collect(),
      'maping' => $maping,
    ]);
  }
  public function createFromPeminjaman(Peminjaman $peminjaman)
  {
    $peminjaman->load(['inventaris.dataAset.kategori', 'inventaris.perusahaan']);

    $inventaris = $peminjaman->inventaris;

    if (!$inventaris) {
      return redirect()
        ->route('peminjaman.index')
        ->with('error', 'Inventaris tidak ditemukan.');
    }

    $serviceAktif = Maintenance::where('inventaris_id', $inventaris->id)
      ->whereIn('status', ['Pengajuan', 'Diproses'])
      ->exists();

    if ($serviceAktif) {
      return redirect()
        ->route('peminjaman.index')
        ->with('warning', 'Inventaris sedang dalam proses Service.');
    }

    $inventarisList = collect();

    return view('content.dashboard.maintenance.create', [
      'perusahaans' => Perusahaan::orderBy('nama_perusahaan')->get(),
      'inventaris' => $inventaris,
      'inventarisList' => collect(),
      'peminjaman' => $peminjaman,
    ]);
  }
  public function inventarisPerusahaan($id)
  {
    $user = auth()->user();
    if ($user->role != 'super_admin') {
      $accessibleIds = $user->getAccessibleCompanyIds();
      if ($accessibleIds && !$accessibleIds->contains($id)) {
        return response()->json([], 403);
      } elseif (!$accessibleIds && $user->id_perusahaan != $id) {
        return response()->json([], 403);
      }
    }

    return Inventaris::with(['dataAset.kategori', 'perusahaan'])
      ->availableForMaintenance()
      ->where('perusahaan_id', $id)
      ->orderBy('kode_aset')
      ->get();
  }
  public function inventarisByKategori(Request $request, $kategori)
  {
    $query = Inventaris::with(['dataAset.kategori', 'perusahaan'])
      ->availableForMaintenance()
      ->whereHas('dataAset', function ($q) use ($kategori) {
        $q->where('kategori_id', $kategori);
      });

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan')) {
        $query->where('perusahaan_id', $request->perusahaan);
      }
    } else {
      $accessibleIds = auth()->user()->getAccessibleCompanyIds();
      if ($accessibleIds) {
        $query->whereIn('perusahaan_id', $accessibleIds);
      } elseif (auth()->user()->id_perusahaan) {
        $query->where('perusahaan_id', auth()->user()->id_perusahaan);
      }
    }

    return response()->json($query->orderBy('kode_aset')->get());
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'inventaris_id' => 'required|exists:inventaris,id',
      'tanggal' => 'required|date',
      'jenis' => 'required|in:Service,Maintenance',
      'kategori' => 'nullable|in:Hardware,Software,Cleaning,Jaringan,Upgrade,Lainnya',

      'vendor' => 'nullable|string|max:255',
      'biaya' => 'nullable|numeric|min:0',

      'keluhan' => 'nullable|string',
      'diagnosa' => 'nullable|string',
      'tindakan' => 'nullable|string',
      'catatan' => 'nullable|string',

      'asal' => 'required|in:Manual,Mapping,Peminjaman',

      'maping_id' => 'nullable|exists:mapings,id',

      'peminjaman_id' => 'nullable|exists:peminjamans,id',
      'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
    ]);

    $user = auth()->user();
    if ($user->role != 'super_admin') {
      $inventaris = Inventaris::find($request->inventaris_id);
      $accessibleIds = $user->getAccessibleCompanyIds();
      $isAllowed = false;

      if ($inventaris) {
        if ($accessibleIds) {
          $isAllowed = $accessibleIds->contains($inventaris->perusahaan_id);
        } else {
          $isAllowed = ($inventaris->perusahaan_id == $user->id_perusahaan);
        }
      }

      if (!$inventaris || !$isAllowed) {
        return back()
          ->withInput()
          ->with('error', 'Anda tidak memiliki akses ke inventaris dari perusahaan ini.');
      }
    }

    $gambar = null;
    if ($request->hasFile('gambar')) {
      $gambar = $request->file('gambar')->store('maintenance', 'public');
    }

    $serviceAktif = Maintenance::where('inventaris_id', $request->inventaris_id)
      ->whereIn('status', ['Pengajuan', 'Diproses'])
      ->exists();

    if ($serviceAktif) {
      return back()
        ->withInput()
        ->with('error', 'Inventaris tersebut masih memiliki Service yang belum selesai.');
    }
    /*
|--------------------------------------------------------------------------
| CEK PEMINJAMAN TERAKHIR
|--------------------------------------------------------------------------
*/

    $peminjaman = Peminjaman::where('inventaris_id', $request->inventaris_id)
      ->where('status', 'Dikembalikan')
      ->where('kondisi_kembali', 'Rusak')
      ->latest()
      ->first();
    /*
|--------------------------------------------------------------------------
| GENERATE KODE SERVICE
|--------------------------------------------------------------------------
*/

    $tanggal = \Carbon\Carbon::parse($request->tanggal)->format('Ymd');

    $prefix = 'SRV-' . $tanggal . '-';

    $last = Maintenance::where('kode_service', 'like', $prefix . '%')
      ->orderByDesc('kode_service')
      ->first();

    $nomor = 1;

    if ($last) {
      $nomor = ((int) substr($last->kode_service, -5)) + 1;
    }

    $kodeService = $prefix . str_pad($nomor, 5, '0', STR_PAD_LEFT);
    Maintenance::create([
      'kode_service' => $kodeService,
      'inventaris_id' => $request->inventaris_id,

      'asal' => $request->asal == 'Manual' && $peminjaman ? 'Peminjaman' : $request->asal,

      'maping_id' => $request->maping_id,

      'peminjaman_id' => $request->peminjaman_id ?? optional($peminjaman)->id,

      'tanggal' => $request->tanggal,
      'jenis' => $request->jenis,
      'kategori' => $request->kategori,
      'status' => 'Pengajuan',

      'keluhan' => $request->keluhan,
      'diagnosa' => $request->diagnosa,
      'tindakan' => $request->tindakan,

      'vendor' => $request->vendor,

      'biaya' => $request->biaya ?? 0,

      'tanggal_selesai' => null,

      'catatan' => $request->catatan,

      'gambar' => $gambar,

      'created_by' => Auth::id(),
    ]);
    /*
|--------------------------------------------------------------------------
| UPDATE STATUS MAPPING
|--------------------------------------------------------------------------
*/

    $statusMaping = $request->jenis == 'Service' ? 'servis' : 'maintenance';

    $maping = Maping::whereHas('keluar', function ($q) use ($request) {
      $q->where('inventaris_id', $request->inventaris_id);
    })
      ->whereIn('status', ['aktif', 'dipinjam'])
      ->latest()
      ->first();

    if ($maping) {
      $maping->update([
        'status' => $statusMaping,
      ]);
    }

    $targetMaping = $maping ?? ($request->filled('maping_id') ? Maping::find($request->maping_id) : null);
    if ($targetMaping) {
      $targetMaping->loadMissing('keluar.inventaris');
      $inventarisId = $targetMaping->keluar?->inventaris?->id ?? $maintenance->inventaris_id;
      if ($inventarisId) {
        return redirect()
          ->route('history.perjalanan.show', $inventarisId)
          ->with('success', 'Data service berhasil ditambahkan.');
      }
    }

    return redirect()
      ->route('maintenance.index')
      ->with('success', 'Data service berhasil ditambahkan.');
  }
  public function proses(Maintenance $maintenance)
  {
    if ($maintenance->status != 'Pengajuan') {
      return back()->with('error', 'Status tidak dapat diproses.');
    }

    $maintenance->update([
      'status' => 'Diproses',
    ]);

    return back()->with('success', 'Status berhasil diubah menjadi Diproses.');
  }
  public function dibatalkan(Maintenance $maintenance)
  {
    if ($maintenance->status != 'Pengajuan') {
      return back()->with('error', 'Service tidak dapat dibatalkan.');
    }

    $maintenance->update([
      'status' => 'Dibatalkan',
    ]);

    /*
    |--------------------------------------------------------------------------
    | KEMBALIKAN STATUS MAPPING
    |--------------------------------------------------------------------------
    */

    $maping = Maping::whereHas('keluar', function ($q) use ($maintenance) {
      $q->where('inventaris_id', $maintenance->inventaris_id);
    })
      ->whereIn('status', ['servis', 'maintenance'])
      ->latest()
      ->first();

    if ($maping) {
      $maping->update([
        'status' => 'aktif',
      ]);
    }

    return back()->with('success', 'Pengajuan Service berhasil dibatalkan.');
  }
  public function selesai(Maintenance $maintenance)
  {
    if ($maintenance->status != 'Diproses') {
      return back()->with('error', 'Status tidak valid.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS SERVICE
    |--------------------------------------------------------------------------
    */

    $maintenance->update([
      'status' => 'Selesai',
      'tanggal_selesai' => now(),
    ]);

    /*
    |--------------------------------------------------------------------------
    | KEMBALIKAN STATUS MAPPING
    |--------------------------------------------------------------------------
    */

    $maping = Maping::whereHas('keluar', function ($q) use ($maintenance) {
      $q->where('inventaris_id', $maintenance->inventaris_id);
    })
      ->whereIn('status', ['servis', 'maintenance'])
      ->latest()
      ->first();

    if ($maping) {
      $maping->update([
        'status' => 'aktif',
      ]);
    }

    /*
    |--------------------------------------------------------------------------
    | JIKA BERASAL DARI PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    if ($maintenance->asal == 'Peminjaman') {
      $maintenance->inventaris->update([
        'status' => 'TERSEDIA',
      ]);
    }

    return back()->with('success', 'Service berhasil diselesaikan.');
  }
  public function tidakDapatDiperbaiki(Maintenance $maintenance)
  {
    if ($maintenance->status != 'Diproses') {
      return back()->with('error', 'Status tidak valid.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE SERVICE
    |--------------------------------------------------------------------------
    */

    $maintenance->update([
      'status' => 'Tidak Dapat Diperbaiki',
      'tanggal_selesai' => now(),
    ]);

    /*
    |--------------------------------------------------------------------------
    | INVENTARIS MENJADI RUSAK
    |--------------------------------------------------------------------------
    */

    $maintenance->inventaris->update([
      'status' => 'AFKIR',
    ]);

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS MAPPING
    |--------------------------------------------------------------------------
    */

    $maping = Maping::whereHas('keluar', function ($q) use ($maintenance) {
      $q->where('inventaris_id', $maintenance->inventaris_id);
    })
      ->whereIn('status', ['servis', 'maintenance'])
      ->latest()
      ->first();

    if ($maping) {
      $maping->update([
        'status' => 'selesai',
      ]);
    }

    return back()->with('success', 'Status berhasil diperbarui.');
  }

  /**
   * Display the specified resource.
   */
  public function show(Maintenance $maintenance)
  {
    $maintenance->load([
      'inventaris.dataAset.kategori',
      'inventaris.perusahaan',

      'creator',

      'maping.karyawan',
      'maping.lokasi',

      'peminjaman.karyawan',
      'peminjaman.perusahaanTujuan',
      'peminjaman.karyawanTujuan',
    ]);

    return view('content.dashboard.maintenance.show', [
      'maintenance' => $maintenance,
      'backRoute' => route('maintenance.index'),
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Maintenance $maintenance)
  {
    if ($maintenance->status != 'Pengajuan') {
      return redirect()
        ->route('maintenance.index')
        ->with('error', 'Data Service tidak dapat diedit.');
    }

    $maintenance->load(['inventaris.dataAset.kategori', 'inventaris.perusahaan']);

    return view('content.dashboard.maintenance.edit', [
      'maintenance' => $maintenance,
      'inventaris' => $maintenance->inventaris,
      'inventarisList' => collect(),

      // agar hidden input asal tetap bekerja
      'maping' => $maintenance->maping,
      'peminjaman' => $maintenance->peminjaman,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Maintenance $maintenance)
  {
    // Hanya boleh edit saat masih Pengajuan
    if ($maintenance->status != 'Pengajuan') {
      return redirect()
        ->route('maintenance.index')
        ->with('error', 'Data Service tidak dapat diedit.');
    }

    $request->validate([
      'tanggal' => 'required|date',

      'jenis' => 'required|in:Service,Maintenance',

      'kategori' => 'nullable|in:Hardware,Software,Cleaning,Jaringan,Upgrade,Lainnya',

      'vendor' => 'nullable|string|max:255',

      'biaya' => 'nullable|numeric|min:0',

      'keluhan' => 'nullable|string',

      'diagnosa' => 'nullable|string',

      'tindakan' => 'nullable|string',

      'catatan' => 'nullable|string',

      'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
    ]);

    if ($request->hasFile('gambar')) {
      if ($maintenance->gambar && Storage::disk('public')->exists($maintenance->gambar)) {
        Storage::disk('public')->delete($maintenance->gambar);
      }
      $gambar = $request->file('gambar')->store('maintenance', 'public');
    } else {
      $gambar = $maintenance->gambar;
    }

    $maintenance->update([
      'tanggal' => $request->tanggal,

      'jenis' => $request->jenis,

      'kategori' => $request->kategori,

      'vendor' => $request->vendor,

      'biaya' => $request->biaya ?? 0,

      'keluhan' => $request->keluhan,

      'diagnosa' => $request->diagnosa,

      'tindakan' => $request->tindakan,

      'catatan' => $request->catatan,

      'gambar' => $gambar,
    ]);
    $maping = Maping::whereHas('keluar', function ($q) use ($maintenance) {
      $q->where('inventaris_id', $maintenance->inventaris_id);
    })
      ->whereIn('status', ['servis', 'maintenance'])
      ->latest()
      ->first();

    if ($maping) {
      $maping->update([
        'status' => $request->jenis == 'Service' ? 'servis' : 'maintenance',
      ]);
    }

    return redirect()
      ->route('maintenance.index')
      ->with('success', 'Data Service & Maintenance berhasil diperbarui.');
  }

  /**
   * Update unggah foto bukti servis dari halaman detail (berlaku sampai status Tidak Dapat Diperbaiki).
   */
  public function updateGambar(Request $request, Maintenance $maintenance)
  {
    if ($maintenance->status == 'Dibatalkan') {
      return back()->with('error', 'Foto tidak dapat diubah untuk transaksi yang telah dibatalkan.');
    }

    $user = auth()->user();
    if ($user->role != 'super_admin') {
      $accessibleIds = $user->getAccessibleCompanyIds();
      $isAllowed = $accessibleIds
        ? $accessibleIds->contains($maintenance->inventaris->perusahaan_id)
        : ($maintenance->inventaris->perusahaan_id == $user->id_perusahaan);
      if (!$isAllowed) {
        return back()->with('error', 'Anda tidak memiliki akses ke data service ini.');
      }
    }

    $request->validate([
      'gambar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
    ]);

    if ($request->hasFile('gambar')) {
      if ($maintenance->gambar && Storage::disk('public')->exists($maintenance->gambar)) {
        Storage::disk('public')->delete($maintenance->gambar);
      }
      $gambar = $request->file('gambar')->store('maintenance', 'public');

      $maintenance->update([
        'gambar' => $gambar,
      ]);
    }

    return back()->with('success', 'Foto bukti servis berhasil diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Maintenance $maintenance)
  {
    // Ambil inventaris sebelum data dihapus
    $inventaris = $maintenance->inventaris;

    if ($inventaris) {
      $keluar = Keluar::where('inventaris_id', $inventaris->id)
        ->latest()
        ->first();

      if ($keluar && $keluar->maping) {
        if (in_array($keluar->maping->status, ['servis', 'maintenance'])) {
          $keluar->maping->update([
            'status' => 'aktif',
          ]);
        }
      }
    }

    if ($maintenance->gambar && Storage::disk('public')->exists($maintenance->gambar)) {
      Storage::disk('public')->delete($maintenance->gambar);
    }

    $maintenance->delete();

    return redirect()
      ->route('maintenance.index')
      ->with('success', 'Data Service & Maintenance berhasil dihapus.');
  }
  public function cetak(Request $request)
  {
    $query = Maintenance::with(['inventaris.dataAset.kategori', 'inventaris.perusahaan', 'creator']);

    /*
    |--------------------------------------------------------------------------
    | FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $query->whereHas('inventaris', function ($q) use ($request) {
          $q->where('perusahaan_id', $request->perusahaan_id);
        });
      }
    } else {
      $accessibleIds = auth()->user()->getAccessibleCompanyIds();
      $query->whereHas('inventaris', function ($q) use ($accessibleIds) {
        $q->whereIn('perusahaan_id', $accessibleIds);
      });
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->where('kode_service', 'like', "%{$search}%")->orWhereHas('inventaris', function ($qq) use ($search) {
          $qq->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
        });
      });
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | JENIS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('jenis')) {
      $query->where('jenis', $request->jenis);
    }

    /*
    |--------------------------------------------------------------------------
    | TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
    }

    $maintenances = $query->orderBy('tanggal', 'desc')->get();
    $laporan = $maintenances;

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $namaPerusahaan = Perusahaan::find($request->perusahaan_id)?->nama_perusahaan ?? 'Semua Perusahaan';
      } else {
        $namaPerusahaan = 'SEMBILAN GROUP';
      }
    } else {
      $namaPerusahaan = auth()->user()->perusahaan->nama_perusahaan;
    }

    return view('content.dashboard.maintenance.cetak', compact('laporan', 'namaPerusahaan'));
  }

  public function exportExcel(Request $request)
  {
    $query = Maintenance::with(['inventaris.dataAset.kategori', 'inventaris.perusahaan', 'creator']);

    if (auth()->user()->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $query->whereHas('inventaris', function ($q) use ($request) {
          $q->where('perusahaan_id', $request->perusahaan_id);
        });
      }
    } else {
      $accessibleIds = auth()->user()->getAccessibleCompanyIds();
      $query->whereHas('inventaris', function ($q) use ($accessibleIds) {
        $q->whereIn('perusahaan_id', $accessibleIds);
      });
    }

    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('kode_service', 'like', "%{$search}%")->orWhereHas('inventaris', function ($qq) use ($search) {
          $qq->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
        });
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('jenis')) {
      $query->where('jenis', $request->jenis);
    }

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
    }

    $maintenances = $query->orderBy('tanggal', 'desc')->get();

    return \Maatwebsite\Excel\Facades\Excel::download(
      new \App\Exports\MaintenanceExport($maintenances, 'Laporan Servis & Maintenance'),
      'Laporan_Servis_Maintenance.xlsx'
    );
  }
}

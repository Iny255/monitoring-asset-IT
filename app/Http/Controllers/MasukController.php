<?php

namespace App\Http\Controllers;

use App\Models\Masuk;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use App\Models\Inventaris;
use App\Models\DataAset;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Exports\MasukExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
class MasukController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Masuk::with(['dataAset.kategori', 'supplier', 'perusahaan', 'perusahaanAsal', 'inventaris']);
    // FILTER PERUSAHAAN
    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->perusahaan_id) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    // FILTER TANGGAL
    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_pembelian', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_pembelian', '<=', $request->tanggal_akhir);
    }

    // FILTER SUPPLIER
    if ($request->supplier_id) {
      $query->where('supplier_id', $request->supplier_id);
    }
    if ($request->filled('jenis_masuk')) {
      $query->where('jenis_masuk', $request->jenis_masuk);
    }

    // SEARCH
    if ($request->search) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->whereHas('dataAset.kategori', function ($sub) use ($search) {
          $sub->where('nama_barang', 'like', "%{$search}%");
        })->orWhereHas('dataAset', function ($sub) use ($search) {
          $sub->where('merek', 'like', "%{$search}%")->orWhere('type', 'like', "%{$search}%");
        })->orWhereHas('inventaris', function ($sub) use ($search) {
          $sub->where('kode_aset', 'like', "%{$search}%")->orWhere('no_inventaris', 'like', "%{$search}%");
        });
      });
    }

    $masuks = $query
      ->latest()
      ->paginate(10)
      ->appends($request->query());

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
    $selectedPerusahaanId = $user->role == 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;

    $suppliers = Supplier::query()
      ->when($selectedPerusahaanId, fn($q) => $q->where('perusahaan_id', $selectedPerusahaanId))
      ->orderBy('nama_supplier')
      ->get();

    return view('content.dashboard.transaksi-masuk.index', compact('masuks', 'perusahaans', 'suppliers'));
  }
  public function cetak(Request $request)
  {
    $user = auth()->user();

    $query = Masuk::with(['dataAset.kategori', 'supplier', 'perusahaan', 'perusahaanAsal']);
    /*
    |--------------------------------------------------------------------------
    | FILTER PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->filled('perusahaan_id')) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal_awal')) {
      $query->whereDate('tanggal_pembelian', '>=', $request->tanggal_awal);
    }

    if ($request->filled('tanggal_akhir')) {
      $query->whereDate('tanggal_pembelian', '<=', $request->tanggal_akhir);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER SUPPLIER
    |--------------------------------------------------------------------------
    */

    if ($request->filled('supplier_id')) {
      $query->where('supplier_id', $request->supplier_id);
    }
    if ($request->filled('jenis_masuk')) {
      $query->where('jenis_masuk', $request->jenis_masuk);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
      $search = $request->search;

      $query->where(function ($q) use ($search) {
        $q->whereHas('dataAset.kategori', function ($sub) use ($search) {
          $sub->where('nama_barang', 'like', "%{$search}%");
        })->orWhereHas('dataAset', function ($sub) use ($search) {
          $sub
            ->where('merek', 'like', "%{$search}%")

            ->orWhere('type', 'like', "%{$search}%");
        });
      });
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA
    |--------------------------------------------------------------------------
    */

    $masuks = $query->orderBy('tanggal_pembelian')->get();

    /*
|--------------------------------------------------------------------------
| DATA LAPORAN (SETIAP TRANSAKSI = 1 BARIS)
|--------------------------------------------------------------------------
*/

    $laporan = $masuks
      ->map(function ($item) {
        return (object) [
          'tanggal_pembelian' => $item->tanggal_pembelian,

          'perusahaan' => $item->perusahaan,

          'jenis_masuk' => $item->jenis_masuk,

          'kategori' => $item->dataAset->kategori->nama_barang ?? '-',

          'merek' => $item->dataAset->merek ?? '-',

          'type' => $item->dataAset->type ?? '-',

          'asal' =>
            $item->jenis_masuk == 'Pembelian'
              ? $item->supplier->nama_supplier ?? '-'
              : $item->perusahaanAsal->nama_perusahaan ?? '-',

          'qty' => $item->jumlah,

          'harga_satuan' => $item->harga_satuan,

          'total' => $item->jenis_masuk == 'Pembelian' ? $item->jumlah * $item->harga_satuan : 0,
        ];
      })
      ->values();

    /*
    |--------------------------------------------------------------------------
    | REKAP KATEGORI
    |--------------------------------------------------------------------------
    */

    $rekapKategori = $masuks
      ->groupBy(function ($item) {
        return $item->dataAset->kategori->nama_barang ?? 'LAINNYA';
      })

      ->map(function ($items) {
        return $items->sum('jumlah');
      });

    /*
    |--------------------------------------------------------------------------
    | TOTAL UNIT
    |--------------------------------------------------------------------------
    */

    $totalUnit = $masuks->sum('jumlah');

    /*
    |--------------------------------------------------------------------------
    | GRAND TOTAL
    |--------------------------------------------------------------------------
    */

    $grandTotal = $masuks->where('jenis_masuk', 'Pembelian')->sum(function ($item) {
      return $item->jumlah * $item->harga_satuan;
    });

    /*
    |--------------------------------------------------------------------------
    | NAMA PERUSAHAAN
    |--------------------------------------------------------------------------
    */

    $namaPerusahaan = 'SEMBILAN GROUP';

    if ($user->role == 'super_admin') {
      if ($request->filled('perusahaan_id')) {
        $perusahaan = Perusahaan::find($request->perusahaan_id);

        $namaPerusahaan = $perusahaan?->nama_perusahaan ?? 'SEMBILAN GROUP';
      }
    } else {
      $namaPerusahaan = Perusahaan::find($user->id_perusahaan)?->nama_perusahaan;
    }

    return view(
      'content.dashboard.transaksi-masuk.cetak',
      compact('laporan', 'rekapKategori', 'totalUnit', 'grandTotal', 'namaPerusahaan')
    );
  }
  public function exportExcel(Request $request)
{
    return Excel::download(
        new MasukExport($request),
        'Penerimaan_Aset_' . now()->format('Ymd_His') . '.xlsx'
    );
}

  public function create()
  {
    $user = auth()->user();

    $perusahaans = $user->role == 'super_admin' ? Perusahaan::all() : collect();

    if ($user->role == 'super_admin') {
      $suppliers = collect();
      $kategoris = collect();
      $dataAsets = collect();
    } else {
      $suppliers = Supplier::where('perusahaan_id', $user->id_perusahaan)->get();

      $kategoris = Kategori::where('perusahaan_id', $user->id_perusahaan)
        ->orderBy('nama_barang')
        ->get();

      $dataAsets = DataAset::with('kategori')
        ->where('perusahaan_id', $user->id_perusahaan)
        ->get();
    }
    return view('content.dashboard.transaksi-masuk.create', compact('perusahaans', 'suppliers', 'kategoris', 'dataAsets'));
  }

  public function store(Request $request)
  {
    $user = auth()->user();

    $request->validate([
      'kategori_id' => 'required|exists:kategoris,id',
      'data_aset_id' => 'required|exists:data_asets,id',

      'jenis_masuk' => 'required|in:Pembelian,Mutasi',

      'supplier_id' => [Rule::requiredIf($request->jenis_masuk == 'Pembelian'), 'nullable', 'exists:suppliers,id'],

      'tanggal_pembelian' => 'required|date',
      'jumlah' => 'required|integer|min:1',
      'harga_satuan' => 'required|numeric|min:0',
      'garansi' => 'nullable|integer|min:0',
      'ket_penerimaan' => 'required|in:BAIK,RUSAK',
    ]);

    DB::beginTransaction();

    try {
      $perusahaanId = $user->role == 'super_admin' ? $request->perusahaan_id : $user->id_perusahaan;

      $masuk = Masuk::create([
        'perusahaan_id' => $perusahaanId,

        'supplier_id' => $request->supplier_id,

        'perusahaan_asal' => $request->perusahaan_asal,

        'history_mutasi_id' => $request->history_mutasi_id,

        'jenis_masuk' => $request->jenis_masuk,

        'data_aset_id' => $request->data_aset_id,

        'tanggal_pembelian' => $request->tanggal_pembelian,

        'jumlah' => $request->jumlah,

        'harga_satuan' => $request->harga_satuan,

        'garansi' => $request->garansi,

        'ket_penerimaan' => $request->ket_penerimaan,
      ]);

      $dataAset = DataAset::with('kategori')->findOrFail($request->data_aset_id);
      $perusahaan = Perusahaan::findOrFail($perusahaanId);

      $prefixKode = strtoupper($dataAset->kategori->kode_barang) . '.' . strtoupper($perusahaan->kode_perusahaan) . '-';

      // Hitung urutan tertinggi no_inventaris untuk perusahaan ini
      $existingInvs = Inventaris::where('perusahaan_id', $perusahaanId)
        ->where('no_inventaris', 'like', 'INV-%')
        ->pluck('no_inventaris');

      $maxInvUrut = 0;
      foreach ($existingInvs as $inv) {
        $numStr = str_replace('INV-', '', $inv);
        if (is_numeric($numStr)) {
          $val = (int) $numStr;
          if ($val > $maxInvUrut) {
            $maxInvUrut = $val;
          }
        }
      }

      // Hitung urutan tertinggi kode_aset untuk prefix kategori & perusahaan ini
      $existingKodes = Inventaris::where('perusahaan_id', $perusahaanId)
        ->where('kode_aset', 'like', $prefixKode . '%')
        ->pluck('kode_aset');

      $maxKodeUrut = 0;
      foreach ($existingKodes as $k) {
        $numStr = substr($k, strlen($prefixKode));
        if (is_numeric($numStr)) {
          $val = (int) $numStr;
          if ($val > $maxKodeUrut) {
            $maxKodeUrut = $val;
          }
        }
      }

      for ($i = 1; $i <= $request->jumlah; $i++) {
        // NO INVENTARIS
        $maxInvUrut++;
        $noInventaris = 'INV-' . str_pad($maxInvUrut, 3, '0', STR_PAD_LEFT);
        while (Inventaris::where('perusahaan_id', $perusahaanId)->where('no_inventaris', $noInventaris)->exists()) {
          $maxInvUrut++;
          $noInventaris = 'INV-' . str_pad($maxInvUrut, 3, '0', STR_PAD_LEFT);
        }

        // KODE ASET
        $maxKodeUrut++;
        $kodeAset = $prefixKode . str_pad($maxKodeUrut, 3, '0', STR_PAD_LEFT);
        while (Inventaris::where('perusahaan_id', $perusahaanId)->where('kode_aset', $kodeAset)->exists()) {
          $maxKodeUrut++;
          $kodeAset = $prefixKode . str_pad($maxKodeUrut, 3, '0', STR_PAD_LEFT);
        }

        Inventaris::create([
          'masuk_id' => $masuk->id,
          'perusahaan_id' => $perusahaanId,
          'data_aset_id' => $dataAset->id,
          'kode_aset' => $kodeAset,
          'no_inventaris' => $noInventaris,
          'status' => 'TERSEDIA',
        ]);
      }

      DB::commit();

      return redirect()
        ->route('transaksi-masuk.index')
        ->with('success', 'Penerimaan aset berhasil disimpan.');
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('Gagal menyimpan data penerimaan aset: ' . $e->getMessage());

      return back()
        ->withInput()
        ->with('error', 'Gagal menyimpan data penerimaan aset: ' . $e->getMessage());
    }
  }

  public function show(int $id)
  {
    $masuk = Masuk::with([
      'perusahaan',
      'perusahaanAsal',
      'supplier',
      'dataAset.kategori',
      'inventaris',
      'historyMutasi',
    ])->findOrFail($id);

    return view('content.dashboard.transaksi-masuk.show', compact('masuk'));
  }

  public function edit(Masuk $masuk)
  {
    $user = auth()->user();

    $perusahaans = $user->role == 'super_admin' ? Perusahaan::all() : collect();

    $suppliers =
      $user->role == 'super_admin' ? Supplier::all() : Supplier::where('perusahaan_id', $user->id_perusahaan)->get();

    $dataAsets = DataAset::with('kategori')
      ->where('perusahaan_id', $masuk->perusahaan_id)
      ->get();

    $suppliers = Supplier::where('perusahaan_id', $masuk->perusahaan_id)->get();

    return view('content.dashboard.transaksi-masuk.edit', compact('masuk', 'perusahaans', 'dataAsets', 'suppliers'));
  }

  public function update(Request $request, Masuk $masuk)
  {
    $request->validate([
      'supplier_id' => [Rule::requiredIf($masuk->jenis_masuk == 'Pembelian'), 'nullable', 'exists:suppliers,id'],
      'tanggal_pembelian' => 'required|date',
      'harga_satuan' => 'required|numeric|min:0',
      'garansi' => 'nullable|integer|min:0',
      'ket_penerimaan' => 'required|in:BAIK,RUSAK',
    ]);

    try {
      $masuk->update([
        'supplier_id' => $request->supplier_id,
        'tanggal_pembelian' => $request->tanggal_pembelian,
        'harga_satuan' => $request->harga_satuan,
        'garansi' => $request->garansi,
        'ket_penerimaan' => $request->ket_penerimaan,
      ]);

      return redirect()
        ->route('transaksi-masuk.index')
        ->with('success', 'Data penerimaan aset berhasil diperbarui.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()
        ->withInput()
        ->with('error', 'Gagal memperbarui data.');
    }
  }

  public function destroy(Masuk $masuk)
  {
    try {
      $masuk->delete();

      return redirect()
        ->route('transaksi-masuk.index')
        ->with('success', 'Data berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());

      return back()->with('error', 'Gagal menghapus data.');
    }
  }

  public function download(Masuk $masuk)
  {
    if ($masuk->gambar && Storage::exists('public/' . $masuk->gambar)) {
      return Storage::download('public/' . $masuk->gambar);
    }

    abort(404, 'File tidak ditemukan');
  }

  public function stok(Request $request)
  {
    $user = auth()->user();

    $query = Inventaris::with(['dataAset.kategori', 'perusahaan', 'keluarTerakhir.karyawan']);

    if ($user->role != 'super_admin') {
      $query->where('perusahaan_id', $user->id_perusahaan);
    } elseif ($request->perusahaan_id) {
      $query->where('perusahaan_id', $request->perusahaan_id);
    }

    $inventaris = $query->get();

    /*
    |--------------------------------------------------------------------------
    | GROUP PER DATA ASET
    |--------------------------------------------------------------------------
    */

    $stoks = $inventaris
      ->groupBy('data_aset_id')
      ->map(function ($items) {
        $first = $items->first();

        return (object) [
          'data_aset_id' => $first->data_aset_id,

          'perusahaan' => $first->perusahaan,

          'kategori' => $first->dataAset->kategori,

          'merek' => $first->dataAset->merek,

          'type' => $first->dataAset->type,

          'total_aset' => $items->count(),

          'tersedia' => $items->where('status', 'TERSEDIA')->count(),

          'dipakai' => $items->where('status', 'DIPAKAI')->count(),

          'dipinjam' => $items->where('status', 'DIPINJAM')->count(),

          'rusak' => $items->where('status', 'RUSAK')->count(),
          'afkir' => $items->where('status', 'AFKIR')->count(),

          'inventaris' => $items,
        ];
      })
      ->values();

    $perusahaans = Perusahaan::all();

    return view('content.dashboard.transaksi-masuk.stok', compact('stoks', 'perusahaans'));
  }
  public function getSupplier(string $id)
  {
    $suppliers = Supplier::where('perusahaan_id', $id)
      ->orderBy('nama_supplier')
      ->get();

    return response()->json($suppliers);
  }

  public function getKategori(string $id)
  {
    $kategoris = Kategori::where('perusahaan_id', $id)
      ->orderBy('nama_barang')
      ->get();

    return response()->json($kategoris);
  }

  public function getDataAset(Request $request, string $id = null)
  {
    $perusahaanId = $id ?? $request->perusahaan_id;
    $kategoriId = $request->kategori_id;

    $query = DataAset::with('kategori');

    if ($perusahaanId) {
      $query->where('perusahaan_id', $perusahaanId);
    }

    if ($kategoriId) {
      $query->where('kategori_id', $kategoriId);
    }

    $dataAsets = $query->get();

    return response()->json($dataAsets);
  }
}

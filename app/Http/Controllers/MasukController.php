<?php

namespace App\Http\Controllers;

use App\Models\Masuk;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use App\Models\Inventaris;
use App\Models\DataAset;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
class MasukController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();

    $query = Masuk::with(['dataAset.kategori', 'supplier', 'perusahaan', 'perusahaanAsal']);
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
        });
      });
    }

    $masuks = $query
      ->latest()
      ->paginate(10)
      ->appends($request->query());

    $perusahaans = Perusahaan::all();
    $suppliers =
      $user->role == 'super_admin'
        ? Supplier::orderBy('nama_supplier')->get()
        : Supplier::where('perusahaan_id', $user->id_perusahaan)
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
    | GABUNGKAN DATA YANG SAMA
    |--------------------------------------------------------------------------
    */

    $laporan = $masuks
      ->groupBy(function ($item) {
        return ($item->perusahaan_id ?? '') .
          '|' .
          ($item->jenis_masuk ?? '') .
          '|' .
          ($item->dataAset->kategori->nama_barang ?? '') .
          '|' .
          ($item->dataAset->merek ?? '') .
          '|' .
          ($item->dataAset->type ?? '') .
          '|' .
          ($item->supplier_id ?? '') .
          '|' .
          ($item->perusahaan_asal ?? '');
      })

      ->map(function ($items) {
        $first = $items->first();

        return (object) [
          'tanggal_pembelian' => $first->tanggal_pembelian,

          'perusahaan' => $first->perusahaan,
          'jenis_masuk' => $first->jenis_masuk,

          'kategori' => $first->dataAset->kategori->nama_barang ?? '-',

          'merek' => $first->dataAset->merek ?? '-',

          'type' => $first->dataAset->type ?? '-',

          'asal' =>
            $first->jenis_masuk == 'Pembelian'
              ? $first->supplier->nama_supplier ?? '-'
              : $first->perusahaanAsal->nama_perusahaan ?? '-',
          'qty' => $items->sum('jumlah'),

          'harga_satuan' => $first->harga_satuan,

          'total' =>
            $first->jenis_masuk == 'Pembelian'
              ? $items->sum(function ($row) {
                return $row->jumlah * $row->harga_satuan;
              })
              : 0,
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

  public function create()
  {
    $user = auth()->user();

    $perusahaans = $user->role == 'super_admin' ? Perusahaan::all() : collect();

    if ($user->role == 'super_admin') {
      $suppliers = collect();

      $dataAsets = collect();
    } else {
      $suppliers = Supplier::where('perusahaan_id', $user->id_perusahaan)->get();

      $dataAsets = DataAset::with('kategori')
        ->where('perusahaan_id', $user->id_perusahaan)
        ->get();
    }
    return view('content.dashboard.transaksi-masuk.create', compact('perusahaans', 'suppliers', 'dataAsets'));
  }

  public function store(Request $request)
  {
    $user = auth()->user();

    $request->validate([
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

      for ($i = 1; $i <= $request->jumlah; $i++) {
        // NO INVENTARIS

        $lastInventaris = Inventaris::where('perusahaan_id', $perusahaanId)
          ->latest('id')
          ->first();

        $urutInv = $lastInventaris ? (int) str_replace('INV-', '', $lastInventaris->no_inventaris) + 1 : 1;

        $noInventaris = 'INV-' . str_pad($urutInv, 3, '0', STR_PAD_LEFT);

        // KODE ASET

        $lastKode = Inventaris::where('perusahaan_id', $perusahaanId)
          ->where('data_aset_id', $dataAset->id)
          ->latest('id')
          ->first();
        $urutKode = $lastKode ? (int) substr($lastKode->kode_aset, -3) + 1 : 1;

        $kodeAset =
          strtoupper($dataAset->kategori->kode_barang) .
          '.' .
          $perusahaan->kode_perusahaan .
          '-' .
          str_pad($urutKode, 3, '0', STR_PAD_LEFT);

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

      dd($e->getMessage());

      return back()
        ->withInput()
        ->with('error', 'Gagal menyimpan data penerimaan aset.');
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
  public function getDataAset(string $id)
  {
    $dataAsets = DataAset::with('kategori')
      ->where('perusahaan_id', $id)
      ->get();

    return response()->json($dataAsets);
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Masuk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Masuk::class);
        $query = Masuk::with('kategori');

        if ($request->search) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%');
            });
        }

        $masuks = $query->orderBy('id', 'desc')->paginate(5);

        return view('content.dashboard.transaksi-masuk.index', compact('masuks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Masuk::class);
        $kategoris = Kategori::all();

        $last = Masuk::latest()->first();
        $kodeMasuk = 'MSK-' . str_pad(($last?->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        return view('content.dashboard.transaksi-masuk.create', compact('kategoris', 'kodeMasuk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Masuk::class);
        $request->validate([
            'id_kategori' => 'required',
            'type'        => 'required',
            'merek'       => 'required',
            'jumlah'      => 'required|integer',
            'tgl_beli'    => 'required|date',
            'supplier'    => 'required',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'garansi'     => 'required|integer',
            'harga'       => 'required',
        ]);

        $last = Masuk::latest()->first();
        $kodeMasuk = 'MSK-' . str_pad(($last?->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        $gambarPath = null;

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar-masuk', 'public');
        }

        Masuk::create([
            'kode_masuk'   => $kodeMasuk,
            'id_kategori' => $request->id_kategori,
            'type'        => $request->type,
            'merek'       => $request->merek,
            'jumlah'      => $request->jumlah,
            'tgl_beli'    => $request->tgl_beli,
            'supplier'    => $request->supplier,
            'gambar'      => $gambarPath,
            'garansi'     => $request->garansi,
            'harga'       => $request->harga,
        ]);

        return redirect()->route('transaksi-masuk.index')->with('success', 'Data barang masuk berhasil disimpan');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $masuk = Masuk::with('kategori')->findOrFail($id);
        //  $this->authorize('view', $masuk);
        return view('content.dashboard.transaksi-masuk.show', compact('masuk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $masuk = Masuk::findOrFail($id);
        $this->authorize('update', $masuk);
        $kategoris = Kategori::all();

        return view('content.dashboard.transaksi-masuk.edit', compact('masuk', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $masuk = Masuk::findOrFail($id);
        $this->authorize('update', $masuk);
        $request->validate([
            'kode_masuk' => 'required',
            'id_kategori' => 'required',
            'type'        => 'required',
            'merek'       => 'required',
            'jumlah'      => 'required|integer',
            'tgl_beli'    => 'required|date',
            'supplier'    => 'required',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'garansi'      => 'required|integer',
            'harga'      => 'required',
        ]);

        $data = $request->only([
            'kode_masuk',
            'id_kategori',
            'type',
            'merek',
            'jumlah',
            'tgl_beli',
            'supplier',
            'garansi',
            'harga'
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('gambar-masuk', 'public');
        }

        $masuk->update($data);

        return redirect()->route('transaksi-masuk.index')->with('success', 'Data berhasil diupdate');
    }

    public function stok(Request $request)
    {
        $stoks = Masuk::with('kategori')
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('kategori', function ($q) use ($request) {
                    $q->where('nama_barang', 'like', '%' . $request->search . '%');
                });
            })
            ->select(
                'id_kategori',
                'type',
                'merek',
                DB::raw('SUM(jumlah) as stok')
            )
            ->groupBy('id_kategori', 'type', 'merek')
            ->orderBy('stok', 'desc')
            ->get();

        return view('content.dashboard.transaksi-masuk.stok', compact('stoks'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $masuk = Masuk::findOrFail($id);
        $this->authorize('delete', $masuk);

        $masuk->delete();

        return redirect()->back()
            ->with('success', 'Transaksi Masuk  berhasil dihapus');
    }
    public function downloadGambar($id)
    {
        $masuk = Masuk::findOrFail($id);

        if (!$masuk->gambar) {
            return redirect()->back()->with('error', 'Gambar tidak tersedia');
        }

        $path = $masuk->gambar;

        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        return response()->download(
            storage_path('app/public/' . $path)
        );
    }
}

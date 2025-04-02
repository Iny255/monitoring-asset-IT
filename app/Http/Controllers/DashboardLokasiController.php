<?php

namespace App\Http\Controllers;


use App\Models\Main;
use App\Models\Post;
use App\Models\User;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardLokasiController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {

    $search = $request->input('search');

    $lokasis = Lokasi::latest();

    if ($search) {
      $lokasis = $lokasis->where(function ($query) use ($search) {
        $query->where('nama_posyandu', 'like', '%' . $search . '%')
          ->orWhere('dukuh', 'like', '%' . $search . '%');
      });
    }

    // Pagination
    $lokasis = $lokasis->paginate(7);


    return view('content.dashboard.lokasi-posyandu.index', compact('lokasis'));
  }

  /**
   * Show the form for creating a new resource.
   */

  public function hapusjadwal($id)
  {
    $result = Main::Hapus('lokasis', ['id' => $id]);
    if ($result == 1) {
      return redirect('/dashboard/lokasi-posyandu')->with('success', 'Data lokasi posyandu berhasil dihapus.');
    } else {
      return redirect('/dashboard/lokasi-posyandu')->with('error', 'Gagal menghapus Data lokasi posyandu.');
    }
  }
  public function create()
  {

    return view('content.dashboard.lokasi-posyandu.create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {

    $validatedData = $request->validate([
      'nama_posyandu' => 'required|string|max:50|unique:lokasis,nama_posyandu',
      'dukuh' => 'required|string|max:20',
      'rt' => 'required|string|max:3',
      'rw' => 'required|string|max:3',
    ]);

    $validatedData['username'] = auth()->user()->username;

    try {
      $lokasi = Lokasi::create($validatedData);

      if ($lokasi) {
        return redirect('/dashboard/lokasi-posyandu')->with('success', 'Data lokasi posyandu berhasil disimpan.');
      } else {
        return redirect('/dashboard/lokasi-posyandu')->with('error', 'Gagal menyimpan Data lokasi posyandu.');
      }
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return redirect('/dashboard/posyandu/lokasi-create')->with('error', 'Data lokasi posyandu tidak berhasil disimpan. Kesalahan: ' . $e->getMessage());
    }
  //   $validatedData = $request->validate([
  //       'nama_posyandu' => 'required|string|max:50',
  //       'dukuh' => 'required|string|max:20',
  //       'rt' => 'required|string|max:3',
  //       'rw' => 'required|string|max:3',
  //   ]);

  //   // Cek apakah kombinasi nama_posyandu, dukuh, rt, rw sudah ada
  //   $existingLokasi = Lokasi::where('nama_posyandu', $request->nama_posyandu)
  //                           ->where('dukuh', $request->dukuh)
  //                           ->where('rt', $request->rt)
  //                           ->where('rw', $request->rw)
  //                           ->exists();

  //   if ($existingLokasi) {
  //       return redirect()->back()->with('error', 'Data lokasi posyandu dengan kombinasi ini sudah ada.');
  //   }

  //   // Simpan data lokasi posyandu
  //   Lokasi::create($validatedData);

  //   return redirect('/dashboard/lokasi-posyandu')->with('success', 'Data lokasi posyandu berhasil disimpan.');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $lokasi = Lokasi::find($id);

    if (!$lokasi) {
      return redirect('/dashboard/lokasi-posyandu')->with('error', 'Data lokasi posyandu tidak ditemukan.');
    }

    return view('content.dashboard.lokasi-posyandu.detail', compact('lokasi'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit($id)
  {
    $lokasis = Lokasi::findOrFail($id);
    return view('content.dashboard.lokasi-posyandu.edit', compact(['lokasis']));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id)
  {
    $lokasi = Lokasi::findOrFail($id);
     // Validasi form input
    $validatedData = $request->validate([
        'nama_posyandu' => 'required|string|max:50|unique:lokasis,nama_posyandu,' . $lokasi->id,
        'dukuh' => 'required|string|max:20',
        'rt' => 'required|string|max:3',
        'rw' => 'required|string|max:3',
    ]);
    $lokasi->nama_posyandu = $request->nama_posyandu;
    $lokasi->dukuh = $request->dukuh;
    $lokasi->rt = $request->rt;
    $lokasi->rw = $request->rw;
    $lokasi->save();

    if ($lokasi) {
      return redirect('/dashboard/lokasi-posyandu')->with('success', 'Data lokasi posyandu berhasil disimpan.');
    } else {
      return redirect('/dashboard/lokasi-posyandu')->with('error', 'Data lokasi posyandu tidak berhasil disimpan.');
    }
  }

  public function destroy(string $id)
  {
    //
  }
}
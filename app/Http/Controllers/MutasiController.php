<?php

namespace App\Http\Controllers;

use App\Models\Maping;
use App\Models\Lokasi;
use App\Models\Perusahaan;
use App\Models\Karyawan;
use App\Models\Inventaris;
use App\Models\Keluar;
use App\Models\Kategori;
use App\Models\DAtaAset;
use App\Models\HistoryMutasi;
use App\Models\MapingAccess;
use App\Models\Access;
use App\Models\Masuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MutasiController extends Controller
{
  public function mutasiForm(Maping $maping)
  {
    $user = auth()->user();

    if ($user->role != 'super_admin' && $maping->id_perusahaan != $user->id_perusahaan) {
      abort(403);
    }
    if ($maping->status != 'aktif') {
    return redirect()
        ->route('maping.index')
        ->with('error', 'Hak Akses hanya dapat dikelola pada mapping yang berstatus Aktif.');
}
    $maping->load([
      'keluar.inventaris.dataAset.kategori',
      'keluar.karyawan',
      'lokasi',
      'perusahaan',
      'mapingAccesses.access',
    ]);

    $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

    $lokasis = Lokasi::where('id_perusahaan', $maping->id_perusahaan)
      ->orderBy('nama_lokasi')
      ->get();

    return view('content.dashboard.maping.mutasi', compact('maping', 'perusahaans', 'lokasis'));
  }
  public function mutasiStore(Request $request, Maping $maping)
  {
    $request->validate([
      'jenis_mutasi' => 'required|in:internal,antar_perusahaan',

      'tanggal_mutasi' => 'required|date',

      'id_lokasi' => 'required|exists:lokasis,id',

      'jenis_penerima' => 'required|in:Perorangan,Perdivisi',

      'id_karyawan' => [Rule::requiredIf($request->jenis_penerima == 'Perorangan'), 'nullable', 'exists:karyawans,id'],

      'divisi' => [Rule::requiredIf($request->jenis_penerima == 'Perdivisi'), 'nullable', 'string', 'max:100'],

      'id_perusahaan_tujuan' => [
        Rule::requiredIf($request->jenis_mutasi == 'antar_perusahaan'),
        'nullable',
        'exists:perusahaans,id',
      ],

      'opsi_hak_akses' => 'required|in:copy,manual',

      'catatan' => 'nullable|string|max:500',
    ]);

    DB::beginTransaction();

    try {
      /*
        |--------------------------------------------------------------------------
        | LOAD RELASI
        |--------------------------------------------------------------------------
        */

      $maping->load([
        'keluar.inventaris.dataAset.kategori',
        'keluar.karyawan',
        'lokasi',
        'perusahaan',
        'mapingAccesses.access',
      ]);

      /*
        |--------------------------------------------------------------------------
        | DATA LAMA
        |--------------------------------------------------------------------------
        */

      $inventaris = $maping->keluar->inventaris;

      $keluarLama = $maping->keluar;
      $lokasiLama = $maping->lokasi;

      /*
|--------------------------------------------------------------------------
| PENERIMA BARU
|--------------------------------------------------------------------------
*/

      $karyawanBaru = null;
      $divisiBaru = null;

      if ($request->jenis_penerima == 'Perorangan') {
        $karyawanBaru = Karyawan::findOrFail($request->id_karyawan);
      } else {
        $divisiBaru = $request->divisi;
      }

      $lokasiBaru = Lokasi::withoutGlobalScope('perusahaan')->findOrFail($request->id_lokasi);

      /*
        |--------------------------------------------------------------------------
        | TENTUKAN PERUSAHAAN TUJUAN
        |--------------------------------------------------------------------------
        */

      if ($request->jenis_mutasi == 'internal') {
        $perusahaanTujuan = $maping->perusahaan;
      } else {
        $perusahaanTujuan = Perusahaan::findOrFail($request->id_perusahaan_tujuan);
      }

      /*
|--------------------------------------------------------------------------
| MUTASI INTERNAL
|--------------------------------------------------------------------------
*/

      if ($request->jenis_mutasi == 'internal') {
        /*
|--------------------------------------------------------------------------
| SIMPAN KONDISI LAMA
|--------------------------------------------------------------------------
*/

        $jenisPenerimaLama = $maping->jenis_penerima;
        $karyawanLama = $maping->karyawan;
        $divisiLama = $maping->divisi;
        $lokasiLama = $maping->lokasi;
        /*
    |--------------------------------------------------------------
    | Update Mapping (Current State)
    |--------------------------------------------------------------
    */

        $maping->update([
          'id_lokasi' => $lokasiBaru->id,

          'karyawan_id' => $request->jenis_penerima == 'Perorangan' ? $karyawanBaru?->id : null,

          'jenis_penerima' => $request->jenis_penerima,

          'divisi' => $request->jenis_penerima == 'Perdivisi' ? $divisiBaru : null,
        ]);
        /*
|--------------------------------------------------------------------------
| PENERIMA LAMA
|--------------------------------------------------------------------------
*/
        if ($jenisPenerimaLama == 'Perorangan') {
          $userLama = optional($karyawanLama)->nama_karyawan;
        } else {
          $userLama = $divisiLama;
        }

        /*
|--------------------------------------------------------------------------
| PENERIMA BARU
|--------------------------------------------------------------------------
*/

        if ($request->jenis_penerima == 'Perorangan') {
          $userBaru = optional($karyawanBaru)->nama_karyawan;
        } else {
          $userBaru = $request->divisi;
        }

        /*
            |--------------------------------------------------------------
            | Simpan History
            |--------------------------------------------------------------
            */

        HistoryMutasi::create([
          'inventaris_id' => $inventaris->id,

          'maping_id' => $maping->id,

          'jenis_mutasi' => 'internal',

          'id_perusahaan_asal' => $maping->id_perusahaan,

          'id_perusahaan_tujuan' => $maping->id_perusahaan,

          'kode_aset_lama' => $inventaris->kode_aset,

          'kode_aset_baru' => $inventaris->kode_aset,

          'no_inventaris_lama' => $inventaris->no_inventaris,

          'no_inventaris_baru' => $inventaris->no_inventaris,

          'nama_aset' => optional($inventaris->dataAset->kategori)->nama_barang,

          'lokasi_lama' => optional($lokasiLama)->nama_lokasi,

          'lokasi_baru' => $lokasiBaru->nama_lokasi,
          'user_lama' => $userLama,

          'user_baru' => $userBaru,
          'tanggal_mutasi' => $request->tanggal_mutasi,

          'catatan' => $request->catatan,

          'opsi_hak_akses' => $request->opsi_hak_akses,

          'created_by' => auth()->id(),
        ]);

        DB::commit();

        $maping->loadMissing('keluar.inventaris');
        $dataAsetId = $maping->keluar?->inventaris?->data_aset_id;
        $targetUrl = $dataAsetId ? route('history.perjalanan.show', $dataAsetId) : route('history.perjalanan.index');

        return redirect($targetUrl)
          ->with('success', 'Mutasi internal berhasil disimpan.');
      }

      /*
        |--------------------------------------------------------------------------
        | MUTASI ANTAR PERUSAHAAN
        |--------------------------------------------------------------------------
        */

      /*
        |--------------------------------------------------------------------------
        | KATEGORI TUJUAN
        |--------------------------------------------------------------------------
        */

      $kategoriLama = $inventaris->dataAset->kategori;

      /*
|--------------------------------------------------------------------------
| CARI / BUAT KATEGORI TUJUAN
|--------------------------------------------------------------------------
*/

      $kategoriTujuan = Kategori::where('perusahaan_id', $perusahaanTujuan->id)
        ->where('kode_barang', $kategoriLama->kode_barang)
        ->first();

      if (!$kategoriTujuan) {
        $kategoriTujuan = Kategori::create([
          'perusahaan_id' => $perusahaanTujuan->id,

          'kode_barang' => $kategoriLama->kode_barang,

          'nama_barang' => $kategoriLama->nama_barang,
        ]);
      }

      /*
|--------------------------------------------------------------------------
| CARI / BUAT DATA ASET TUJUAN
|--------------------------------------------------------------------------
*/

      $dataAsetLama = $inventaris->dataAset;

      $dataAsetTujuan = DataAset::where('perusahaan_id', $perusahaanTujuan->id)
        ->where('kategori_id', $kategoriTujuan->id)
        ->where('merek', $dataAsetLama->merek)
        ->where('type', $dataAsetLama->type)
        ->where('warna', $dataAsetLama->warna)
        ->first();

      if (!$dataAsetTujuan) {
        $dataAsetTujuan = DataAset::create([
          'perusahaan_id' => $perusahaanTujuan->id,

          'kategori_id' => $kategoriTujuan->id,

          'merek' => $dataAsetLama->merek,

          'type' => $dataAsetLama->type,

          'warna' => $dataAsetLama->warna,
        ]);
      }
      /*
|--------------------------------------------------------------------------
| BUAT TRANSAKSI MASUK BARU
|--------------------------------------------------------------------------
*/

      $masukBaru = Masuk::create([
        'perusahaan_id' => $perusahaanTujuan->id,
        'supplier_id' => null,
        'perusahaan_asal' => $maping->id_perusahaan,
        'history_mutasi_id' => null, // nanti diupdate setelah HistoryMutasi dibuat
        'data_aset_id' => $dataAsetTujuan->id,
        'jenis_masuk' => 'Mutasi',
        'tanggal_pembelian' => $request->tanggal_mutasi,
        'jumlah' => 1,
        'harga_satuan' => 0,
        'garansi' => null,
        'ket_penerimaan' => 'BAIK',
      ]);

      /*
|--------------------------------------------------------------------------
| GENERATE NOMOR INVENTARIS BARU
|--------------------------------------------------------------------------
*/

      $prefixKodeMutasi = strtoupper($kategoriTujuan->kode_barang) . '.' . strtoupper($perusahaanTujuan->kode_perusahaan) . '-';

      $existingInvsTujuan = Inventaris::withoutGlobalScopes()
        ->where('perusahaan_id', $perusahaanTujuan->id)
        ->where('no_inventaris', 'like', 'INV-%')
        ->pluck('no_inventaris');

      $maxInvUrutMutasi = 0;
      foreach ($existingInvsTujuan as $inv) {
        $numStr = str_replace('INV-', '', $inv);
        if (is_numeric($numStr)) {
          $val = (int) $numStr;
          if ($val > $maxInvUrutMutasi) {
            $maxInvUrutMutasi = $val;
          }
        }
      }
      $maxInvUrutMutasi++;
      $noInventarisBaru = 'INV-' . str_pad($maxInvUrutMutasi, 3, '0', STR_PAD_LEFT);
      while (Inventaris::withoutGlobalScopes()->where('perusahaan_id', $perusahaanTujuan->id)->where('no_inventaris', $noInventarisBaru)->exists()) {
        $maxInvUrutMutasi++;
        $noInventarisBaru = 'INV-' . str_pad($maxInvUrutMutasi, 3, '0', STR_PAD_LEFT);
      }

      $existingKodesTujuan = Inventaris::withoutGlobalScopes()
        ->where('perusahaan_id', $perusahaanTujuan->id)
        ->where('kode_aset', 'like', $prefixKodeMutasi . '%')
        ->pluck('kode_aset');

      $maxKodeUrutMutasi = 0;
      foreach ($existingKodesTujuan as $k) {
        $numStr = substr($k, strlen($prefixKodeMutasi));
        if (is_numeric($numStr)) {
          $val = (int) $numStr;
          if ($val > $maxKodeUrutMutasi) {
            $maxKodeUrutMutasi = $val;
          }
        }
      }
      $maxKodeUrutMutasi++;
      $kodeAsetBaru = $prefixKodeMutasi . str_pad($maxKodeUrutMutasi, 3, '0', STR_PAD_LEFT);
      while (Inventaris::withoutGlobalScopes()->where('perusahaan_id', $perusahaanTujuan->id)->where('kode_aset', $kodeAsetBaru)->exists()) {
        $maxKodeUrutMutasi++;
        $kodeAsetBaru = $prefixKodeMutasi . str_pad($maxKodeUrutMutasi, 3, '0', STR_PAD_LEFT);
      }
      /*
|--------------------------------------------------------------------------
| BUAT INVENTARIS BARU
|--------------------------------------------------------------------------
*/

      $inventarisBaru = Inventaris::create([
        'masuk_id' => $masukBaru->id,

        'perusahaan_id' => $perusahaanTujuan->id,

        'data_aset_id' => $dataAsetTujuan->id,

        'kode_aset' => $kodeAsetBaru,
        

        'no_inventaris' => $noInventarisBaru,

        'status' => 'DIPAKAI',
        'is_transfer' => false,
      ]);
      $inventaris->update([
        'is_transfer' => true,
      ]);

      /*
|--------------------------------------------------------------------------
| BUAT TRANSAKSI KELUAR BARU
|--------------------------------------------------------------------------
*/

      $keluarBaru = Keluar::create([
        'inventaris_id' => $inventarisBaru->id,

        'perusahaan_id' => $perusahaanTujuan->id,

        'jenis_penerima' => $request->jenis_penerima,

        'karyawan_id' => $karyawanBaru?->id,

        'divisi_klr' => $divisiBaru,

        'lokasi_id' => $lokasiBaru->id,

        'tgl_keluar' => $request->tanggal_mutasi,

        'perusahaan_klr' => $keluarLama->perusahaan_klr,

        'gambar' => $keluarLama->gambar,

        'created_by' => auth()->id(),
      ]);

      /*
|--------------------------------------------------------------------------
| BUAT MAPPING BARU
|--------------------------------------------------------------------------
*/

      $mapingBaru = Maping::create([
        'id_keluar' => $keluarBaru->id,

        'id_perusahaan' => $perusahaanTujuan->id,

        'id_lokasi' => $lokasiBaru->id,

        'karyawan_id' => $request->jenis_penerima == 'Perorangan' ? $karyawanBaru?->id : null,

        'jenis_penerima' => $request->jenis_penerima,

        'divisi' => $request->jenis_penerima == 'Perdivisi' ? $divisiBaru : null,

        'processor' => $maping->processor,

        'ram' => $maping->ram,

        'device_id' => $maping->device_id,

        'produk_id' => $maping->produk_id,

        'system' => $maping->system,

        'version' => $maping->version,

        'instal_on' => $maping->instal_on,

        'tanggal_digunakan' => $request->tanggal_mutasi,

        'catatan' => $maping->catatan,

        'status' => 'aktif',
      ]);
      // dd($mapingBaru);

      /*
|--------------------------------------------------------------------------
| COPY HAK AKSES
|--------------------------------------------------------------------------
*/

      $aksesBerhasil = [];

      $aksesGagal = [];

      if ($request->opsi_hak_akses == 'copy') {
        $aksesLama = MapingAccess::with('access')
          ->where('maping_id', $maping->id)
          ->get();

        foreach ($aksesLama as $item) {
          if (!$item->access) {
            continue;
          }

          /*
        |--------------------------------------------------------------------------
        | CARI ACCESS DI PERUSAHAAN TUJUAN
        |--------------------------------------------------------------------------
        */

          $accessBaru = Access::where('id_perusahaan', $perusahaanTujuan->id)
            ->where('kategori', $item->access->kategori)
            ->where('jenis', $item->access->jenis)
            ->where('nama_akses', $item->access->nama_akses)
            ->first();

          if (!$accessBaru) {
            $aksesGagal[] = $item->access->nama_akses;

            continue;
          }

          MapingAccess::create([
            'maping_id' => $mapingBaru->id,

            'access_id' => $accessBaru->id,

            'status' => $item->status,
          ]);

          $aksesBerhasil[] = $item->access->nama_akses;
        }
      }

      /*
|--------------------------------------------------------------------------
| SUSUN CATATAN HAK AKSES
|--------------------------------------------------------------------------
*/

      $catatanHakAkses = '';

      if ($request->opsi_hak_akses == 'copy') {
        if (count($aksesBerhasil)) {
          $catatanHakAkses .= "Hak akses berhasil disalin :\n- " . implode("\n- ", $aksesBerhasil);
        }

        if (count($aksesGagal)) {
          if ($catatanHakAkses != '') {
            $catatanHakAkses .= "\n\n";
          }

          $catatanHakAkses .= "Hak akses yang belum tersedia di perusahaan tujuan :\n- " . implode("\n- ", $aksesGagal);
        }
      }
      /*
|--------------------------------------------------------------------------
| PENERIMA LAMA
|--------------------------------------------------------------------------
*/

      if ($maping->jenis_penerima == 'Perorangan') {
        $userLama = optional($maping->karyawan)->nama_karyawan;
      } else {
        $userLama = $maping->divisi;
      }
      /*
|--------------------------------------------------------------------------
| PENERIMA BARU
|--------------------------------------------------------------------------
*/

      if ($request->jenis_penerima == 'Perorangan') {
        $userBaru = $karyawanBaru->nama_karyawan;
      } else {
        $userBaru = $request->divisi;
      }

      /*
|--------------------------------------------------------------------------
| SIMPAN HISTORY MUTASI
|--------------------------------------------------------------------------
*/

      $historyMutasi = HistoryMutasi::create([
        'inventaris_id' => $inventaris->id,
        'maping_id' => $maping->id,
        'jenis_mutasi' => 'antar_perusahaan',
        'id_perusahaan_asal' => $maping->id_perusahaan,
        'id_perusahaan_tujuan' => $perusahaanTujuan->id,
        'kode_aset_lama' => $inventaris->kode_aset,
        'kode_aset_baru' => $kodeAsetBaru,
        'no_inventaris_lama' => $inventaris->no_inventaris,
        'no_inventaris_baru' => $noInventarisBaru,
        'nama_aset' => optional($inventaris->dataAset->kategori)->nama_barang,
        'lokasi_lama' => optional($lokasiLama)->nama_lokasi,
        'lokasi_baru' => $lokasiBaru->nama_lokasi,
        'user_lama' => $userLama,
        'user_baru' => $userBaru,
        'tanggal_mutasi' => $request->tanggal_mutasi,
        'catatan' => trim(($request->catatan ?? '') . "\n\n" . $catatanHakAkses),
        'opsi_hak_akses' => $request->opsi_hak_akses,
        'created_by' => auth()->id(),
      ]);
      $masukBaru->update([
        'history_mutasi_id' => $historyMutasi->id,
      ]);

      /*
|--------------------------------------------------------------------------
| UPDATE MAPPING LAMA
|--------------------------------------------------------------------------
*/

      $maping->update([
        'status' => 'selesai',
      ]);

      /*
|--------------------------------------------------------------------------
| COMMIT
|--------------------------------------------------------------------------
*/

      DB::commit();

      $message = 'Mutasi antar perusahaan berhasil diproses.';

      if (count($aksesGagal)) {
        $message .= ' Sebagian hak akses belum tersedia di perusahaan tujuan.';
      }

      $maping->loadMissing('keluar.inventaris');
      $dataAsetId = $maping->keluar?->inventaris?->data_aset_id;
      $targetUrl = $dataAsetId ? route('history.perjalanan.show', $dataAsetId) : route('history.perjalanan.index');

      return redirect($targetUrl)
        ->with('success', $message)
        ->with('akses_gagal', $aksesGagal);
    } catch (\Throwable $e) {
      DB::rollBack();

      dd([
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile(),
      ]);
    }
  }

  public function searchUserMutasi(Request $request)
  {
    $query = Karyawan::query();

    $query->where('id_perusahaan', $request->perusahaan);

    if ($request->filled('divisi')) {
      $query->where('divisi', $request->divisi);
    }

    if ($request->filled('keyword')) {
      $query->where(function ($q) use ($request) {
        $q->where('nama_karyawan', 'like', '%' . $request->keyword . '%')->orWhere(
          'kode_karyawan',
          'like',
          '%' . $request->keyword . '%'
        );
      });
    }

    return response()->json(
      $query
        ->orderBy('nama_karyawan')

        ->limit(10)

        ->get()
    );
  }
  public function getLokasi(Request $request)
  {
    $lokasis = Lokasi::withoutGlobalScope('perusahaan')
      ->where('id_perusahaan', $request->perusahaan)
      ->orderBy('nama_lokasi')
      ->get();

    return response()->json($lokasis);
  }
  public function getDivisi(Request $request)
  {
    return Karyawan::where('id_perusahaan', $request->perusahaan)
      ->select('divisi')
      ->distinct()
      ->orderBy('divisi')
      ->get();
  }

  public function searchKaryawan(Request $request)
  {
    $keyword = $request->q;
    $perusahaanId = $request->perusahaan_id;

    $karyawan = Karyawan::query()

      ->when($perusahaanId, function ($q) use ($perusahaanId) {
        $q->where('id_perusahaan', $perusahaanId);
      })

      ->when($keyword, function ($q) use ($keyword) {
        $q->where(function ($sub) use ($keyword) {
          $sub
            ->where('kode_karyawan', 'like', "%{$keyword}%")
            ->orWhere('nama_karyawan', 'like', "%{$keyword}%")
            ->orWhere('divisi', 'like', "%{$keyword}%");
        });
      })

      ->orderBy('nama_karyawan')
      ->limit(15)

      ->get(['id', 'kode_karyawan', 'nama_karyawan', 'divisi']);

    return response()->json($karyawan);
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Pendaftaran;
use App\Models\Pertumbuhan;
use Illuminate\Http\Request;
use App\Models\Anthropometri;
use App\Models\ZScore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardAnthropometriController extends Controller
{
  /**
   * Display a listing of the resource.
   */



  public function showByGender(Request $request)
  {
    $routeName = $request->route()->getName();
    $gender = $routeName === 'anthropometri.laki' ? 'laki-laki' : 'perempuan';

    $anthropometriData = Anthropometri::whereHas('pendaftaran', function ($query) use ($gender) {
      $query->where('jenis_kelamin', $gender);
    })->with('pendaftaran')->paginate(7);

    return view('content.dashboard.anthropometri.index', compact('anthropometriData', 'gender', 'routeName'));
  }

  public function indexLakiLaki(Request $request)
  {

    // $anthropometriData = Anthropometri::where('jenis_kelamin', 'laki-laki')->latest()->get();

    $search = $request->input('search');

    $anthropometriData = Anthropometri::join('pendaftarans', 'anthropometris.pendaftaran_id', '=', 'pendaftarans.id')
      ->where('anthropometris.jenis_kelamin', 'laki-laki')
      ->select('anthropometris.*', 'pendaftarans.nama_balita', 'pendaftarans.nik')
      ->latest();

    if ($search) {
      $anthropometriData = $anthropometriData->where(function ($query) use ($search) {
        $query->where('pendaftarans.nama_balita', 'like', '%' . $search . '%')
          ->orWhere('pendaftarans.nik', 'like', '%' . $search . '%');
      });
    }

    // Pagination
    $anthropometriData = $anthropometriData->paginate(7);

    return view('content.dashboard.anthropometri.indexLakiLaki', compact('anthropometriData'));
  }

  public function indexPerempuan(Request $request)
  {

    // $anthropometriData = Anthropometri::where('jenis_kelamin', 'perempuan')->latest()->get();

    $search = $request->input('search');
    $anthropometriData = Anthropometri::join('pendaftarans', 'anthropometris.pendaftaran_id', '=', 'pendaftarans.id')
      ->where('anthropometris.jenis_kelamin', 'perempuan')
      ->select('anthropometris.*', 'pendaftarans.nama_balita', 'pendaftarans.nik')
      ->latest();

    if ($search) {
      $anthropometriData = $anthropometriData->where(function ($query) use ($search) {
        $query->where('pendaftarans.nama_balita', 'like', '%' . $search . '%')
          ->orWhere('pendaftarans.nik', 'like', '%' . $search . '%');
      });
    }

    // Pagination
    $anthropometriData = $anthropometriData->paginate(7);

    return view('content.dashboard.anthropometri.indexPerempuan', compact('anthropometriData'));
  }

  public function observasi(Request $request, $id)
  {
    $dataObserv = Anthropometri::findOrFail($id);

    /** Get filter berdasarkan bulan
     */
    $latestMonthData = Pertumbuhan::where('anthropometri_id', $dataObserv->id)
      ->select(DB::raw("MAX(CONCAT(LPAD(bulan, 2, '0'), '/', tahun)) as latest_month"))
      ->first();

    if ($latestMonthData && $latestMonthData->latest_month) {
      $selectedMonth = $request->input('month') ?? $latestMonthData->latest_month;
    } else {
      return redirect()->back()->with('error', 'Tidak ada data riwayat pertumbuhan ditemukan.');
    }


    $parsedDate = Carbon::createFromFormat('m/Y', $selectedMonth);

    $getAllMonthsBased = Pertumbuhan::where('anthropometri_id', $dataObserv->id)
      ->select(DB::raw("CONCAT(LPAD(bulan, 2, '0'), '/', tahun) as month"))
      ->groupBy('month')
      ->pluck('month')
      ->toArray();
    /** -----------------------------
     */

    // $item = Pertumbuhan::where('anthropometri_id', $dataObserv->id)
    //   ->orderBy('tahun', 'desc')
    //   ->orderBy('bulan', 'desc')
    //   ->first();

    $item = Pertumbuhan::where('anthropometri_id', $dataObserv->id)
      ->where(DB::raw("CONCAT(LPAD(bulan, 2, '0'), '/', tahun)"), $selectedMonth) // filter bulan
      ->orderBy('tahun', 'desc')
      ->orderBy('bulan', 'desc')
      ->first();


    if ($item) {
      $usiaBalita = $item->usia;

      $pesan = '';
      if ($item->status_gizi == 'sangat pendek (severely stunted)') {
        $pesan .=
          '- Hasil perhitungan Z-Score <span class="text-primary fw-bold">' . number_format($item->z_score, 2, '.', '')  . '</span> <br>' .
          ' - Berat badan balita terakhir adalah ' . $item->berat_badan . ' kg<br>' .
          '- Tinggi badan balita terakhir adalah ' . $item->tinggi_badan . ' cm<br>' .
          '- Balita dengan kategori status gizi  <span class="text-danger fw-bold"> sangat pendek (severely stunted)</span> <br>' .
          "- SARAN: Segera lakukan konsultasi ke tenaga kesehatan untuk mendapatkan penanganan yang tepat.";
      } elseif ($item->status_gizi == 'pendek (stunted)') {
        $pesan .=
          '- Hasil perhitungan Z-Score <span class="text-primary fw-bold">' . number_format($item->z_score, 2, '.', '')  . '</span> <br>' .
          ' - Berat badan balita terakhir adalah ' . $item->berat_badan . ' kg<br>' .
          '- Tinggi badan balita terakhir adalah ' . $item->tinggi_badan . ' cm<br>' .
          '- Balita dengan kategori status gizi  <span class="text-info fw-bold"> pendek </span> <br>' .
          "- SARAN: Konsultasikan kondisi anak ke puskesmas atau dokter spesialis gizi untuk mendapatkan saran pola makan yang lebih baik.";
      } elseif ($item->status_gizi == 'normal') {
        $pesan .=
          '- Hasil perhitungan Z-Score <span class="text-primary fw-bold">' . number_format($item->z_score, 2, '.', '')  . '</span> <br>' .
          ' - Berat badan balita terakhir adalah ' . $item->berat_badan . ' kg<br>' .
          '- Tinggi badan balita terakhir adalah ' . $item->tinggi_badan . ' cm<br>' .
          '- Balita dengan kategori status gizi  <span class="text-primary fw-bold"> normal </span> <br>' .
          "- SARAN: Pertahankan pola makan yang seimbang dan gizi yang cukup.";
      } elseif ($item->status_gizi == 'tinggi') {
        $pesan .=
          '- Hasil perhitungan Z-Score <span class="text-primary fw-bold">' . number_format($item->z_score, 2, '.', '')  . '</span> <br>' .
          ' - Berat badan balita terakhir adalah ' . $item->berat_badan . ' kg<br>' .
          '- Tinggi badan balita terakhir adalah ' . $item->tinggi_badan . ' cm<br>' .
          '- Balita dengan kategori status gizi  <span class="text-info fw-bold"> tinggi </span> <br>' .
          "- SARAN: Kondisi ini biasanya tidak memerlukan intervensi khusus, namun tetap lakukan pemantauan tumbuh kembang secara rutin.";
      }

      return view('content.dashboard.anthropometri.observasi-result', compact(
        'dataObserv',
        'item',
        'pesan',
        'selectedMonth',
        'getAllMonthsBased'
      ));
    } else {
      return redirect()->back()->with('error', 'Tidak ada hasil observasi ditemukan.');
    }
  }

  public function calculateStunting($tinggiBadan, $usiaBulan, $beratBadan, $jenisKelamin)
  {
    $dataTinggiBadanLk  = [
      0 => [44.2, 46.1, 48, 49.9, 51.8, 53.7, 55.6],
      1 => [48.9, 50.8, 52.8, 54.7, 56.7, 58.6, 60.6],
      2 => [52.4, 54.4, 56.4, 58.4, 60.4, 62.4, 64.4],
      3 => [55.3, 57.3, 59.4, 61.4, 63.5, 65.5, 67.6],
      4 => [57.6, 59.7, 61.8, 63.9, 66, 68, 70.1],
      5 => [59.6, 61.7, 63.8, 65.9, 68, 70.1, 72.2],
      6 => [61.2, 63.3, 65.5, 67.6, 69.8, 71.9, 74],
      7 => [62.7, 64.8, 67, 69.2, 71.3, 73.5, 75.7],
      8 => [64, 66.2, 68.4, 70.6, 72.8, 75, 77.2],
      9 => [65.2, 67.5, 69.7, 72, 74.2, 76.5, 78.7],
      10 => [66.4, 68.7, 71, 73.3, 75.6, 77.9, 80.1],
      11 => [67.6, 69.9, 72.2, 74.5, 76.9, 79.2, 81.5],
      12 => [68.6, 71, 73.4, 75.7, 78.1, 80.5, 82.9],
      13 => [69.6, 72.1, 74.5, 76.9, 79.3, 81.8, 84.2],
      14 => [70.6, 73.1, 75.6, 78, 80.5, 83, 85.5],
      15 => [71.6, 74.1, 76.6, 79.1, 81.7, 84.2, 86.7],
      16 => [72.5, 75, 77.6, 80.2, 82.8, 85.4, 88],
      17 => [73.3, 76, 78.6, 81.2, 83.9, 86.5, 89.2],
      18 => [74.2, 76.9, 79.6, 82.3, 85, 87.7, 90.4],
      19 => [75, 77.7, 80.5, 83.2, 86, 88.8, 91.5],
      20 => [75.8, 78.6, 81.4, 84.2, 87, 89.8, 92.6],
      21 => [76.5, 79.4, 82.3, 85.1, 88, 90.9, 93.8],
      22 => [77.2, 80.2, 83.1, 86, 89, 91.9, 94.9],
      23 => [78, 81, 83.9, 86.9, 89.9, 92.9, 95.9],
      24 => [78.7, 81.7, 84.8, 87.8, 90.9, 93.9, 97],
      24 => [78, 81, 84.1, 87.1, 90.2, 93.2, 96.3],
      25 => [78.6, 81.7, 84.9, 88, 91.1, 94.2, 97.3],
      26 => [79.3, 82.5, 85.6, 88.8, 92, 95.2, 98.3],
      27 => [79.9, 83.1, 86.4, 89.6, 92.9, 96.1, 99.3],
      28 => [80.5, 83.8, 87.1, 90.4, 93.7, 97, 100.3],
      29 => [81.1, 84.5, 87.8, 91.2, 94.5, 97.9, 101.2],
      30 => [81.7, 85.1, 88.5, 91.9, 95.3, 98.7, 102.1],
      31 => [82.3, 85.7, 89.2, 92.7, 96.1, 99.6, 103],
      32 => [82.8, 86.4, 89.9, 93.4, 96.9, 100.4, 103.9],
      33 => [83.4, 86.9, 90.5, 94.1, 97.6, 101.2, 104.8],
      34 => [83.9, 87.5, 91.1, 94.8, 98.4, 102, 105.6],
      35 => [84.4, 88.1, 91.8, 95.4, 99.1, 102.7, 106.4],
      36 => [85, 88.7, 92.4, 96.1, 99.8, 103.5, 107.2],
      37 => [85.5, 89.2, 93, 96.7, 100.5, 104.2, 108],
      38 => [86, 89.8, 93.6, 97.4, 101.2, 105, 108.8],
      39 => [86.5, 90.3, 94.2, 98, 101.8, 105.7, 109.5],
      40 => [87, 90.9, 94.7, 98.6, 102.5, 106.4, 110.3],
      41 => [87.5, 91.4, 95.3, 99.2, 103.2, 107.1, 111],
      42 => [88, 91.9, 95.9, 99.9, 103.8, 107.8, 111.7],
      43 => [88.4, 92.4, 96.4, 100.4, 104.5, 108.5, 112.5],
      44 => [88.9, 93, 97, 101, 105.1, 109.1, 113.2],
      45 => [89.4, 93.5, 97.5, 101.6, 105.7, 109.8, 113.9],
      46 => [89.8, 94, 98.1, 102.2, 106.3, 110.4, 114.6],
      47 => [90.3, 94.4, 98.6, 102.8, 106.9, 111.1, 115.2],
      48 => [90.7, 94.9, 99.1, 103.3, 107.5, 111.7, 115.9],
      49 => [91.2, 95.4, 99.7, 103.9, 108.1, 112.4, 116.6],
      50 => [91.6, 95.9, 100.2, 104.4, 108.7, 113, 117.3],
      51 => [92.1, 96.4, 100.7, 105, 109.3, 113.6, 117.9],
      52 => [92.5, 96.9, 101.2, 105.6, 109.9, 114.2, 118.6],
      53 => [93, 97.4, 101.7, 106.1, 110.5, 114.9, 119.2],
      54 => [93.4, 97.8, 102.3, 106.7, 111.1, 115.5, 119.9],
      55 => [93.9, 98.3, 102.8, 107.2, 111.7, 116.1, 120.6],
      56 => [94.3, 98.8, 103.3, 107.8, 112.3, 116.7, 121.2],
      57 => [94.7, 99.3, 103.8, 108.3, 112.8, 117.4, 121.9],
      58 => [95.2, 99.7, 104.3, 108.9, 113.4, 118, 122.6],
      59 => [95.6, 100.2, 104.8, 109.4, 114, 118.6, 123.2],
      60 => [96.1, 100.7, 105.3, 110, 114.6, 119.2, 123.9]
    ];

    $dataTinggiBadanPr  = [
      0 => [43.6, 45.4, 47.3, 49.1, 51, 52.9, 54.7],
      1 => [47.8, 49.8, 51.7, 53.7, 55.6, 57.6, 59.5],
      2 => [51, 53, 55, 57.1, 59.1, 61.1, 63.2],
      3 => [53.5, 55.6, 57.7, 59.8, 61.9, 64, 66.1],
      4 => [55.6, 57.8, 59.9, 62.1, 64.3, 66.4, 68.6],
      5 => [57.4, 59.6, 61.8, 64, 66.2, 68.5, 70.7],
      6 => [58.9, 61.2, 63.5, 65.7, 68, 70.3, 72.5],
      7 => [60.3, 62.7, 65, 67.3, 69.6, 71.9, 74.2],
      8 => [61.7, 64, 66.4, 68.7, 71.1, 73.5, 75.8],
      9 => [62.9, 65.3, 67.7, 70.1, 72.6, 75, 77.4],
      10 => [64.1, 66.5, 69, 71.5, 73.9, 76.4, 78.9],
      11 => [65.2, 67.7, 70.3, 72.8, 75.3, 77.8, 80.3],
      12 => [66.3, 68.9, 71.4, 74, 76.6, 79.2, 81.7],
      13 => [67.3, 70, 72.6, 75.2, 77.8, 80.5, 83.1],
      14 => [68.3, 71, 73.7, 76.4, 79.1, 81.7, 84.4],
      15 => [69.3, 72, 74.8, 77.5, 80.2, 83, 85.7],
      16 => [70.2, 73, 75.8, 78.6, 81.4, 84.2, 87],
      17 => [71.1, 74, 76.8, 79.7, 82.5, 85.4, 88.2],
      18 => [72, 74.9, 77.8, 80.7, 83.6, 86.5, 89.4],
      19 => [72.8, 75.8, 78.8, 81.7, 84.7, 87.6, 90.6],
      20 => [73.7, 76.7, 79.7, 82.7, 85.7, 88.7, 91.7],
      21 => [74.5, 77.5, 80.6, 83.7, 86.7, 89.8, 92.9],
      22 => [75.2, 78.4, 81.5, 84.6, 87.7, 90.8, 94],
      23 => [76, 79.2, 82.3, 85.5, 88.7, 91.9, 95],
      24 => [76.7, 80, 83.2, 86.4, 89.6, 92.9, 96.1],
      25 => [76.8, 80, 83.3, 86.6, 89.9, 93.1, 96.4],
      26 => [77.5, 80.8, 84.1, 87.4, 90.8, 94.1, 97.4],
      27 => [78.1, 81.5, 84.9, 88.3, 91.7, 95, 98.4],
      28 => [78.8, 82.2, 85.7, 89.1, 92.5, 96, 99.4],
      29 => [79.5, 82.9, 86.4, 89.9, 93.4, 96.9, 100.3],
      30 => [80.1, 83.6, 87.1, 90.7, 94.2, 97.7, 101.3],
      31 => [80.7, 84.3, 87.9, 91.4, 95, 98.6, 102.2],
      32 => [81.3, 84.9, 88.6, 92.2, 95.8, 99.4, 103.1],
      33 => [81.9, 85.6, 89.3, 92.9, 96.6, 100.3, 103.9],
      34 => [82.5, 86.2, 89.9, 93.6, 97.4, 101.1, 104.8],
      35 => [83.1, 86.8, 90.6, 94.4, 98.1, 101.9, 105.6],
      36 => [83.6, 87.4, 91.2, 95.1, 98.9, 102.7, 106.5],
      37 => [84.2, 88, 91.9, 95.7, 99.6, 103.4, 107.3],
      38 => [84.7, 88.6, 92.5, 96.4, 100.3, 104.2, 108.1],
      39 => [85.3, 89.2, 93.1, 97.1, 101, 105, 108.9],
      40 => [85.8, 89.8, 93.8, 97.7, 101.7, 105.7, 109.7],
      41 => [86.3, 90.4, 94.4, 98.4, 102.4, 106.4, 110.5],
      42 => [86.8, 90.9, 95, 99, 103.1, 107.2, 111.2],
      43 => [87.4, 91.5, 95.6, 99.7, 103.8, 107.9, 112],
      44 => [87.9, 92, 96.2, 100.3, 104.5, 108.6, 112.7],
      45 => [88.4, 92.5, 96.7, 100.9, 105.1, 109.3, 113.5],
      46 => [88.9, 93.1, 97.3, 101.5, 105.8, 110, 114.2],
      47 => [89.3, 93.6, 97.9, 102.1, 106.4, 110.7, 114.9],
      48 => [89.8, 94.1, 98.4, 102.7, 107, 111.3, 115.7],
      49 => [90.3, 94.6, 99, 103.3, 107.7, 112, 116.4],
      50 => [90.7, 95.1, 99.5, 103.9, 108.3, 112.7, 117.1],
      51 => [91.2, 95.6, 100.1, 104.5, 108.9, 113.3, 117.7],
      52 => [91.7, 96.1, 100.6, 105, 109.5, 114, 118.4],
      53 => [92.1, 96.6, 101.1, 105.6, 110.1, 114.6, 119.1],
      54 => [92.6, 97.1, 101.6, 106.2, 110.7, 115.2, 119.8],
      55 => [93, 97.6, 102.2, 106.7, 111.3, 115.9, 120.4],
      56 => [93.4, 98.1, 102.7, 107.3, 111.9, 116.5, 121.1],
      57 => [93.9, 98.5, 103.2, 107.8, 112.5, 117.1, 121.8],
      58 => [94.3, 99, 103.7, 108.4, 113, 117.7, 122.4],
      59 => [94.7, 99.5, 104.2, 108.9, 113.6, 118.3, 123.1],
      60 => [95.2, 99.9, 104.7, 109.4, 114.2, 118.9, 123.7]
    ];

    $dataTinggiBadan = ($jenisKelamin === 'laki-laki') ? $dataTinggiBadanLk : $dataTinggiBadanPr;

    if ($usiaBulan < 0 || $usiaBulan > 60) {
      return [
        'status' => 'Usia tidak valid',
        'status_gizi' => 'N/A',
        'zscore' => 'N/A',
        'keterangan' => 'Usia anak melebihi batas maksimal untuk kategori balita (60 bulan), silahkan lanjutkan pemantauan
        melalui layanan kesehatan anak usia sekolah.'
      ];
    }

    $data = $dataTinggiBadan[$usiaBulan] ?? null;
    // dd($usiaBulan);

    if ($data) {
      // Z-Score berdasarkan tinggi badan
      $minus2SD = $data[1];
      $minus1SD = $data[2];
      $median = $data[3]; // Median (nilai ke -4)
      $plus1SD = $data[4];
      $plus2SD = $data[5];

      // Hitung z-score
      $x = ($tinggiBadan - $median);
      $yMinus = ($median - $minus1SD);
      $yPositif = ($median - $plus1SD);

      $zScore = $x < 0 ? ($tinggiBadan - $median) / $yMinus : ($tinggiBadan - $median) / $yPositif;

      if ($zScore < -3) {
        $status = 'Stunting';
      } elseif ($zScore >= -3 && $zScore < -2) {
        $status = 'Stunting';
      } elseif ($zScore >= -2 && $zScore <= 3) {
        $status = 'Normal';
      } else {
        $status = 'Tinggi';
      }

      //  z-score
      if ($zScore < -3) {
        $keterangan = 'sangat pendek (severely stunted)';
      } elseif ($zScore >= -3 && $zScore < -2) {
        $keterangan = 'pendek (stunted)';
      } elseif ($zScore >= -2 && $zScore <= 3) {
        $keterangan = 'normal';
      } else {
        $keterangan = 'tinggi';
      }

      return [
        'status' => $status,
        'zscore' => $zScore,
        'status_gizi' => $keterangan,
        'jenis_kelamin' => $jenisKelamin,

        //deskripsi detail keterangan.
        'keterangan' => sprintf(
          '- Hasil perhitungan Z-Score: <span class="text-primary"> %.2f</span> <br>' .
            ' - Berat badan balita terakhir: %s kg<br>' .
            '- Tinggi badan balita terakhir: %d cm<br>' .
            '- Balita dengan kategori status gizi %s',
          $zScore,
          $beratBadan,
          $tinggiBadan,
          $jenisKelamin,
          $keterangan
        )
      ];
    } else {
      return [
        'status' => 'Data tinggi badan tidak ditemukan untuk usia ini',
        'keterangan' => ''
      ];
    }
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Request $request)
  {
    $routeName = $request->route()->getName();
    $gender = $routeName === 'anthropometri.laki.create' ? 'laki-laki' : 'perempuan';

    return view('content.dashboard.anthropometri.create', compact('gender'));
  }

  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'nik' => 'required|string|max:16',
      'nama_balita' => 'required|string|max:100',
      'tinggi_badan' => 'required|numeric|min:0|max:300',
      'berat_badan' => 'required|numeric|min:0|max:200',
      'jenis_kelamin' => 'required|string|in:laki-laki,perempuan'
    ]);

    // Ambil data pendaftaran berdasarkan NIK
    $pendaftaran = Pendaftaran::where('nik', $validatedData['nik'])->first();

    if (!$pendaftaran) {
      return redirect()->back()->with('error', 'NIK tidak ditemukan.');
    }

    $tglLahir  = $pendaftaran->tanggal_lahir;
    $tglLahir = Carbon::parse($tglLahir);
    $usiaBalita = $tglLahir->diffInMonths(Carbon::now());

    $validatedData['pendaftaran_id'] = $pendaftaran->id;
    $validatedData['usia'] = $usiaBalita;

    // Kalkulasi Stunting
    $stuntingResult = $this->calculateStunting(
      $validatedData['tinggi_badan'],
      $usiaBalita,
      $validatedData['berat_badan'],
      $validatedData['jenis_kelamin']
    );

    $validatedData['status_stunting'] = $stuntingResult['status'];
    $validatedData['status_gizi'] = $stuntingResult['status_gizi'];
    $validatedData['z_score'] = $stuntingResult['zscore'];

    if (empty($validatedData['jenis_kelamin'])) {
      return redirect()->back()->with('error', 'Jenis kelamin is required.');
    }

    // cek if pendaftaran_id already exists di anthropometri table
    $existingAnthropometri = Anthropometri::where('pendaftaran_id', $validatedData['pendaftaran_id'])->first();

    if ($existingAnthropometri) {
      return redirect()->back()->with('error', 'Gagal menyimpan. Data untuk NIK ' . $validatedData['nik'] . ' ini sudah terdaftar di Anthropometri!');
    }

    try {
      DB::beginTransaction();

      // $anthropometri = Anthropometri::create($validatedData);

      $anthropometri = Anthropometri::create([
        'nik' => $validatedData['nik'],
        'nama_balita' => $validatedData['nama_balita'],
        'tinggi_badan' => $validatedData['tinggi_badan'],
        'berat_badan' => $validatedData['berat_badan'],
        'jenis_kelamin' => $validatedData['jenis_kelamin'],
        'pendaftaran_id' => $validatedData['pendaftaran_id'],
        'usia' => $validatedData['usia'],
      ]);


      // Simpan track data ke table pertumbuhan
      Pertumbuhan::create([
        'anthropometri_id' => $anthropometri->id,
        'pendaftaran_id' => $anthropometri->pendaftaran_id,
        'bulan' => Carbon::now()->format('m'),
        'tahun' => Carbon::now()->format('Y'),
        'tinggi_badan' => $validatedData['tinggi_badan'],
        'berat_badan' => $validatedData['berat_badan'],
        'cara_ukur' => '-',
        'status_stunting' => $validatedData['status_stunting'],
        'status_gizi' => $validatedData['status_gizi'],
        'usia' => $usiaBalita,
        'z_score' => $validatedData['z_score'],
      ]);

      DB::commit();

      // WhatsApp message
      $nomorTelepon = preg_replace('/[^0-9]/', '', $pendaftaran->no_telepon);

      if ($stuntingResult['zscore'] === 'N/A') {
        $formattedZScore = 'N/A';
      } else {
        $formattedZScore = number_format((float)$stuntingResult['zscore'], 2);
      }

      $pesan = "Halo {$pendaftaran->nama_ortu},\n\n"
        . "Hasil pengukuran Panjang Badan/Tinggi Badan (PB/TB) anak Anda, "
        . "{$pendaftaran->nama_balita} yang berusia {$validatedData['usia']} bulan adalah sebagai berikut:\n\n"
        . "Status Stunting: {$stuntingResult['status']}\n"
        . "Status Gizi: {$stuntingResult['status_gizi']}\n"
        . "Z-Score: " . $formattedZScore . "\n\n"
        . "Saran: ";

      if ($stuntingResult['status_gizi'] == 'sangat pendek (severely stunted)') {
        $pesan .= "Segera lakukan konsultasi ke tenaga kesehatan untuk mendapatkan penanganan yang tepat.";
      } elseif ($stuntingResult['status_gizi'] == 'pendek (stunted)') {
        $pesan .= "Konsultasikan kondisi anak ke puskesmas atau dokter spesialis gizi untuk mendapatkan saran pola makan yang lebih baik.";
      } elseif ($stuntingResult['status_gizi'] == 'normal') {
        $pesan .= "Pertahankan pola makan yang seimbang dan gizi yang cukup.";
      } elseif ($stuntingResult['status_gizi'] == 'tinggi') {
        $pesan .= "Kondisi ini biasanya tidak memerlukan intervensi khusus, namun tetap lakukan pemantauan tumbuh kembang secara rutin.";
      }

      $pesan = urlencode($pesan);
      $whatsappUrl = "https://wa.me/$nomorTelepon?text=$pesan";

      if ($request->input('jenis_kelamin') === 'laki-laki') {
        return redirect('/dashboard/anthropometri/laki-laki')
          ->with('success', 'Data anthropometri berhasil disimpan.')
          ->with('whatsappUrl', $whatsappUrl);
      } else {
        return redirect('/dashboard/anthropometri/perempuan')
          ->with('success', 'Data anthropometri berhasil disimpan.')
          ->with('whatsappUrl', $whatsappUrl);
      }
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e->getMessage());
      return redirect()->back()->with('error', 'Data tidak berhasil disimpan. Kesalahan: ' . $e->getMessage());
    }
  }


  function edit(string $id)
  {
    $dataAnthropo = Anthropometri::findOrFail($id);

    $riwayat = Pertumbuhan::where('anthropometri_id', $id)
      ->orderBy('tahun', 'asc')
      ->orderBy('bulan', 'asc')
      ->get();


    return view('content.dashboard.anthropometri.edit', compact('dataAnthropo', 'riwayat'));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $data = Anthropometri::findOrFail($id);
    $data->delete();

    return redirect()->back();
  }
}
<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ZScore;
use Illuminate\View\View;
use App\Models\Pertumbuhan;
use Illuminate\Http\Request;
use App\Models\Anthropometri;
use App\Models\Pendaftaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardPertumbuhanController extends Controller
{
  function index(Request $request, $id): View
  {
    $data = Anthropometri::findOrFail($id);
    $zScoreLimits = ZScore::pluck('nilai')->toArray();

    $pertumbuhanRecords = Pertumbuhan::where('anthropometri_id', $data->id)
      ->select(DB::raw("CONCAT(LPAD(bulan, 2, '0'), '/', tahun) as month"), 'z_score', 'berat_badan', 'tinggi_badan', 'usia')
      ->orderBy('tahun')
      ->orderBy('bulan')
      ->get();

    $growthData = $pertumbuhanRecords->map(function ($record) {
      return [
        'month' => $record->month,
        'z_score' => $record->z_score,
        'berat_badan' => $record->berat_badan,
        'tinggi_badan' => $record->tinggi_badan,
      ];
    })->toArray();

    //get latest usia balita
    $lastUsia = $pertumbuhanRecords->last()?->usia ?? null;
    // dd($lastUsia);

    // get latest berat dan tb trkhir balita
    $lastBbTb = $pertumbuhanRecords->last();

    // get last status gizi stuntingnya
    $lastRecord = $pertumbuhanRecords->last();
    $status_stunting = $lastRecord && $lastRecord->z_score < -2 ? 'stunting' : 'normal';

    // gap 12 bulan trakhir
    $currentMonth = Carbon::now();
    $last12Months = [];
    for ($i = 12; $i >= 0; $i--) { //ambil 6 dlu
      $last12Months[] = $currentMonth->copy()->subMonths($i)->format('m/Y');
    }

    return view('content.dashboard.pertumbuhan.index', compact(
      'data',
      'zScoreLimits',
      'growthData',
      'last12Months',
      'pertumbuhanRecords',
      'lastBbTb',
      'status_stunting',
      'lastUsia'
    ));
  }

  function indexOrtu(): View  //list page
  {
    $user = Auth::user();

    $dataAnak = Anthropometri::join('pendaftarans', 'pendaftarans.id', '=', 'anthropometris.pendaftaran_id')
      ->where('pendaftarans.email_ortu', $user->email)
      ->select(
        'pendaftarans.nama_balita',
        'pendaftarans.nama_posyandu',
        'anthropometris.*'
      )
      ->distinct() // not duplikasi
      ->get();

    // dd($dataAnak);

    $count = $dataAnak->count();

    return view('content.dashboard.pertumbuhan.index-ortu', compact('dataAnak', 'count'));
  }

  // function indexPetugas(Request $request, $id)
  // {

  //   $data = Anthropometri::findOrFail($id);

  //   $ages = $data->usia;
  //   $zScoreLimits = ZScore::pluck('nilai')->toArray();

  //   $zScore = $data->z_score;
  //   // dd($zScore);

  //   $status_gizi = '';
  //   if ($zScore < -3) {
  //     $status_gizi = 'severely-stunted';
  //   } elseif ($zScore >= -3 && $zScore < -2) {
  //     $status_gizi = 'stunted';
  //   } elseif ($zScore >= -2 && $zScore <= 3) {
  //     $status_gizi = 'normal';
  //   } else {
  //     $status_gizi = 'tinggi';
  //   }

  //   $latestMonthData = Pertumbuhan::where('anthropometri_id', $data->id)
  //     ->select(DB::raw("MAX(CONCAT(LPAD(bulan, 2, '0'), '/', tahun)) as latest_month"))
  //     ->first();

  //   // dd($latestMonthData);
  //   if ($latestMonthData && $latestMonthData->latest_month) {
  //     $selectedMonth = $request->input('month') ?? $latestMonthData->latest_month;
  //   } else {
  //     return redirect()->back()->with('error', 'Tidak ada data riwayat pertumbuhan ditemukan.');
  //   }

  //   $parsedDate = Carbon::createFromFormat('m/Y', $selectedMonth);

  //   /** Get grafik berdasarkan bulan
  //    */
  //   $getAllMonthsBased = Pertumbuhan::where('anthropometri_id', $data->id)
  //     ->select(DB::raw("CONCAT(LPAD(bulan, 2, '0'), '/', tahun) as month"))
  //     ->groupBy('month')
  //     ->pluck('month')
  //     ->toArray();

  //   $pertumbuhan = Pertumbuhan::where('anthropometri_id', $data->id)
  //     ->where('bulan', '=', $parsedDate->format('m'))
  //     ->where('tahun', '=', $parsedDate->format('Y'))
  //     ->get();


  //   $zScore = [];
  //   if ($pertumbuhan->isNotEmpty()) {
  //     foreach ($pertumbuhan as $item) {
  //       $zScore[] = $item->z_score;
  //       $data->berat_badan = $pertumbuhan->first()->berat_badan;
  //       $data->tinggi_badan = $pertumbuhan->first()->tinggi_badan;
  //       $data->usia = $pertumbuhan->first()->usia;
  //     }
  //     $ages = $pertumbuhan->first()->usia;
  //   } else {
  //     $zScore[] = null;
  //   }


  //   return view('content.dashboard.pertumbuhan.index-petugas', compact(
  //     'data',
  //     'status_gizi',
  //     'zScoreLimits',
  //     'zScore',
  //     'ages',
  //     'selectedMonth',
  //     'getAllMonthsBased'
  //   ));
  // }

  function indexPetugas(Request $request, $id)
  {
    $data = Anthropometri::findOrFail($id);
    $zScoreLimits = ZScore::pluck('nilai')->toArray();

    $pertumbuhanRecords = Pertumbuhan::where('anthropometri_id', $data->id)
      ->select(DB::raw("CONCAT(LPAD(bulan, 2, '0'), '/', tahun) as month"), 'z_score', 'berat_badan', 'tinggi_badan', 'usia')
      ->orderBy('tahun')
      ->orderBy('bulan')
      ->get();

    $growthData = $pertumbuhanRecords->map(function ($record) {
      return [
        'month' => $record->month,
        'z_score' => $record->z_score,
        'berat_badan' => $record->berat_badan,
        'tinggi_badan' => $record->tinggi_badan,
      ];
    })->toArray();

    //get latest usia balita
    $lastUsia = $pertumbuhanRecords->last()?->usia ?? null;
    // dd($lastUsia);

    // get latest berat dan tb trkhir balita
    $lastBbTb = $pertumbuhanRecords->last();

    // get last status gizi stuntingnya
    $lastRecord = $pertumbuhanRecords->last();
    $status_stunting = $lastRecord && $lastRecord->z_score < -2 ? 'stunting' : 'normal';


    // gap 6 bulan trakhir
    $currentMonth = Carbon::now();
    $last12Months = [];
    for ($i = 12; $i >= 0; $i--) { //ambil 6 dlu
      $last12Months[] = $currentMonth->copy()->subMonths($i)->format('m/Y');
    }

    return view('content.dashboard.pertumbuhan.index-petugas', compact(
      'data',
      'zScoreLimits',
      'growthData',
      'last12Months',
      'pertumbuhanRecords',
      'lastBbTb',
      'status_stunting',
      'lastUsia'
    ));
  }
}
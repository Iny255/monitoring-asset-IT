<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lokasi;
use Illuminate\View\View;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PendaftarExcelExport;

class LaporanController extends Controller
{
  function laporanPendaftarPosyandu(Request $request): View
  {
    $search = $request->input('search');
    $filterPosyandu = $request->input('filter_posyandu');

    $pendaftarans = Pendaftaran::latest();

    //$pendaftarans = Pendaftaran::with('anthropometri')->latest()->get();
    //  dd($pendaftarans);

    if ($search) {
      $pendaftarans = $pendaftarans->where(function ($query) use ($search) {
        $query->where('nama_balita', 'like', '%' . $search . '%')
          ->orWhere('nik', 'like', '%' . $search . '%');
      });
    }

    if ($filterPosyandu) {
      $pendaftarans = $pendaftarans->whereHas('lokasi', function ($query) use ($filterPosyandu) {
        $query->where('nama_posyandu', $filterPosyandu);
      });
    }

    // Pagination
    $pendaftarans = $pendaftarans->paginate(7);

    $posyandu = Lokasi::all();

    return view('content.dashboard.laporan.pendaftar-posyandu', compact('pendaftarans', 'posyandu'));
  }

  function exportExcel(Request $request)
  {
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    return Excel::download(new PendaftarExcelExport($startDate, $endDate), 'laporan_pendaftar_posyandu.xlsx');
  }

  // Method for PDF EXPORT
  protected $month;

  public function __construct(Request $request)
  {
    $this->month = $request->query('month');
  }

  public function exportPdf()
  {
    [$year, $month] = explode('-', $this->month);
    // dd($month);

    // get query data based bulan & tahun pada tabel pertumbuhans
    $pendaftarans = Pendaftaran::join('pertumbuhans', function ($join) use ($month, $year) {
      $join->on('pendaftarans.id', '=', 'pertumbuhans.pendaftaran_id')
        ->where('pertumbuhans.bulan', $month) // filter kolom bulan
        ->where('pertumbuhans.tahun', $year) // filter kolom tahun
      ;
    })
      ->select(
        'pendaftarans.*',
        'pertumbuhans.berat_badan',
        'pertumbuhans.tinggi_badan',
        'pertumbuhans.z_score',
        'pertumbuhans.usia',
        'pertumbuhans.status_gizi',
        'pertumbuhans.created_at as latest_pertumbuhan'
      )
      ->orderBy('latest_pertumbuhan', 'desc')
      ->get();

    $formattedMonth = Carbon::createFromDate($year, $month, 1)->format('F Y');
    $infoCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y, h:i:s');

    $tglSignature = Carbon::now('Asia/Jakarta')->format('d F Y');

    $pdf = Pdf::loadView('content.dashboard.laporan.pendaftaran-pdf', [
      'pendaftarans' => $pendaftarans,
      'month' => $formattedMonth,
      'infoCetak' => $infoCetak,
      'tglSignature' => $tglSignature
    ])
      ->setPaper('a4', 'landscape');

    return $pdf->download('laporan_posyandu_' . $this->month . '.pdf');
  }
}
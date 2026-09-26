<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $companyName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 10mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Calibri', 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 15px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 12px;
        }

        .report-header h2 {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .report-header h3 {
            font-size: 13px;
            font-weight: 700;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }

        .meta-info {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .table-troubleshoot {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }

        .table-troubleshoot th,
        .table-troubleshoot td {
            border: 1px solid #000000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .table-troubleshoot th {
            background-color: #99cad6 !important;
            color: #000000;
            text-align: center;
            font-weight: 700;
            line-height: 1.25;
        }

        .table-troubleshoot td.text-center {
            text-align: center;
        }

        .table-troubleshoot td.text-left {
            text-align: left;
        }

        .table-troubleshoot .check-mark {
            font-size: 13px;
            font-weight: bold;
            line-height: 1;
        }

        .signature-section {
            margin-top: 30px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 220px;
        }

        .signature-space {
            height: 60px;
        }

        /* TAMPILAN CETAK DI LAYAR vs SAAT PRINT */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .table-troubleshoot {
                page-break-inside: auto;
            }
            .table-troubleshoot tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .table-troubleshoot thead {
                display: table-header-group;
            }
        }

        @media screen {
            body {
                background: #f1f5f9;
                padding: 20px;
            }
            .sheet-container {
                background: #fff;
                max-width: 1250px;
                margin: 0 auto;
                padding: 25px 30px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>

    {{-- ACTION BAR (TIDAK TAMPIL SAAT PRINT) --}}
    <div class="no-print mb-4" style="max-width: 1250px; margin: 0 auto 20px auto;">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('e-ticket.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Tiket
                    </a>
                    <span class="badge bg-primary px-2.5 py-2">
                        <i class="bx bx-file me-1"></i> {{ count($tickets) }} Data Troubleshooting
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" onclick="window.print()" class="btn btn-primary btn-sm px-3">
                        <i class="bx bx-printer me-1"></i> Cetak / Simpan PDF
                    </button>
                    <a href="{{ route('e-ticket.export-pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm">
                        <i class="bx bx-download me-1"></i> Download PDF File
                    </a>
                    <a href="{{ route('e-ticket.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
                        <i class="bx bx-spreadsheet me-1"></i> Download Excel (.xlsx)
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KONTEN LAPORAN UTAMA --}}
    <div class="sheet-container">

        {{-- HEADER JUDUL --}}
        <div class="report-header">
            <h2>CHECKLIST TEMUAN &amp; TINDAKAN TROUBLESHOOT</h2>
            <h3>{{ strtoupper($companyName) }}</h3>
        </div>

        {{-- META DIVISI & BULAN --}}
        <div class="meta-info">
            <div>Divisi : {{ $divisionName }}</div>
            <div>Bulan : {{ $periodText }}</div>
        </div>

        {{-- TABEL TROUBLESHOOTING --}}
        <table class="table-troubleshoot">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3%;">No.</th>
                    <th rowspan="2" style="width: 8%;">Tanggal Masuk<br>Troubleshooting</th>
                    <th rowspan="2" style="width: 8%;">Tanggal Respont/<br>Tindakan</th>
                    <th rowspan="2" style="width: 12%;">Nama Perangkat</th>
                    <th rowspan="2" style="width: 8%;">Nama Pengguna</th>
                    <th rowspan="2" style="width: 7%;">Divisi</th>
                    <th rowspan="2" style="width: 14%;">Temuan dan Trouble</th>
                    <th rowspan="2" style="width: 14%;">Tindakan Perbaikan</th>
                    <th rowspan="2" style="width: 8%;">Tindakan<br>Pencegahan</th>
                    <th colspan="2" style="width: 6%;">Status</th>
                    <th rowspan="2" style="width: 14%;">Verifikasi &amp; Validasi Tindakan Perbaikan &amp; Pencegahan</th>
                    <th rowspan="2" style="width: 8%;">Tanggal<br>Penyelesaian</th>
                </tr>
                <tr>
                    <th style="width: 3%; padding: 2px;">OK</th>
                    <th style="width: 3%; padding: 2px;">NG</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $index => $tkt)
                    @php
                        $isOk = $tkt->isStatusOk();
                        $tglMasuk = $tkt->created_at ? $tkt->created_at->format('d-m-y') : '-';
                        $tglRespon = ($tkt->responded_at ?? $tkt->created_at) ? ($tkt->responded_at ?? $tkt->created_at)->format('d-m-y') : '-';
                        $tglSelesai = ($tkt->resolved_at ?? $tkt->closed_at) ? ($tkt->resolved_at ?? $tkt->closed_at)->format('d-m-y') : '-';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $tglMasuk }}</td>
                        <td class="text-center">{{ $tglRespon }}</td>
                        <td class="text-left">{{ $tkt->perangkat_name }}</td>
                        <td class="text-left">{{ $tkt->pelapor_name }}</td>
                        <td class="text-left">{{ $tkt->divisi_pelapor }}</td>
                        <td class="text-left">{{ $tkt->judul }}</td>
                        <td class="text-left">{{ $tkt->tindakan_perbaikan_formatted }}</td>
                        <td class="text-center">{{ $tkt->tindakan_pencegahan_formatted }}</td>
                        <td class="text-center">
                            @if ($isOk)
                                <span class="check-mark">&#10003;</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if (!$isOk)
                                <span class="check-mark">&#10003;</span>
                            @endif
                        </td>
                        <td class="text-left">{{ $tkt->verifikasi_formatted }}</td>
                        <td class="text-center">{{ $tglSelesai }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center py-4 text-muted">
                            <em>Tidak ada data tiket troubleshooting untuk periode yang dipilih.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TANDA TANGAN --}}
        <div class="signature-section d-flex justify-content-between align-items-start px-4">
            <div class="signature-box">
                <div>Dilaporkan Oleh,</div>
                <div class="fw-bold mt-1">Staff IT</div>
                <div class="signature-space"></div>
                <div class="border-top border-dark pt-1 fw-bold">( {{ auth()->user()->name ?? 'Nama Staff' }} )</div>
            </div>

            <div class="signature-box">
                <div>Mengetahui,</div>
                <div class="fw-bold mt-1">Asmen IT</div>
                <div class="signature-space"></div>
                <div class="border-top border-dark pt-1 fw-bold">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
            </div>

            <div class="signature-box">
                <div>Disetujui Oleh,</div>
                <div class="fw-bold mt-1">MR</div>
                <div class="signature-space"></div>
                <div class="border-top border-dark pt-1 fw-bold">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
            </div>
        </div>

    </div>

</body>
</html>

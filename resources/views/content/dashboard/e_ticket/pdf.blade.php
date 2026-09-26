<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 10mm 10mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .report-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .report-header h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .report-header h3 {
            font-size: 9.5pt;
            font-weight: bold;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8pt;
            font-weight: bold;
        }

        .table-troubleshoot {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-bottom: 15px;
        }

        .table-troubleshoot th,
        .table-troubleshoot td {
            border: 1px solid #000000;
            padding: 3px 4px;
            vertical-align: middle;
        }

        .table-troubleshoot th {
            background-color: #99cad6;
            color: #000000;
            text-align: center;
            font-weight: bold;
            line-height: 1.2;
        }

        .check-mark {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            font-weight: bold;
        }

        .signature-table {
            width: 100%;
            margin-top: 20px;
            font-size: 8pt;
            page-break-inside: avoid;
        }

        .signature-space {
            height: 45px;
        }
    </style>
</head>
<body>

    {{-- HEADER JUDUL --}}
    <div class="report-header">
        <h2>CHECKLIST TEMUAN &amp; TINDAKAN TROUBLESHOOT</h2>
        <h3>{{ strtoupper($companyName) }}</h3>
    </div>

    {{-- META DIVISI & BULAN --}}
    <table class="meta-table" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%;">Divisi : {{ $divisionName }}</td>
            <td style="width: 50%; text-align: right;">Bulan : {{ $periodText }}</td>
        </tr>
    </table>

    {{-- TABEL TROUBLESHOOTING --}}
    <table class="table-troubleshoot">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No.</th>
                <th rowspan="2" style="width: 8%;">Tanggal Masuk<br>Troubleshooting</th>
                <th rowspan="2" style="width: 8%;">Tanggal Respont/<br>Tindakan</th>
                <th rowspan="2" style="width: 11%;">Nama Perangkat</th>
                <th rowspan="2" style="width: 8%;">Nama Pengguna</th>
                <th rowspan="2" style="width: 7%;">Divisi</th>
                <th rowspan="2" style="width: 14%;">Temuan dan Trouble</th>
                <th rowspan="2" style="width: 14%;">Tindakan Perbaikan</th>
                <th rowspan="2" style="width: 7%;">Tindakan<br>Pencegahan</th>
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
                    <td colspan="13" class="text-center" style="padding: 10px;">
                        <em>Tidak ada data tiket troubleshooting untuk periode yang dipilih.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <table class="signature-table" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 33%; text-align: center;">
                <div>Dilaporkan Oleh,</div>
                <div style="font-weight: bold; margin-top: 2px;">Staff IT</div>
                <div class="signature-space"></div>
                <div style="border-top: 1px solid #000; width: 160px; margin: 0 auto; padding-top: 2px; font-weight: bold;">
                    ( {{ auth()->user()->name ?? 'Nama Staff' }} )
                </div>
            </td>
            <td style="width: 34%; text-align: center;">
                <div>Mengetahui,</div>
                <div style="font-weight: bold; margin-top: 2px;">Asmen IT</div>
                <div class="signature-space"></div>
                <div style="border-top: 1px solid #000; width: 160px; margin: 0 auto; padding-top: 2px; font-weight: bold;">
                    ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                </div>
            </td>
            <td style="width: 33%; text-align: center;">
                <div>Disetujui Oleh,</div>
                <div style="font-weight: bold; margin-top: 2px;">MR</div>
                <div class="signature-space"></div>
                <div style="border-top: 1px solid #000; width: 160px; margin: 0 auto; padding-top: 2px; font-weight: bold;">
                    ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

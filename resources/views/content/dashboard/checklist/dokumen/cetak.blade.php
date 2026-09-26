<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Perawatan Device - {{ $kodeAset }} (Tahun {{ $tahun }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        @page {
            size: 330mm 215mm; /* Ukuran Kertas F4 / Folio Landscape (330 x 215 mm) */
            margin: 6mm 10mm 6mm 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        .paper-sheet {
            background: #fff;
            width: 330mm;
            min-height: 215mm;
            margin: 15px auto;
            padding: 10mm 14mm;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        /* TABLE FORMATTING IDENTICAL TO USER SPREADSHEET */
        table.tbl-form {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 10px;
        }

        table.tbl-form th,
        table.tbl-form td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
            color: #000;
        }

        .text-center { text-align: center !important; }
        .text-start { text-align: left !important; }
        .text-end { text-align: right !important; }
        .fw-bold { font-weight: bold !important; }

        .title-header {
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5px;
            padding: 4px;
            text-transform: uppercase;
        }

        .form-code {
            font-size: 11px;
            font-weight: bold;
            font-style: italic;
            text-align: right;
            margin-top: 6px;
        }

        .check-ok {
            color: #059669;
            font-weight: bold;
            font-size: 12px;
        }

        .check-ng {
            color: #dc2626;
            font-weight: bold;
            font-size: 12px;
        }

        .check-date {
            color: #0f172a;
            font-weight: 700;
            font-size: 8.5px;
            display: inline-block;
            line-height: 1;
            white-space: nowrap;
            letter-spacing: -0.3px;
        }

        .header-identitas td {
            font-size: 10.5px;
        }

        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            line-height: 11px;
            font-size: 10px;
            margin-right: 3px;
            vertical-align: middle;
        }

        /* PRINT STYLES */
        @media print {
            @page {
                size: 330mm 215mm;
                margin: 6mm 10mm 6mm 10mm;
            }
            .no-print {
                display: none !important;
            }
            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }
            .paper-sheet {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 330mm !important;
                min-height: auto !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    {{-- TOOLBAR CONTROL AT THE TOP (NO PRINT) --}}
    <div class="no-print bg-dark text-white p-3 mb-3 shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('history.perjalanan.show', $unit->id) }}" class="btn btn-outline-light btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Kembali ke Riwayat
                </a>
                <span class="fw-bold fs-6">
                    <i class="bx bx-file me-1 text-warning"></i> Dokumen Perawatan Device: {{ $kodeAset }}
                </span>
                <span class="badge bg-secondary">Tahun {{ $tahun }}</span>
                <span class="badge bg-info text-dark" title="Ukuran kertas cetak 330 x 215 mm">
                    <i class="bx bx-printer me-1"></i> Kertas: F4 / Folio Landscape
                </span>
            </div>

            <form method="GET" action="{{ route('history.perjalanan.dokumen_perawatan.cetak', $unit->id) }}" class="d-flex align-items-center gap-2 flex-wrap">
                {{-- PILIH TAHUN --}}
                <div class="d-flex align-items-center gap-1">
                    <label class="small text-white-50">Tahun:</label>
                    <select name="tahun" class="form-select form-select-sm" style="width: 95px;" onchange="this.form.submit()">
                        @for ($y = date('Y') + 1; $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- TAMPILKAN HALAMAN --}}
                <div class="d-flex align-items-center gap-1">
                    <label class="small text-white-50">Halaman:</label>
                    <select name="page" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                        <option value="all" {{ $page == 'all' ? 'selected' : '' }}>Semua (1 & 2)</option>
                        <option value="f001" {{ $page == 'f001' ? 'selected' : '' }}>Hal 1 (F-IT-001/00)</option>
                        <option value="f002" {{ $page == 'f002' ? 'selected' : '' }}>Hal 2 (F-IT-002/00)</option>
                    </select>
                </div>

                {{-- MODE TERISI / BLANK --}}
                <div class="form-check form-switch ms-2 me-2">
                    <input class="form-check-input" type="checkbox" name="blank" value="1" id="switchBlank" {{ $isBlank ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label small text-white" for="switchBlank">Form Kosong</label>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('history.perjalanan.dokumen_perawatan.excel', array_merge(['id' => $unit->id], request()->query())) }}" class="btn btn-success btn-sm">
                        <i class="bx bxs-file-export me-1"></i> Download Excel
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-primary btn-sm">
                        <i class="bx bx-printer me-1"></i> Cetak / Simpan PDF
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- HALAMAN 1 / SHEET 1: F-IT-001/00 - CHECKLIST PERAWATAN DEVICE            --}}
    {{-- ========================================================================= --}}
    @if ($page == 'all' || $page == 'f001')
        <div class="paper-sheet {{ $page == 'all' ? 'page-break' : '' }}">
            <table class="tbl-form">
                {{-- TITLE --}}
                <tr>
                    <td colspan="49" class="title-header">
                        CHECKLIST PERAWATAN DEVICE
                    </td>
                </tr>

                {{-- HEADER IDENTITAS DEVICE --}}
                <tr class="header-identitas">
                    <td style="width: 250px;" class="fw-bold">
                        Kategori Device : &nbsp;
                        <span class="checkbox-box">{!! $isLaptop ? '&#10003;' : '&nbsp;' !!}</span> Laptop
                    </td>
                    <td colspan="16" class="fw-bold">
                        Nama Device : &nbsp; <span class="fw-normal">{{ $namaDevice }} ({{ $kodeAset }})</span>
                    </td>
                    <td colspan="16" class="fw-bold">
                        Nama Pengguna : &nbsp; <span class="fw-normal">{{ $namaPengguna }}</span>
                    </td>
                    <td colspan="16" class="fw-bold">
                        Divisi : &nbsp; <span class="fw-normal">{{ $divisi }}</span>
                    </td>
                </tr>
                <tr class="header-identitas">
                    <td class="fw-bold">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="checkbox-box">{!! $isPrinter ? '&#10003;' : '&nbsp;' !!}</span> Printer
                    </td>
                    <td colspan="48" class="text-center fw-bold text-uppercase" style="background-color: #f8fafc; letter-spacing: 1px;">
                        WAKTU PERAWATAN (TAHUN {{ $tahun }})
                    </td>
                </tr>
                <tr class="header-identitas">
                    <td class="fw-bold">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="checkbox-box">{!! $isHpTablet ? '&#10003;' : '&nbsp;' !!}</span> HP/Tablet
                        @if ($kategoriLain)
                            &nbsp;<span class="checkbox-box">&#10003;</span> <span class="small">{{ $kategoriLain }}</span>
                        @endif
                    </td>
                    @for ($b = 1; $b <= 12; $b++)
                        <td colspan="4" class="text-center fw-bold" style="font-size: 8.5px; background-color: #f1f5f9;">
                            {{ $bulanNames[$b] }}
                        </td>
                    @endfor
                </tr>

                {{-- SUB-HEADER MINGGU (1, 2, 3, 4) --}}
                <tr class="text-center fw-bold" style="background-color: #e2e8f0; font-size: 8.5px;">
                    <td class="text-start fw-bold" style="font-size: 9.5px;">
                        JENIS PERAWATAN:
                    </td>
                    @for ($b = 1; $b <= 12; $b++)
                        <td style="width: 14px;">1</td>
                        <td style="width: 14px;">2</td>
                        <td style="width: 14px;">3</td>
                        <td style="width: 14px;">4</td>
                    @endfor
                </tr>

                {{-- DAFTAR JENIS PERAWATAN (ROWS) --}}
                @php
                    $displayRows = max(10, count($jenisPerawatanList));
                @endphp
                @for ($i = 0; $i < $displayRows; $i++)
                    @php
                        $itemText = $jenisPerawatanList[$i] ?? null;
                    @endphp
                    <tr>
                        <td style="font-size: 9px; padding: 4px 6px;">
                            @if ($itemText)
                                {{ $i + 1 }}. {{ $itemText }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        @for ($b = 1; $b <= 12; $b++)
                            @for ($m = 1; $m <= 4; $m++)
                                <td class="text-center" style="padding: 1px 0; font-size: 10px; white-space: nowrap;">
                                    @if ($itemText && !$isBlank && !empty($matrix[$itemText][$b][$m]))
                                        @if ($matrix[$itemText][$b][$m] === '✔')
                                            <span class="check-ok">&#10003;</span>
                                        @elseif ($matrix[$itemText][$b][$m] === '✖')
                                            <span class="check-ng">&#10007;</span>
                                        @else
                                            <span class="check-date">{{ $matrix[$itemText][$b][$m] }}</span>
                                        @endif
                                    @else
                                        &nbsp;
                                    @endif
                                </td>
                            @endfor
                        @endfor
                    </tr>
                @endfor
            </table>

            {{-- KODE DOKUMEN ISO POJOK KANAN BAWAH --}}
            <div class="form-code">
                F-IT-001/00
            </div>
        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- HALAMAN 2 / SHEET 2: F-IT-002/00 - KARTU HISTORY DEVICE                  --}}
    {{-- ========================================================================= --}}
    @if ($page == 'all' || $page == 'f002')
        <div class="paper-sheet">
            <table class="tbl-form">
                {{-- TITLE --}}
                <tr>
                    <td colspan="8" class="title-header">
                        KARTU HISTORY DEVICE
                    </td>
                </tr>

                {{-- HEADER IDENTITAS DEVICE --}}
                <tr class="header-identitas">
                    <td style="width: 140px;" class="fw-bold">Kategori Device</td>
                    <td colspan="7">
                        : &nbsp;
                        <span class="checkbox-box">{!! $isLaptop ? '&#10003;' : '&nbsp;' !!}</span> Laptop
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="checkbox-box">{!! $isPrinter ? '&#10003;' : '&nbsp;' !!}</span> Printer
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="checkbox-box">{!! $isHpTablet ? '&#10003;' : '&nbsp;' !!}</span> HP/Tablet
                        @if ($kategoriLain)
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="checkbox-box">&#10003;</span> {{ $kategoriLain }}
                        @endif
                    </td>
                </tr>
                <tr class="header-identitas">
                    <td class="fw-bold">Nama Device</td>
                    <td colspan="7">
                        : &nbsp; {{ $namaDevice }} ({{ $kodeAset }})
                    </td>
                </tr>
                <tr class="header-identitas">
                    <td class="fw-bold">Nama Pengguna</td>
                    <td colspan="7">
                        : &nbsp; {{ $namaPengguna }}
                    </td>
                </tr>
                <tr class="header-identitas">
                    <td class="fw-bold">Divisi</td>
                    <td colspan="7">
                        : &nbsp; {{ $divisi }}
                    </td>
                </tr>

                {{-- TABEL KOLOM HISTORY --}}
                <tr class="text-center fw-bold" style="background-color: #f1f5f9;">
                    <th rowspan="2" style="width: 35px;">NO</th>
                    <th rowspan="2" style="width: 90px;">TANGGAL</th>
                    <th rowspan="2">TEMUAN DAN TROUBLESHOOT</th>
                    <th rowspan="2">TINDAKAN PERBAIKAN</th>
                    <th colspan="2" style="width: 70px;">STATUS</th>
                    <th rowspan="2" style="width: 130px;">DIPERIKSA OLEH,</th>
                    <th rowspan="2" style="width: 130px;">MENGETAHUI,</th>
                </tr>
                <tr class="text-center fw-bold" style="background-color: #f1f5f9;">
                    <th style="width: 35px;">OK</th>
                    <th style="width: 35px;">NG</th>
                </tr>

                {{-- BODY ROWS HISTORY --}}
                @foreach ($historyLogs as $idx => $log)
                    <tr>
                        <td class="text-center" style="height: 24px;">
                            {{ $log['tanggal'] ? ($idx + 1) : '' }}
                        </td>
                        <td class="text-center font-monospace">
                            {{ $log['tanggal'] ? $log['tanggal']->format('d-m-Y') : '' }}
                        </td>
                        <td style="padding-left: 6px;">
                            {{ $log['temuan'] }}
                        </td>
                        <td style="padding-left: 6px;">
                            {{ $log['tindakan'] }}
                        </td>
                        <td class="text-center">
                            @if ($log['is_ok'])
                                <span class="check-ok">&#10003;</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($log['is_ng'])
                                <span class="check-ng">&#10007;</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $log['diperiksa_oleh'] }}
                        </td>
                        <td class="text-center">
                            {{-- Dikosongkan khusus untuk Document Control sesuai instruksi user --}}
                            {{ $log['mengetahui'] }}
                        </td>
                    </tr>
                @endforeach
            </table>

            {{-- KODE DOKUMEN ISO POJOK KANAN BAWAH --}}
            <div class="form-code">
                F-IT-002/00
            </div>
        </div>
    @endif

</body>
</html>

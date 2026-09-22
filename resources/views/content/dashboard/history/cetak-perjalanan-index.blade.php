<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Daftar Tracking Device & Perjalanan Aset</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 10px;
        }

        .no-print {
            margin-bottom: 15px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 7px 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-print {
            background: #154b87;
            color: white;
        }

        .btn-close {
            background: #6c757d;
            color: white;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #154b87;
            padding-bottom: 12px;
        }

        .header h2 {
            margin: 0 0 4px 0;
            color: #154b87;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h3 {
            margin: 0 0 6px 0;
            color: #2c3e50;
            font-size: 14px;
            font-weight: bold;
        }

        .header p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }

        .info-filter {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 11px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        thead th {
            background: #154b87;
            color: white;
            border: 1px solid #154b87;
            text-transform: uppercase;
            font-size: 10px;
            padding: 8px 6px;
            letter-spacing: 0.5px;
            text-align: center;
        }

        tbody td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            text-align: center;
            color: white;
        }

        .status-tersedia { background: #28a745; }
        .status-dipakai { background: #007bff; }
        .status-dipinjam { background: #17a2b8; }
        .status-rusak { background: #dc3545; }
        .status-afkir { background: #6c757d; }

        .rekap-box {
            margin-top: 15px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            padding: 10px 14px;
            background: #f1f5f9;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .rekap-item {
            display: inline-flex;
            gap: 5px;
        }

        .signatures {
            margin-top: 35px;
            width: 100%;
            display: table;
            page-break-inside: avoid;
        }

        .sign-col {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .sign-space {
            height: 60px;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">
            Cetak PDF / Print
        </button>
        <button onclick="window.close()" class="btn btn-close">
            Tutup
        </button>
    </div>

    <div class="header">
        <h2>LAPORAN DAFTAR TRACKING DEVICE / PERJALANAN ASET</h2>
        <h3>{{ strtoupper($namaPerusahaan) }}</h3>
        <p>Audit trail & log unit inventaris aset IT</p>
    </div>

    <div class="info-filter">
        <div>
            <strong>Kategori:</strong> {{ $namaKategori }}
            @if(request('search'))
                • <strong>Pencarian:</strong> "{{ request('search') }}"
            @endif
        </div>
        <div>
            <strong>Tanggal Cetak:</strong> {{ date('d-m-Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="35">NO</th>
                <th width="120">KODE ASET</th>
                <th width="110">NO INVENTARIS</th>
                @if ($user->role == 'super_admin')
                    <th>PERUSAHAAN</th>
                @endif
                <th>KATEGORI</th>
                <th>MEREK & TYPE</th>
                <th width="90">STATUS</th>
                <th>PEMAKAI SAAT INI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventarisList as $index => $inv)
                @php
                    $namaPemakai = '-';
                    if ($inv->status == 'DIPAKAI' && $inv->keluarTerakhir) {
                        $maping = $inv->keluarTerakhir->maping;
                        if ($maping && in_array($maping->status, ['aktif', 'servis', 'maintenance'])) {
                            $namaPemakai = $maping->jenis_penerima == 'Perorangan' ? ($maping->karyawan?->nama_karyawan ?? '-') : ($maping->divisi ?? '-');
                        } else {
                            $namaPemakai = $inv->keluarTerakhir->jenis_penerima == 'Perorangan' ? ($inv->keluarTerakhir->karyawan?->nama_karyawan ?? '-') : ($inv->keluarTerakhir->divisi_klr ?? '-');
                        }
                    } elseif ($inv->status == 'DIPINJAM' && $inv->peminjamanTerakhir) {
                        $namaPemakai = $inv->peminjamanTerakhir->karyawan?->nama_karyawan ?? ($inv->peminjamanTerakhir->karyawanTujuan?->nama_karyawan ?? '-');
                    }
                @endphp
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="fw-bold" style="color: #154b87;">{{ $inv->kode_aset ?? '-' }}</td>
                    <td>{{ $inv->no_inventaris ?? '-' }}</td>
                    @if ($user->role == 'super_admin')
                        <td>{{ $inv->perusahaan?->nama_perusahaan ?? '-' }}</td>
                    @endif
                    <td>{{ $inv->dataAset?->kategori?->nama_barang ?? '-' }}</td>
                    <td>
                        <span class="fw-bold">{{ $inv->dataAset?->merek ?? '-' }}</span>
                        @if($inv->dataAset?->type)
                            <span style="color:#666;">- {{ $inv->dataAset->type }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($inv->status == 'TERSEDIA')
                            <span class="badge-status status-tersedia">TERSEDIA</span>
                        @elseif($inv->status == 'DIPAKAI')
                            <span class="badge-status status-dipakai">DIPAKAI</span>
                        @elseif($inv->status == 'DIPINJAM')
                            <span class="badge-status status-dipinjam">DIPINJAM</span>
                        @elseif($inv->status == 'RUSAK')
                            <span class="badge-status status-rusak">RUSAK</span>
                        @elseif($inv->status == 'AFKIR')
                            <span class="badge-status status-afkir">AFKIR</span>
                        @else
                            <span class="badge-status" style="background:#6c757d;">{{ $inv->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if($namaPemakai !== '-')
                            <strong>{{ $namaPemakai }}</strong>
                        @else
                            <span style="color:#888;">Belum dipakai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $user->role == 'super_admin' ? 8 : 7 }}" class="text-center" style="padding: 20px;">
                        Tidak ada data unit aset yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- REKAP RINGKASAN --}}
    <div class="rekap-box">
        <div class="rekap-item"><strong>Total Unit:</strong> {{ $inventarisList->count() }} unit</div>
        <div class="rekap-item">• <strong>Tersedia:</strong> {{ $inventarisList->where('status', 'TERSEDIA')->count() }}</div>
        <div class="rekap-item">• <strong>Dipakai:</strong> {{ $inventarisList->where('status', 'DIPAKAI')->count() }}</div>
        <div class="rekap-item">• <strong>Dipinjam:</strong> {{ $inventarisList->where('status', 'DIPINJAM')->count() }}</div>
        <div class="rekap-item">• <strong>Rusak:</strong> {{ $inventarisList->where('status', 'RUSAK')->count() }}</div>
        <div class="rekap-item">• <strong>Afkir:</strong> {{ $inventarisList->where('status', 'AFKIR')->count() }}</div>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="signatures">
        <div class="sign-col">
            <p>Mengetahui,</p>
            <div class="sign-space"></div>
            <p><strong>( IT Manager / Supervisor )</strong></p>
        </div>
        <div class="sign-col">
            <p>Dicetak Oleh,</p>
            <div class="sign-space"></div>
            <p><strong>( {{ auth()->user()->name }} )</strong></p>
        </div>
    </div>

    <div class="footer">
        Dokumen dicetak otomatis oleh Sistem Monitoring Asset IT pada {{ date('d-m-Y H:i:s') }}
    </div>

</body>
</html>

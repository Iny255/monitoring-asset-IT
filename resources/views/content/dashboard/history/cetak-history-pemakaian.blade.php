<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan History Pemakaian Aset</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .title h2 {
            margin: 0 0 5px 0;
            color: #154b87;
            font-size: 18px;
            text-transform: uppercase;
        }

        .title p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        thead th {
            background: #154b87;
            color: white;
            border: 1px solid #154b87;
            text-transform: uppercase;
            font-size: 10px;
            padding: 8px 6px;
            letter-spacing: .5px;
        }

        tbody td {
            border: 1px solid #bfc7d1;
            padding: 7px 6px;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 10px;
            color: #555;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #154b87; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Cetak PDF / Print
        </button>
    </div>

    <div class="title">
        <h2>Laporan History Pemakaian Aset</h2>
        <p>
            {{ $user->role == 'super_admin' ? 'SEMBILAN GROUP' : strtoupper($user->perusahaan->nama_perusahaan ?? 'PERUSAHAAN') }}
            • Tanggal Cetak: {{ date('d-m-Y H:i') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">NO</th>
                <th width="80">TGL KELUAR</th>
                <th width="120">NO INVENTARIS</th>
                <th>DATA ASET</th>
                <th>PENERIMA / USER ASET</th>
                <th>LOKASI PENEMPATAN</th>
                <th width="120">PETUGAS INPUT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d-m-Y') }}</td>
                    <td class="text-bold">{{ $item->inventaris->no_inventaris ?? '-' }}</td>
                    <td>
                        <strong>{{ strtoupper($item->inventaris->dataAset->kategori->nama_barang ?? '-') }}</strong><br>
                        <small style="color: #666;">
                            {{ strtoupper($item->inventaris->dataAset->merek ?? '-') }}
                            {{ strtoupper($item->inventaris->dataAset->type ?? '-') }}
                        </small>
                    </td>
                    <td>
                        @if ($item->jenis_penerima == 'Perorangan')
                            <strong>{{ strtoupper($item->karyawan->nama_karyawan ?? '-') }}</strong> (Perorangan)
                        @else
                            <strong>{{ strtoupper($item->divisi_klr ?? '-') }}</strong> (Per Divisi)
                        @endif
                    </td>
                    <td>{{ strtoupper($item->lokasi->nama_lokasi ?? '-') }}</td>
                    <td class="text-center">{{ $item->user->name ?? 'Sistem' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        Tidak ada data riwayat pemakaian aset.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem Monitoring Aset pada {{ date('d-m-Y H:i:s') }}
    </div>

</body>
</html>

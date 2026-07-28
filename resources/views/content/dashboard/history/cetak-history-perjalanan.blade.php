<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan History Perjalanan Aset</title>
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

        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            background: #154b87;
            border-radius: 3px;
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

    @php
        $first = $inventaris->first();
    @endphp

    <div class="title">
        <h2>Laporan History Perjalanan Aset</h2>
        <p>
            {{ $first->dataAset->kategori->nama_barang ?? '-' }} • {{ $first->dataAset->merek ?? '-' }} • {{ $first->dataAset->type ?? '-' }}
            • {{ $user->role == 'super_admin' ? 'SEMBILAN GROUP' : strtoupper($user->perusahaan->nama_perusahaan ?? 'PERUSAHAAN') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="75">TANGGAL</th>
                <th width="110">KODE ASET</th>
                <th width="90">AKTIVITAS</th>
                <th>USER BARU</th>
                <th>LOKASI BARU</th>
                <th>HAK AKSES</th>
                <th>KETERANGAN</th>
                <th width="100">PETUGAS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timeline as $item)
                <tr>
                    <td class="text-center">{{ $item['tanggal']->format('d-m-Y') }}</td>
                    <td><strong>{{ $item['kode_aset'] }}</strong></td>
                    <td class="text-center"><span class="badge">{{ $item['aktivitas'] }}</span></td>
                    <td>{{ $item['user_baru'] ?? '-' }}</td>
                    <td>{{ $item['lokasi_baru'] ?? '-' }}</td>
                    <td>{{ $item['hak_akses'] ?? '-' }}</td>
                    <td>{{ $item['keterangan'] }}</td>
                    <td class="text-center">{{ $item['petugas'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        Tidak ada riwayat perjalanan aset.
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

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Laporan History Pencabutan Asset</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            width: 100%;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .title h2 {
            margin: 0;
            font-size: 22px;
            color: #154b87;
        }

        .small {
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        thead th {
            background: #154b87 !important;
            color: #fff !important;
            border: 1px solid #154b87;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }

        tbody td {
            border: 1px solid #999;
            padding: 7px;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background: #f7f9fc;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #555;
        }

        @media print {

            .no-print {
                display: none;
            }

        }
    </style>

</head>

<body>

    <script>
        window.onload = function() {

            window.print();

            window.onafterprint = function() {

                window.close();

            }

        }
    </script>

    <div class="container">

        <div class="title">

            <h2>

                LAPORAN HISTORY PENCABUTAN ASSET

            </h2>

            <div class="small">

                Monitoring Asset System

            </div>

            <div class="small">

                Tanggal Cetak :

                {{ now()->format('d-m-Y H:i') }}

            </div>

        </div>

        <table>

            <thead>

                <tr>

                    <th width="5%">No</th>

                    <th width="10%">Tanggal</th>

                    <th width="12%">Kode Asset</th>

                    <th width="12%">No Inventaris</th>

                    <th width="18%">Nama Asset</th>

                    <th width="15%">User Lama</th>

                    <th width="12%">Lokasi Lama</th>

                    <th width="12%">Lokasi Baru</th>

                    <th width="14%">Petugas</th>

                </tr>

            </thead>

            <tbody>

                @forelse($histories as $i => $item)

                    <tr>

                        <td class="text-center">

                            {{ $i + 1 }}

                        </td>

                        <td class="text-center">

                            {{ \Carbon\Carbon::parse($item->tanggal_pencabutan)->format('d-m-Y') }}

                        </td>

                        <td>

                            {{ $item->kode_aset }}

                        </td>

                        <td>

                            {{ $item->no_inventaris }}

                        </td>

                        <td>

                            {{ $item->nama_aset }}

                        </td>

                        <td>

                            {{ $item->user_lama }}

                        </td>

                        <td>

                            {{ $item->lokasi_lama }}

                        </td>

                        <td>

                            {{ $item->lokasi_baru }}

                        </td>

                        <td>

                            {{ optional($item->creator)->name }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9" class="text-center">

                            Tidak ada data pencabutan.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="footer">

            Dicetak oleh :

            <strong>{{ auth()->user()->name }}</strong>

            <br>

            {{ now()->format('d-m-Y H:i') }}

        </div>

    </div>

</body>

</html>
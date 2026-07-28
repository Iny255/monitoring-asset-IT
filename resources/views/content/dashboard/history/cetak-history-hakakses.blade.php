<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>Laporan Riwayat Hak Akses</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2 {
            margin: 0;
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

            font-size: 11px;

            letter-spacing: .5px;

        }

        tbody td {
            border: 1px solid #bfc7d1;
            padding: 7px;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .small {
            font-size: 11px;
            color: #666;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 11px;
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

        };
    </script>

    <div class="title">

        <h2>LAPORAN RIWAYAT HAK AKSES</h2>

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

                <th width="14%">Tanggal</th>

                <th width="15%">No Inventaris</th>

                <th width="18%">User Asset</th>

                <th width="18%">Hak Akses</th>

                <th width="10%">Jenis</th>

                <th width="10%">Aktivitas</th>

                <th width="10%">Oleh</th>

            </tr>

        </thead>

        <tbody>

            @forelse($histories as $i => $item)
                <tr>

                    <td align="center">

                        {{ $i + 1 }}

                    </td>

                    <td>

                        {{ $item->created_at->format('d-m-Y H:i') }}

                    </td>

                    <td>

                        {{ optional($item->maping->keluar->inventaris)->no_inventaris }}

                    </td>

                    <td>

                        {{ optional($item->maping->keluar->karyawan)->nama_karyawan }}

                    </td>

                    <td>

                        {{ optional($item->access)->nama_akses }}
                        @if(!empty($item->email))
                            <br><small style="color: #154b87;">({{ $item->email }})</small>
                        @endif

                    </td>

                    <td align="center">

                        {{ strtoupper(optional($item->access)->jenis) }}

                    </td>

                    <td align="center">

                        {{ strtoupper($item->aksi) }}

                    </td>

                    <td>

                        {{ optional($item->user)->name }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8" align="center">

                        Tidak ada data.

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

</body>

</html>

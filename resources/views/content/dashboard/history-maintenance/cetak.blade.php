<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Laporan History Service & Maintenance</title>

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

        .text-right {
            text-align: right;
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

                LAPORAN HISTORY SERVICE & MAINTENANCE

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

                    <th width="4%">No</th>

                    <th width="8%">Tanggal</th>

                    @if (auth()->user()->role == 'super_admin')
                        <th width="12%">Perusahaan</th>
                    @endif

                    <th width="13%">Kode Service</th>

                    <th width="10%">Kode Asset</th>

                    <th width="20%">Nama Asset</th>

                    <th width="8%">Jenis</th>

                    <th width="10%">Asal</th>

                    <th width="10%">Status</th>

                    <th width="10%">Vendor</th>

                    <th width="10%">Biaya</th>

                </tr>

            </thead>

            <tbody>

                @forelse($laporan as $i=>$item)
                    <tr>

                        <td class="text-center">

                            {{ $i + 1 }}

                        </td>

                        <td class="text-center">

                            {{ $item->tanggal->format('d-m-Y') }}

                        </td>

                        @if (auth()->user()->role == 'super_admin')
                            <td>

                                {{ $item->inventaris->perusahaan->nama_perusahaan ?? '-' }}

                            </td>
                        @endif

                        <td>

                            {{ $item->kode_service }}

                        </td>

                        <td>

                            {{ $item->inventaris->kode_aset }}

                        </td>

                        <td>

                            {{ $item->inventaris->dataAset->nama_barang }}

                            <br>

                            <small>

                                {{ $item->inventaris->dataAset->merek }}

                                {{ $item->inventaris->dataAset->type }}

                            </small>

                        </td>

                        <td class="text-center">

                            {{ $item->jenis }}

                        </td>

                        <td class="text-center">

                            {{ $item->asal }}

                        </td>

                        <td class="text-center">

                            {{ $item->status }}

                        </td>

                        <td>

                            {{ $item->vendor ?: '-' }}

                        </td>

                        <td class="text-right">

                            {{ number_format($item->biaya, 0, ',', '.') }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="{{ auth()->user()->role == 'super_admin' ? 11 : 10 }}" class="text-center">

                            Tidak ada data History Service & Maintenance.

                        </td>

                    </tr>
                @endforelse

            </tbody>

            <tfoot>

                <tr>

                    <th colspan="{{ auth()->user()->role == 'super_admin' ? 10 : 9 }}" class="text-right">

                        TOTAL DATA

                    </th>

                    <th class="text-center">

                        {{ $laporan->count() }}

                    </th>

                </tr>

            </tfoot>

        </table>

        <div class="footer">

            Dicetak oleh :

            <strong>

                {{ auth()->user()->name }}

            </strong>

            <br>

            {{ now()->format('d-m-Y H:i') }}

        </div>

    </div>

</body>

</html>
